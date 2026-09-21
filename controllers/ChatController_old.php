<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\AiConversation;
use app\models\AiMessage;

class ChatController extends Controller
{

    public function actionIndex($id = null)
    {
        // Vedo solo le MIE conversazioni
        $conversations = AiConversation::findMine()->orderBy(['updated_at' => SORT_DESC])->all();

        $current = null;
        if ($id) {
            // Controllo di sicurezza: se l'ID non è mio, non caricarlo
            $current = AiConversation::find()
                ->where(['id' => $id, 'user_id' => Yii::$app->user->id])
                ->one();
        } 

        return $this->render('index', [
            'conversations' => $conversations,
            'current' => $current ?? new AiConversation(),
        ]);
    }

private function getSemanticContext($userMessage)
    {
        $client = new \yii\httpclient\Client(['transport' => 'yii\httpclient\CurlTransport']);

        try {
            // 1. Genera l'embedding della domanda dell'utente
            $ollamaRes = $client->createRequest()
                ->setMethod('POST')
                ->setUrl('http://127.0.0.1:11434/api/embeddings')
                ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
                ->setData(['model' => 'nomic-embed-text', 'prompt' => $userMessage])
                ->send();

            if (!$ollamaRes->isOk || !isset($ollamaRes->data['embedding'])) return "";
            $vector = $ollamaRes->data['embedding'];

            // 2. Cerca i 3 frammenti più simili in Qdrant
            $qdrantRes = $client->createRequest()
                ->setMethod('POST')
                ->setUrl('http://127.0.0.1:6333/collections/progetto_dashdemo/points/search')
                ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
                ->setData([
                    'vector' => $vector,
                    'limit' => 3,
                    'with_payload' => true
                ])->send();

            if (!$qdrantRes->isOk) return "";

            // 3. Formatta il contesto per l'IA
            $context = "\n[CONTESTO CODICE SORGENTE RECUPERATO]:\n";
            foreach ($qdrantRes->data['result'] as $point) {
                $context .= "--- Frammento da " . ($point['payload']['filename'] ?? 'file') . " ---\n";
                $context .= $point['payload']['text'] . "\n\n";
            }
            return $context;

        } catch (\Exception $e) {
            Yii::error("Errore RAG: " . $e->getMessage());
            return "";
        }
    }



    /**
     * AZIONE PRINCIPALE: Gestisce l'invio del messaggio e la risposta dell'IA
     */
    public function actionSendMessage()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Alziamo il timeout: la CPU ha bisogno di tempo per elaborare 500+ record
        set_time_limit(1200);

        $post = Yii::$app->request->post();
        $message = $post['message'] ?? '';
        $convId = ($post['conversation_id'] === 'null' || empty($post['conversation_id'])) ? null : $post['conversation_id'];

        // Recuperiamo il modello selezionato dalla tendina nell'Index
        $modelSelected = $post['model'] ?? 'dashboard-ai';

        $searchQuery = strtolower(trim($message));
        $externalContext = "";

        // 1. ESTRAZIONE DATI DAL CRM (Solo se usiamo il modello Dashboard)
        if ($modelSelected === 'dashboard-ai') {
            // Estraiamo le parole chiave (es: "auxcoop")
            $keywordParts = $this->extractKeywordsArray($searchQuery);

            if (!empty($keywordParts)) {
                // Interroghiamo il DB6 per attività e progetti
                $externalContext .= $this->getActivitiesContext($keywordParts, $searchQuery);
                $externalContext .= $this->getProjectsContext($keywordParts, $searchQuery);
            }
        }
if ($modelSelected === 'dashboard-ai') {
            $externalContext .= $this->getSemanticContext($message);
        // LOG DI DEBUG
Yii::error("CONTEXT RECUPERATO DA QDRANT: " . substr($semanticContext, 0, 500), 'AI_DEBUG');
        if (!empty($semanticContext)) {
    $externalContext .= $semanticContext;
}
        
            }
        // 2. SALVATAGGIO CONVERSAZIONE E MESSAGGIO UTENTE
        $convId = $this->saveConversation($convId, $message);

        $userMsg = new AiMessage([
            'conversation_id' => $convId,
            'role' => 'user',
            'content' => $message
        ]);
        $userMsg->save(false);

        // 3. PREPARAZIONE PROMPT PER OLLAMA
        $messages = [];

        // SYSTEM PROMPT DINAMICO
        if ($modelSelected === 'dashboard-ai') {
            $messages[] = [
    'role' => 'system',
    'content' => "Sei un esperto sviluppatore PHP e analista del software. 
                  Ti verranno forniti frammenti del codice sorgente reale nel tag [CONTESTO CODICE SORGENTE].
                  
                  REGOLE RIGIDE:
                  1. Rispondi alla domanda usando ESCLUSIVAMENTE le informazioni presenti nel [CONTESTO CODICE SORGENTE] e nei [DATI REALI CRM].
                  2. Se l'utente chiede come funziona una funzione, cerca il nome della funzione nel contesto fornito e spiegala.
                  3. NON inventare codice Python o esempi generici. Se non trovi l'informazione nel codice fornito, dì che non hai accesso a quel file specifico.
                  4. Cita i nomi dei file (es. ChatController.php) se presenti nel contesto."
];
        } else {
            $messages[] = [
                'role' => 'system',
                'content' => "Sei un assistente AI generico. Rispondi in modo utile e cordiale in italiano."
            ];
        }

        // Carichiamo la cronologia (ultimi 4 messaggi per non appesantire troppo)
        $history = AiMessage::find()
            ->where(['conversation_id' => $convId])
            ->orderBy('id ASC')
            ->limit(5)
            ->all();

        foreach ($history as $m) {
            $messages[] = ['role' => $m->role, 'content' => $m->content];
        }

        // 4. INIEZIONE DEL CONTESTO (Solo se siamo in modalità Analista)
        if ($modelSelected === 'dashboard-ai' && !empty($externalContext)) {
            // Appendiamo i dati reali all'ultimo messaggio inviato
            $lastIdx = count($messages) - 1;
            $messages[$lastIdx]['content'] .= "\n\n[DATI REALI CRM]:\n" . $externalContext;
        }

        // 5. CHIAMATA A OLLAMA
        try {
            $logFile = Yii::getAlias('@runtime/logs/ai_chat_custom.log');
            $startTime = microtime(true);

            // Eseguiamo la chat con il modello scelto dall'utente
            $reply = Yii::$app->ollama->chat($modelSelected, $messages);

            $duration = round(microtime(true) - $startTime, 2);

            // Log di controllo per monitorare le performance della CPU
            file_put_contents($logFile, date('Y-m-d H:i:s') . " | Modello: $modelSelected | Tempo: {$duration}s | Context: " . strlen($externalContext) . " chars\n", FILE_APPEND);

            if (!$reply) {
                return ['success' => false, 'error' => "Il modello $modelSelected non ha risposto."];
            }

            // 6. SALVATAGGIO RISPOSTA IA
            $aiMsg = new AiMessage([
                'conversation_id' => $convId,
                'role' => 'assistant',
                'content' => $reply
            ]);
            $aiMsg->save();

            return [
                'success' => true,
                'reply' =>   $modelSelected .'_____'.$reply,
                'conversation_id' => $convId,
                'duration' => $duration
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => "Errore critico IA: " . $e->getMessage()];
        }
    }

    /* =========================================================
     * FUNZIONI PRIVATE DI SUPPORTO
     * ========================================================= */

    private function handleFileUpload()
    {
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $tmpName = $_FILES['attachment']['tmp_name'];
            $type = $_FILES['attachment']['type'];

            if ($type === 'application/pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($tmpName);
                return "\n\n[TESTO DOCUMENTO ALLEGATO]:\n" . $pdf->getText();
            } else {
                return "\n\n[TESTO FILE ALLEGATO]:\n" . file_get_contents($tmpName);
            }
        }
        return "";
    }
    private function extractKeywords($searchQuery)
    {
        // Lista molto più ampia di parole da ignorare (verbi comuni, termini di chat, articoli)
        $stopWords = [
            'quanti',
            'quante',
            'ticket',
            'ha',
            'in',
            'tutto',
            'e',
            'mi',
            'puoi',
            'fare',
            'un',
            'una',
            'uno',
            'riassunto',
            'di',
            'tutti',
            'tutte',
            'i',
            'suoi',
            'dati',
            'azienda',
            'cliente',
            'clienti',
            'ditta',
            'progetto',
            'progetti',
            'interventi',
            'intervento',
            'attività',
            'il',
            'la',
            'lo',
            'gli',
            'le',
            'per',
            'su',
            'con',
            'fammi',
            'delle',
            'sulle',
            'questo',
            'questa',
            'ultimi',
            'ultime',
            'quali',
            'sono',
            'che',
            'del',
            'della',
            'dei',
            'degli',
            'dammi',
            'trova',
            'cerca',
            'vedere',
            'mostrami',
            'storico',
            'ore',
            'da',
            'a',
            // --- VERBI E PAROLE DI CHAT ---
            'abbiamo',
            'avete',
            'hanno',
            'vorrei',
            'sapere',
            'dimmi',
            'elencami',
            'fatto',
            'fatti',
            'stato',
            'stati',
            'aperti',
            'chiusi',
            'totale',
            'totali',
            'quali',
            'qual',
            'quale',
            'ci',
            'ne',
            'registrati',
            'inseriti'
        ];

        // Dividiamo la frase ignorando spazi, virgole, punti e apostrofi
        $parole = preg_split('/[\s,\.\?\'\"]+/', $searchQuery);
        $keywordParts = [];

        foreach ($parole as $p) {
            $p = trim($p);
            // Scartiamo parole corte (<= 2 lettere) e le stopwords
            if (strlen($p) > 2 && !in_array($p, $stopWords)) {
                $keywordParts[] = $p;
            }
        }

        // Unisce i pezzi validi. Es: "aux", "coop" diventa "aux%coop"
        return !empty($keywordParts) ? implode('%', $keywordParts) : '';
    }

    private function getProjectsContext($parolaChiave, $searchQuery)
    {
        // Se non viene richiesta una query sui progetti, ignoriamo
        if (strpos($searchQuery, 'progett') === false && strpos($searchQuery, 'ore') === false && strpos($searchQuery, 'riassunto') === false) {
            return "";
        }

        Yii::warning("RICERCA PROGETTI AVVIATA CON CHIAVE: '$parolaChiave'", 'AI_CHAT');

        $query = "SELECT soggetto, dataevento, oredelta, codiceprogetto, xtipologia FROM x_vistaprog";
        $params = [];

        if (!empty($parolaChiave)) {
            // Aggiunti wildcards % per cercare frammenti di nome
            $query .= " WHERE soggetto LIKE :chiave OR codiceprogetto LIKE :chiave LIMIT 20";
            $params[':chiave'] = "%" . $parolaChiave . "%";
        } else {
            $query .= " LIMIT 5";
        }

        try {
            $dati = Yii::$app->db6->createCommand($query, $params)->queryAll();
            Yii::warning("PROGETTI TROVATI: " . count($dati), 'AI_CHAT');

            if (!empty($dati)) {
                $oreProgetto = 0;
                foreach ($dati as $p) {
                    $oreProgetto += (float)$p['oredelta'];
                }
                $riassunto = [
                    'totale_ore_progetti' => $oreProgetto,
                    'numero_voci_progetto_trovate' => count($dati)
                ];
                return "\n[RIASSUNTO NUMERICO PROGETTI (DA PHP)]:\n" . json_encode($riassunto) .
                    "\n[DETTAGLIO PROGETTI DA CRM]:\n" . json_encode($dati) . "\n";
            }
        } catch (\Exception $e) {
            Yii::warning("ERRORE QUERY PROGETTI: " . $e->getMessage(), 'AI_CHAT');
            return "\n[ERRORE LETTURA PROGETTI]: " . $e->getMessage() . "\n";
        }
        return "";
    }

    private function getActivitiesContext($parolaChiave, $searchQuery)
    {
        if (strpos($searchQuery, 'attività') === false && strpos($searchQuery, 'ticket') === false && strpos($searchQuery, 'intervent') === false && strpos($searchQuery, 'riassunto') === false) {
            return "";
        }

        Yii::warning("RICERCA ATTIVITÀ AVVIATA CON CHIAVE: '$parolaChiave'", 'AI_CHAT');

        $query = "SELECT soggetto, dataevento, TIPOEVENTO, oredelta, codicestatoevento, oggetto, descrizioneprogetto FROM xestrazione";
        $params = [];

        if (!empty($parolaChiave)) {
            $query .= " WHERE soggetto LIKE :chiave ORDER BY dataevento DESC LIMIT 50";
            $params[':chiave'] = "%" . $parolaChiave . "%";
        } else {
            $query .= " ORDER BY dataevento DESC LIMIT 10";
        }

        try {
            $dati = Yii::$app->db6->createCommand($query, $params)->queryAll();
            Yii::warning("ATTIVITÀ TROVATE: " . count($dati), 'AI_CHAT');

            if (!empty($dati)) {
                $chiusi = 0;
                $aperti = 0;
                $ore = 0;

                foreach ($dati as $row) {
                    if (strtolower($row['codicestatoevento']) === 'closed') $chiusi++;
                    else $aperti++;
                    $ore += (float)$row['oredelta'];
                }

                $riassunto = [
                    'soggetto_trovato' => $dati[0]['soggetto'],
                    'totale_interventi_estratti' => count($dati),
                    'ticket_chiusi' => $chiusi,
                    'ticket_in_corso_o_aperti' => $aperti,
                    'ore_totali_lavorate' => $ore
                ];
                Yii::warning("RIASSUNTO ATTIVITÀ GENERATO: " . json_encode($riassunto), 'AI_CHAT');

                $ultimeAttivita = array_slice($dati, 0, 15);

                return "\n[RIASSUNTO NUMERICO ATTIVITÀ CALCOLATO DA PHP]:\n" . json_encode($riassunto) .
                    "\n[DETTAGLIO RECENTI ATTIVITÀ DA CRM]:\n" . json_encode($ultimeAttivita) . "\n";
            } else {
                return "\n[DATI CRM]: Nessuna attività trovata per i criteri indicati.\n";
            }
        } catch (\Exception $e) {
            Yii::warning("ERRORE QUERY ATTIVITÀ: " . $e->getMessage(), 'AI_CHAT');
            return "\n[ERRORE LETTURA ATTIVITÀ]: " . $e->getMessage() . "\n";
        }
    }

    private function saveConversation($convId, $message)
    {
        if ($convId === null) {
            $conv = new AiConversation();
            $conv->title = substr($message, 0, 50);
            $conv->user_id = Yii::$app->user->id;
            $conv->save();
            return $conv->id;
        } else {
            $conv = AiConversation::findOne(['id' => (int)$convId, 'user_id' => Yii::$app->user->id]);
            if ($conv) {
                $conv->updated_at = new \yii\db\Expression('GETDATE()');
                $conv->save();
            }
            return $convId;
        }
    }






    public function _____actionSendMessage()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        set_time_limit(300); // 5 minuti di timeout per richieste pesanti

        $post = Yii::$app->request->post();
        $message = $post['message'] ?? '';
        $convId = $post['conversation_id'];

        if ($convId === 'null' || empty($convId)) {
            $convId = null;
        }

        // ==========================================
        // 1. GESTIONE FILE ALLEGATO (PDF o Testo)
        // ==========================================
        $fileContent = "";
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $tmpName = $_FILES['attachment']['tmp_name'];
            $type = $_FILES['attachment']['type'];

            if ($type === 'application/pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($tmpName);
                $fileContent = "\n\n[TESTO ESTRATTO DAL DOCUMENTO ALLEGATO]:\n" . $pdf->getText();
            } else {
                $fileContent = "\n\n[TESTO ESTRATTO DAL FILE ALLEGATO]:\n" . file_get_contents($tmpName);
            }
        }

        // ==========================================
        // 2. RECUPERO CONTESTO ESTERNO (VISTE VTIGER SU DB6)
        // ==========================================
        $externalContext = "";
        $searchQuery = strtolower($message);

        // A. Puliamo la stringa per trovare il VERO nome dell'azienda/progetto
        $stopPhrases = [
            'quanti ticket ha',
            'mi puoi fare un riassunto',
            'di tutti i suoi dati',
            'azienda',
            'cliente',
            'progetto',
            'interventi',
            'attività',
            'ticket',
            'fammi',
            'un',
            'il',
            'la',
            'i',
            'gli',
            'le',
            'di',
            'per',
            'su',
            'con'
        ];

        // Rimuoviamo le parole di interazione tipiche, lasciando (si spera) solo il nome
        $cleanSearch = trim(str_ireplace($stopPhrases, '', $searchQuery));
        $parolaChiave = $cleanSearch; // Es. "aux coop"

        // B. Ricerca nella vista PROGETTI (x_vistaprog)
        if (strpos($searchQuery, 'progett') !== false || strpos($searchQuery, 'ore') !== false) {
            $queryProgetti = "SELECT soggetto, dataevento, oredelta, codiceprogetto, xtipologia FROM x_vistaprog";
            $params = [];

            if (!empty($parolaChiave) && strlen($parolaChiave) > 2) {
                $queryProgetti .= " WHERE soggetto LIKE :chiave OR codiceprogetto LIKE :chiave LIMIT 20";
                $params[':chiave'] = "%$parolaChiave%";
            } else {
                $queryProgetti .= " LIMIT 5";
            }

            try {
                $datiProgetti = Yii::$app->db6->createCommand($queryProgetti, $params)->queryAll();
                if (!empty($datiProgetti)) {
                    $oreProgetto = 0;
                    foreach ($datiProgetti as $p) {
                        $oreProgetto += (float)$p['oredelta'];
                    }
                    $riassuntoProg = [
                        'totale_ore_progetti_estratti' => $oreProgetto,
                        'numero_voci_trovate' => count($datiProgetti)
                    ];
                    $externalContext .= "\n[RIASSUNTO NUMERICO PROGETTI (DA PHP)]:\n" . json_encode($riassuntoProg) . "\n";
                    $externalContext .= "\n[DETTAGLIO PROGETTI DA CRM]:\n" . json_encode($datiProgetti) . "\n";
                }
            } catch (\Exception $e) {
                $externalContext .= "\n[ERRORE LETTURA PROGETTI]: " . $e->getMessage() . "\n";
            }
        }

        // C. Ricerca nella vista ESTRAZIONE ATTIVITÀ (xestrazione)
        if (strpos($searchQuery, 'attività') !== false || strpos($searchQuery, 'ticket') !== false || strpos($searchQuery, 'intervent') !== false) {

            // Limitiamo le colonne per non saturare la memoria dell'IA
            $queryAttivita = "SELECT soggetto, dataevento, TIPOEVENTO, oredelta, codicestatoevento, oggetto, descrizioneprogetto FROM xestrazione";
            $params = [];

            if (!empty($parolaChiave) && strlen($parolaChiave) > 2) {
                $queryAttivita .= " WHERE soggetto LIKE :chiave ORDER BY dataevento DESC LIMIT 50";
                $params[':chiave'] = "%$parolaChiave%";
            } else {
                $queryAttivita .= " ORDER BY dataevento DESC LIMIT 10";
            }

            try {
                $datiAttivita = Yii::$app->db6->createCommand($queryAttivita, $params)->queryAll();

                if (!empty($datiAttivita)) {
                    // Pre-calcoliamo i totali per evitare che l'IA sbagli i conti
                    $ticketChiusi = 0;
                    $ticketAperti = 0;
                    $oreTotali = 0;

                    foreach ($datiAttivita as $row) {
                        if (strtolower($row['codicestatoevento']) === 'closed') {
                            $ticketChiusi++;
                        } else {
                            $ticketAperti++;
                        }
                        $oreTotali += (float)$row['oredelta'];
                    }

                    $riassuntoMatematico = [
                        'soggetto_trovato' => $datiAttivita[0]['soggetto'],
                        'totale_interventi_estratti' => count($datiAttivita),
                        'ticket_chiusi' => $ticketChiusi,
                        'ticket_in_corso_o_aperti' => $ticketAperti,
                        'ore_totali_lavorate' => $oreTotali
                    ];

                    $externalContext .= "\n[RIASSUNTO NUMERICO CALCOLATO DA PHP (USALO PER I TOTALI)]:\n" . json_encode($riassuntoMatematico) . "\n";

                    // Passiamo all'IA solo gli ultimi 15 per darle contesto sugli oggetti, senza riempire il prompt
                    $ultimeAttivita = array_slice($datiAttivita, 0, 15);
                    $externalContext .= "\n[DETTAGLIO RECENTI ATTIVITÀ DA CRM]:\n" . json_encode($ultimeAttivita) . "\n";
                } else {
                    $externalContext .= "\n[DATI CRM]: Nessuna attività trovata per '$parolaChiave'.\n";
                }
            } catch (\Exception $e) {
                $externalContext .= "\n[ERRORE LETTURA ATTIVITÀ]: " . $e->getMessage() . "\n";
            }
        }

        // ==========================================
        // 3. GESTIONE DELLA CONVERSAZIONE (Salvataggio DB)
        // ==========================================
        if ($convId === null) {
            $conv = new AiConversation();
            $conv->title = substr($message, 0, 50);
            $conv->user_id = Yii::$app->user->id;
            $conv->save();
            $convId = $conv->id;
        } else {
            if (!is_numeric($convId)) {
                return ['success' => false, 'error' => 'ID Conversazione non valido.'];
            }
            $conv = AiConversation::findOne(['id' => (int)$convId, 'user_id' => Yii::$app->user->id]);
            if (!$conv) throw new \yii\web\ForbiddenHttpException("Non autorizzato.");

            $conv->updated_at = new \yii\db\Expression('GETDATE()');
            $conv->save();
        }

        // Salviamo il messaggio dell'utente (includendo il file estratto, se presente)
        $fullContentUser = $message . $fileContent;
        $userMsg = new AiMessage([
            'conversation_id' => $convId,
            'role' => 'user',
            'content' => $fullContentUser
        ]);
        $userMsg->save(false);

        // ==========================================
        // 4. PREPARAZIONE STORICO E PROMPT PER OLLAMA
        // ==========================================
        $history = AiMessage::find()->where(['conversation_id' => $convId])->orderBy('created_at ASC')->all();
        $messages = [];

        // System Prompt: Istruzioni rigide per Llama
        $messages[] = [
            'role' => 'system',
            'content' => "Sei l'assistente gestionale esperto dell'azienda.
                          Ti verranno forniti dei dati dal CRM Vtiger.
                          REGOLE FONDAMENTALI:
                          1. NON calcolare somme o conteggi da solo. Usa SOLO i numeri forniti nel tag [RIASSUNTO NUMERICO CALCOLATO DA PHP].
                          2. Elenca le attività più recenti citando l'oggetto e lo stato basandoti sul tag [DETTAGLIO RECENTI ATTIVITÀ].
                          3. Scrivi in italiano naturale e professionale, NON mostrare mai all'utente le stringhe JSON.
                          4. Se i dati dicono 'Nessuna attività', dillo chiaramente senza inventare nulla."
        ];

        foreach ($history as $m) {
            $messages[] = ['role' => $m->role, 'content' => $m->content];
        }

        // Iniettiamo il contesto (Vtiger + File) solo nell'ultima richiesta per il RAG
        if (!empty($externalContext)) {
            $messages[count($messages) - 1]['content'] .= "\n\n--- DATI DI SISTEMA ---\n" . $externalContext;
        }

        // ==========================================
        // 5. CHIAMATA AL SERVER OLLAMA
        // ==========================================
        try {
            $reply = Yii::$app->ollama->chat('llama3.2', $messages);

            if ($reply === null || $reply === false) {
                return [
                    'success' => false,
                    'error' => "Il server IA ha chiuso la connessione. Prova con una richiesta più specifica."
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Timeout o Errore IA: " . $e->getMessage()
            ];
        }

        // ==========================================
        // 6. SALVATAGGIO RISPOSTA
        // ==========================================
        $aiMsg = new AiMessage([
            'conversation_id' => $convId,
            'role' => 'assistant',
            'content' => $reply
        ]);
        $aiMsg->save();

        return [
            'success' => true,
            'reply' => $reply,
            'conversation_id' => $convId
        ];
    }
    public function actionSendMessage__preview()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        set_time_limit(300);
        $post = Yii::$app->request->post();
        $message = $post['message'] ?? '';
        $convId = $post['conversation_id'] === 'null' ? null : $post['conversation_id'];

        // 1. GESTIONE FILE (PDF/TEXT)
        $fileContent = "";
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $tmpName = $_FILES['attachment']['tmp_name'];
            $type = $_FILES['attachment']['type'];
            if ($type === 'application/pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($tmpName);
                $fileContent = "\n[ALLEGATO]:\n" . $pdf->getText();
            } else {
                $fileContent = "\n[ALLEGATO]:\n" . file_get_contents($tmpName);
            }
        }

        // 2. RECUPERO CONTESTO ESTERNO (VTIGER db6 + KNOWLEDGE BASE)
        $externalContext = "";
        $searchQuery = strtolower($message);

        // A. Cerca nelle FAQ (Vtiger db6)
        if (strpos($searchQuery, 'come') !== false || strpos($searchQuery, 'faq') !== false) {
            $faq = Yii::$app->db6->createCommand("SELECT question, answer FROM vtiger_faq WHERE question LIKE :q LIMIT 1", [':q' => "%$message%"])->queryOne();
            if ($faq) $externalContext .= "\n[FAQ VTIGER]: Q: {$faq['question']} A: {$faq['answer']}\n";
        }

        // B. Cerca Ticket Aperti (Vtiger db6)
        if (strpos($searchQuery, 'ticket') !== false || strpos($searchQuery, 'assistenza') !== false) {
            $tickets = Yii::$app->db6->createCommand("SELECT title, status FROM vtiger_troubletickets WHERE status != 'Closed' LIMIT 5")->queryAll();
            if ($tickets) $externalContext .= "\n[TICKET APERTI]: " . json_encode($tickets) . "\n";
        }

        // C. Cerca Progetti (Vtiger db6)
        if (strpos($searchQuery, 'progett') !== false) {
            $projects = Yii::$app->db6->createCommand("SELECT projectname, projectstatus FROM vtiger_project WHERE projectstatus != 'completed'")->queryAll();
            if ($projects) $externalContext .= "\n[PROGETTI ATTIVI]: " . json_encode($projects) . "\n";
        }

        // D. Knowledge Base (Tabella Manuali)
        // Cerchiamo se ci sono istruzioni nei manuali caricati
        $kb = \app\models\Aiknowledge::find()->where(['like', 'content', $message])->limit(2)->all();
        foreach ($kb as $item) {
            $externalContext .= "\n[MANUALE - {$item->software_name}]: {$item->content}\n";
        }

        // 3. GESTIONE CONVERSAZIONE
        if ($convId === null) {
            $conv = new AiConversation(['title' => substr($message, 0, 50), 'user_id' => Yii::$app->user->id]);
            $conv->save();
            $convId = $conv->id;
        } else {
            $conv = AiConversation::findOne(['id' => $convId, 'user_id' => Yii::$app->user->id]);
            if (!$conv) return ['success' => false, 'error' => 'Non autorizzato'];
            $conv->updated_at = new \yii\db\Expression('GETDATE()');
            $conv->save();
        }

        // 4. SALVATAGGIO MESSAGGIO UTENTE
        // Salviamo il messaggio pulito nel DB, ma invieremo a Ollama il messaggio + contesto
        $userMsg = new AiMessage(['conversation_id' => $convId, 'role' => 'user', 'content' => $message . ($fileContent ? " (Allegato presente)" : "")]);
        $userMsg->save(false);

        // 5. PREPARAZIONE MESSAGGI PER OLLAMA
        $history = AiMessage::find()->where(['conversation_id' => $convId])->orderBy('created_at ASC')->all();
        $messages = [[
            'role' => 'system',
            'content' => "Sei un analista dati aziendale di alto livello. 
                          Il sistema ti fornirà dei dati estratti dal database CRM aziendale nei tag [DATI PROGETTI DA VISTA x_vistaprog] e [DATI ATTIVITÀ DA VISTA xestrazione] in formato JSON.
                          Il tuo compito è analizzare questi dati grezzi e rispondere alla domanda dell'utente in italiano chiaro, naturale e discorsivo.
                          NON mostrare all'utente il formato JSON grezzo. Riassumi i concetti: es. 'Per il cliente X ci sono stati Y interventi per un totale di Z ore'.
                          Se i dati forniti non rispondono alla domanda, dillo chiaramente."
        ]];

        foreach ($history as $m) {
            $messages[] = ['role' => $m->role, 'content' => $m->content];
        }

        // Inseriamo il contesto estratto (file + DB esterni) solo nell'ultimo messaggio per non appesantire la cronologia
        $messages[count($messages) - 1]['content'] .= $fileContent . $externalContext;

        // 6. CHIAMATA OLLAMA
        try {
            $reply = Yii::$app->ollama->chat('llama3.2', $messages);
            if (!$reply) throw new \Exception("Nessuna risposta dal server IA.");

            $aiMsg = new AiMessage(['conversation_id' => $convId, 'role' => 'assistant', 'content' => $reply]);
            $aiMsg->save();

            return ['success' => true, 'reply' => $reply, 'conversation_id' => $convId];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    public function actionSendMessage_old()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        // 1. Diamo tempo infinito (o quasi) a PHP per questa richiesta
        set_time_limit(300); // 5 minuti
        $post = Yii::$app->request->post();

        $convId = $post['conversation_id'];
        $convId = $post['conversation_id'];
        if ($convId === 'null' || empty($convId)) {
            $convId = null;
        }
        // Gestione File Caricato
        $fileContent = "";
        // ... (parte iniziale del file upload) ...
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $tmpName = $_FILES['attachment']['tmp_name'];
            $type = $_FILES['attachment']['type'];

            if ($type === 'application/pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($tmpName);
                $fileContent = "\n[TESTO ESTRATTO DAL DOCUMENTO]:\n" . $pdf->getText();
            } else {
                $fileContent = "\n[TESTO ESTRATTO DAL FILE]:\n" . file_get_contents($tmpName);
            }
        }

        if ($convId === null) {
            // Nuova conversazione
            $conv = new AiConversation();
            $conv->title = substr($post['message'], 0, 50);
            $conv->user_id = Yii::$app->user->id;
            $conv->save();
            $convId = $conv->id;
        } else {
            // Verifica che l'ID sia un numero prima di interrogare il DB
            if (!is_numeric($convId)) {
                return ['success' => false, 'error' => 'ID Conversazione non valido.'];
            }

            $conv = AiConversation::findOne(['id' => (int)$convId, 'user_id' => Yii::$app->user->id]);
            if (!$conv) throw new \yii\web\ForbiddenHttpException("Non autorizzato.");

            $conv->updated_at = new \yii\db\Expression('GETDATE()');
            $conv->save();
        }

        // 2. Salva messaggio User
        $fullContent = $post['message']. $fileContent;
        $userMsg = new AiMessage([
            'conversation_id' => $convId,
            'role' => 'user',
            'content' => $fullContent
        ]);
        $userMsg->save(false);

        // 3. Prepara Cronologia per Ollama
        $history = AiMessage::find()->where(['conversation_id' => $convId])->orderBy('created_at ASC')->all();
        $messages = [];
        $messages[] = [
            'role' => 'system',
            'content' => 'Sei un assistente aziendale preciso e analitico. 
                  NON inventare fatti se non sei sicuro. 
                  Se ti viene fornito un documento, basati esclusivamente sul testo del documento. 
                  Usa un tono professionale e rispondi in italiano.'
        ];
        foreach ($history as $m) {
            $messages[] = ['role' => $m->role, 'content' => $m->content];
        }
        // Se c'è un file, aggiungiamo il suo contenuto all'ULTIMO messaggio per dare contesto
        if (!empty($fileContent)) {
            $messages[count($messages) - 1]['content'] .= $fileContent;
        }
      //  $reply = Yii::$app->ollama->chat('llama3.2', $messages);
        // 4. Chiamata a Ollama con gestione Timeout estesa
        try {
            // Se nel tuo componente Ollama.php usi yii\httpclient\Client, 
            // assicurati che il timeout lì sia alto. 
            // Altrimenti, passiamo il nome esatto del modello che abbiamo visto nel tuo curl:
            $reply = Yii::$app->ollama->chat('llama3.2', $messages);

            if ($reply === null || $reply === false) {
                return [
                    'success' => false,
                    'error' => "Il server IA ha chiuso la connessione o il modello è troppo pesante."
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Timeout o Errore: " . $e->getMessage()
            ];
        }
        // 5. Salva risposta Assistant
        $aiMsg = new AiMessage([
            'conversation_id' => $convId,
            'role' => 'assistant',
            'content' => $reply
        ]);
        $aiMsg->save();

        return [
            'success' => true,
            'reply' => $reply,
            'conversation_id' => $convId
        ];
    }
    public function actionCheckStatus_____()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $client = new \yii\httpclient\Client();
            $url = 'http://127.0.0.1:11434/api/tags';
            // Proviamo a contattare Ollama sulla porta standard
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl($url)
                ->setTimeout(3)
                ->send();

            return ['online' => $response->isOk, 'details' => 'Connesso'];
        } catch (\Exception $e) {
            return ['online' => false, 'details' => $e->getMessage()];
        }
    }
    public function actionCheckStatus()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Proviamo a connetterci via socket (è il modo più veloce per vedere se la porta è aperta)
        $connection = @fsockopen('127.0.0.1', 11434, $errno, $errstr, 2);

        if (is_resource($connection)) {
            fclose($connection);
            return ['online' => true];
        } else {
            return ['online' => false, 'error' => $errstr];
        }
    }
}
