<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\AiConversation;
use app\models\AiMessage;

class ChatController extends Controller
{
    /**
     * Recupera la lista dei modelli installati localmente su Ollama
     */
    public function actionGetModels()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $ch = curl_init("http://localhost:11434/api/tags");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);

            $models = [];
            if (isset($data['models'])) {
                foreach ($data['models'] as $m) {
                    $name = strtolower($m['name']);
                    $family = $m['details']['family'] ?? '';
                    $parameterSize = $m['details']['parameter_size'] ?? 'N/D';

                    // LOGICA DI AUTO-DESCRIZIONE
                    if (str_contains($name, 'dashboard')) {
                        $desc = "Agente Personalizzato: Ottimizzato per Vtiger e analisi dati aziendali.";
                        $icon = "fa-database";
                    } elseif (str_contains($name, 'coder') || str_contains($name, 'code')) {
                        $desc = "Specialista Coding ($family): Ottimizzato per programmazione e debug.";
                        $icon = "fa-code";
                    } elseif (str_contains($name, 'math')) {
                        $desc = "Specialista Matematico: Ottimo per calcoli complessi e logica.";
                        $icon = "fa-calculator";
                    } elseif (str_contains($parameterSize, 'b') && (int)$parameterSize <= 3) {
                        $desc = "Modello Ultra-Leggero ($parameterSize): Risposte istantanee, basso carico CPU.";
                        $icon = "fa-bolt";
                    } elseif (str_contains($parameterSize, 'b') && (int)$parameterSize >= 7) {
                        $desc = "Modello Avanzato ($parameterSize): Grande capacità di ragionamento, più lento su CPU.";
                        $icon = "fa-brain";
                    } else {
                        $desc = "Modello Generico basato su $family. Bilanciato per conversazione.";
                        $icon = "fa-comment-dots";
                    }

                    $models[] = [
                        'id' => $m['name'],
                        'name' => strtoupper(str_replace(':latest', '', $m['name'])),
                        'desc' => $desc,
                        'icon' => $icon,
                        'size' => round($m['size'] / (1024 * 1024 * 1024), 2) . " GB"
                    ];
                }
            }
            return ['success' => true, 'models' => $models];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function actionIndex($id = null)
    {
        $conversations = AiConversation::findMine()->orderBy(['updated_at' => SORT_DESC])->all();

        $current = null;
        if ($id) {
            $current = AiConversation::find()
                ->where(['id' => $id, 'user_id' => Yii::$app->user->id])
                ->one();
        }

        return $this->render('index', [
            'conversations' => $conversations,
            'current' => $current ?? new AiConversation(),
        ]);
    }



public function actionSendMessage()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $startTime = microtime(true);
    set_time_limit(1200);

    $post = Yii::$app->request->post();
    $message = $post['message'] ?? '';
    
    // Protezione ID (quella che abbiamo corretto prima)
    $convIdRaw = $post['conversation_id'] ?? null;
    $convId = ($convIdRaw === 'null' || empty($convIdRaw) || !is_numeric($convIdRaw)) ? null : (int)$convIdRaw;
    
    $modelSelected = $post['model'] ?? 'dashdemo-ai';
    $logFile = Yii::getAlias('@runtime/logs/ai_chat_custom.log');
    $timestamp = date('Y-m-d H:i:s');

    // 1. DEFINIZIONE DEI TOOLS
    $tools = [
        [
            'type' => 'function',
            'function' => [
                'name' => 'get_analytic_report',
                'description' => 'Usa questo tool per ottenere totali, somme, ore e statistiche sui ticket dei clienti dal CRM.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'cliente' => ['type' => 'string', 
                        'description' => 'Nome cliente o "all" per classifica globale']
                    ],
                    
                ]
            ]
        ],
        [
            'type' => 'function',
            'function' => [
                'name' => 'search_source_code',
                'description' => 'Usa questo tool per cercare spiegazioni tecniche, file o logiche nel codice sorgente.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Argomento tecnico da cercare']
                    ],
                    'required' => ['query']
                ]
            ]
        ]
    ];

    // 2. SALVATAGGIO MESSAGGIO UTENTE (Mantengo la tua logica)
    $convId = $this->saveConversation($convId, $message);
    $userMsg = new \app\models\AiMessage([
        'conversation_id' => $convId,
        'role' => 'user',
        'content' => $message 
    ]);
    $userMsg->save(false);

    // 3. COSTRUZIONE MESSAGGI PER OLLAMA (Inclusa la tua istruzione forte)
    $messages = [];
$messages[] = [
    'role' => 'system', 
    'content' => "Sei un Agente Analitico. 
    REGOLE MANDATORIE:
    1. Per OGNI cliente menzionato nella domanda, DEVI chiamare il tool 'get_analytic_report'.
    2. Se l'utente chiede un confronto tra due clienti (es. X vs Y), chiama il tool prima per X e poi per Y.
    3. NON provare a rispondere usando la tua memoria o i risultati della ricerca semantica per dati numerici.
    4. Se non chiami il tool per i conteggi, la tua risposta sarà considerata errata.
    5.I ticket con oggetto contenente ':P2000:P2000' sono esclusi automaticamente da quasliasi conteggio, anche se appaiono negli esempi."
];
    // Storico (ultimi 6)
    $history = \app\models\AiMessage::find()
        ->where(['conversation_id' => $convId])
        ->andWhere(['!=', 'id', $userMsg->id])
        ->orderBy('created_at DESC')
        ->limit(6)
        ->all();
    
    foreach (array_reverse($history) as $m) {
        $messages[] = ['role' => $m->role, 'content' => $m->content];
    }
    $messages[] = ['role' => 'user', 'content' => $message];

    // 4. CHIAMATA A OLLAMA CON TOOLS
    try {
        $response = Yii::$app->ollama->chat($modelSelected, $messages, $tools);
        $reply = "";

        // Verifichiamo se l'IA vuole chiamare un Tool
        if (isset($response['message']['tool_calls'])) {
            file_put_contents($logFile, "[$timestamp] TOOL CALL DETECTED\n", FILE_APPEND);
            
            foreach ($response['message']['tool_calls'] as $toolCall) {
                $funcName = $toolCall['function']['name'];
                $args = $toolCall['function']['arguments'];
                $contextResult = "";

                if ($funcName === 'get_analytic_report') {
                    $cliente = $args['cliente'] ?? '';
                    // Usiamo la tua funzione esistente!
                    $contextResult = $this->getActivitiesContext([$cliente], $message);
                } elseif ($funcName === 'search_source_code') {
                    $query = $args['query'] ?? '';
                    $contextResult = $this->getSemanticContext($query);
                }

                // Aggiungiamo il risultato come nuovo messaggio e chiediamo la sintesi finale
                $messages[] = $response['message']; 
                $messages[] = ['role' => 'tool', 'content' => $contextResult, 'name' => $funcName];
            }
            
            $finalRes = Yii::$app->ollama->chat($modelSelected, $messages);
            $reply = $finalRes['message']['content'] ?? "Errore sintesi.";
        } else {
            // Risposta normale
            $reply = $response['message']['content'] ?? "Nessuna risposta.";
        }
        
        // 5. SALVATAGGIO RISPOSTA IA
        $executionTime = round(microtime(true) - $startTime, 2);
        file_put_contents($logFile, "{$timestamp} - RESP ({$executionTime}s) per conv {$convId}\n", FILE_APPEND);

        if (!empty($reply)) {
            $aiMsg = new \app\models\AiMessage([
                'conversation_id' => $convId,
                'role' => 'assistant',
                'content' => $reply
            ]);
            $aiMsg->save();
        }

        return [
            'success' => true,
            'reply' => $reply,
            'conversation_id' => $convId
        ];

    } catch (\Exception $e) {
        file_put_contents($logFile, "ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
public function _preageactionSendMessage()
{
    Yii::$app->response->format = Response::FORMAT_JSON;
    $startTime = microtime(true);
    set_time_limit(1200);

    $post = Yii::$app->request->post();
    $message = $post['message'] ?? '';
    $convId = ($post['conversation_id'] === 'null' || empty($post['conversation_id'])) ? null : $post['conversation_id'];
    $modelSelected = $post['model'] ?? 'dashdemo-ai';
    $searchQuery = strtolower(trim($message));

    $logFile = Yii::getAlias('@runtime/logs/ai_chat_custom.log');
    $timestamp = date('Y-m-d H:i:s');

// 1. DETERMINAZIONE DEL CONTESTO
// --- 1. DETERMINAZIONE DEL CONTESTO ---
// --- 1. DETERMINAZIONE DEL CONTESTO ---
$finalContext = "";
$keywordParts = $this->extractKeywordsArray($searchQuery);

// LOG DI DEBUG: Vediamo cosa ha estratto
file_put_contents($logFile, "KEYWORDS ESTRATTE: " . implode('|', $keywordParts) . "\n", FILE_APPEND);

$isAnalyticQuery = preg_match('/(quanti|totale|ore|somma|media|ammontano|chiusi|aperti)/i', $searchQuery);

if ($isAnalyticQuery) {
    // Cerchiamo la keyword: se non c'è in questa domanda (es. "e quante ore?"), 
    // la cerchiamo nell'ultimo messaggio dell'utente nello storico.
    $mainKeyword = null;
    $potentialSubjects = array_filter($keywordParts, function($w) {
        return !in_array(strtolower($w), ['ticket', 'quanti', 'ore', 'somma', 'ammontano', 'sono']);
    });

    if (!empty($potentialSubjects)) {
        $mainKeyword = reset($potentialSubjects);
    } else {
        // RECUPERO DALLO STORICO: se l'utente non specifica il nome, prendiamo l'ultimo usato
        $lastUserMsg = AiMessage::find()
            ->where(['conversation_id' => $convId, 'role' => 'user'])
            ->orderBy('created_at DESC')
            ->one();
        if ($lastUserMsg) {
            $prevKeywords = $this->extractKeywordsArray(strtolower($lastUserMsg->content));
            $mainKeyword = !empty($prevKeywords) ? $prevKeywords[0] : null;
        }
    }

    if ($mainKeyword) {
        $crmData = $this->getActivitiesContext([$mainKeyword], $searchQuery);
        if (strpos($crmData, 'DATI TOTALI CALCOLATI') !== false) {
            $finalContext = "=== REPORT ANALITICO DEFINITIVO ===\n" . $crmData;
        }
    }
}

// Se ancora vuoto (domanda su codice o altro)
if (empty($finalContext)) {
    $finalContext = $this->getSemanticContext($message);
    $finalContext .= "\n\n[STRUTTURA PROGETTO]:\n" . $this->getProjectStructure(Yii::getAlias('@app'));
}
    // 2. SALVATAGGIO MESSAGGIO UTENTE
    $convId = $this->saveConversation($convId, $message);
    $userMsg = new AiMessage([
        'conversation_id' => $convId,
        'role' => 'user',
        'content' => $message 
    ]);
    $userMsg->save(false);

    // 3. COSTRUZIONE MESSAGGI PER OLLAMA
  // 3. COSTRUZIONE MESSAGGI PER OLLAMA
    $messages = [];

    // Definiamo un'istruzione molto forte per l'IA
    $systemPrompt = "Sei un Analista Dati Dashboard. 
    REGOLA FONDAMENTALE PER I CALCOLI:
    1. Se nel contesto trovi la sezione 'REPORT ANALITICO DEFINITIVO', devi usare ESCLUSIVAMENTE i numeri riportati lì per rispondere a domande su totali, somme e conteggi.
    2. Non provare mai a contare i record nell'elenco degli 'ESEMPI', perché quell'elenco è volutamente incompleto.
    3. Se il report dice che ci sono 5711 record, tu devi rispondere 5711, anche se vedi solo pochi esempi.
    4. Usa i 'FRAMMENTI DI CODICE' solo se l'utente chiede spiegazioni sulla logica software.";

    $messages[] = [
        'role' => 'system', 
        'content' => $systemPrompt
    ];
    // Storico (ultimi 6 messaggi per non appesantire)
    $history = AiMessage::find()
        ->where(['conversation_id' => $convId])
        ->andWhere(['!=', 'id', $userMsg->id])
        ->orderBy('created_at DESC')
        ->limit(6)
        ->all();
    
    foreach (array_reverse($history) as $m) {
        $messages[] = ['role' => $m->role, 'content' => $m->content];
    }

    // Messaggio finale con il contesto iniettato
    $finalUserContent = "[CONTESTO]:\n" . $finalContext . "\n\n[DOMANDA]: " . $message;
    $messages[] = ['role' => 'user', 'content' => $finalUserContent];
file_put_contents($logFile, "DEBUG PROMPT COMPLETO:\n" . json_encode($messages, JSON_PRETTY_PRINT), FILE_APPEND);    // 4. CHIAMATA A OLLAMA
    try {
        $reply = Yii::$app->ollama->chat($modelSelected, $messages);
        
        $executionTime = round(microtime(true) - $startTime, 2);
        file_put_contents($logFile, "{$timestamp} - RESP ({$executionTime}s) per conv {$convId}\n", FILE_APPEND);

        if ($reply) {
            $aiMsg = new AiMessage([
                'conversation_id' => $convId,
                'role' => 'assistant',
                'content' => $reply
            ]);
            $aiMsg->save();
        }

        return [
            'success' => true,
            'reply' => $reply ?: "Nessuna risposta ricevuta.",
            'conversation_id' => $convId
        ];

    } catch (\Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
private function getAnalyticDataFromQdrant($keyword)
{
    $client = new \yii\httpclient\Client(['transport' => 'yii\httpclient\CurlTransport']);
    $totaleOre = 0;
    $conteggioTotale = 0;
    $nextOffset = null;
    $esempiTesto = "";

    // Ciclo "Do-While" per scaricare tutte le pagine di dati
    do {
        $payload = [
            "filter" => [
                "should" => [
                   ["key" => "soggetto", "match" => ["text" => $keyword]],
        ["key" => "codicesoggetto", "match" => ["text" => $keyword]],
        ["key" => "Custom1", "match" => ["value" => $keyword]],
        ["key" => "codiceprogetto", "match" => ["text" => $keyword]]
                ]
            ],
            "limit" => 1000, // Massimo consentito per singola chiamata
            "with_payload" => true,
            "with_vector" => false
        ];

        // Se abbiamo un offset dalla chiamata precedente, lo usiamo per la pagina successiva
        if ($nextOffset !== null) {
            $payload['offset'] = $nextOffset;
        }

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://127.0.0.1:6333/collections/progetto_dashdemo/points/scroll')
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setData($payload)
            ->send();

        if (!$response->isOk) break;

        $result = $response->data['result'] ?? [];
        $points = $result['points'] ?? [];
        
        foreach ($points as $p) {
            $pay = $p['payload'];
            $totaleOre += (float)($pay['oredelta'] ?? 0);
            
            // Salviamo solo i primi 10 titoli come esempio per l'IA
            if ($conteggioTotale < 10) {
                $esempiTesto .= "- {$pay['dataevento']} | Ore: " . ($pay['oredelta'] ?? 0) . " | {$pay['oggetto']}\n";
            }
            $conteggioTotale++;
        }

        // Recuperiamo l'offset per il prossimo giro
        $nextOffset = $result['next_page_offset'] ?? null;

    } while ($nextOffset !== null);

    return [
        'count' => $conteggioTotale,
        'sum' => $totaleOre,
        'esempi_da_ignorare_per_il_calcolo' => $esempiTesto
    ];
}

public function actionSendMessage1()
{
    
Yii::$app->response->format = Response::FORMAT_JSON;
    set_time_limit(1200);
  $post = Yii::$app->request->post();
    $message = $post['message'] ?? '';
    $convId = ($post['conversation_id'] === 'null' || empty($post['conversation_id'])) ? null : $post['conversation_id'];
    $searchQuery = strtolower(trim($message));
    // --- PREPARAZIONE LOG MANUALE ---
    $logFile = Yii::getAlias('@runtime/logs/ai_chat_custom.log');
    $logSeparator = str_repeat('=', 50) . "\n";
    $timestamp = date('Y-m-d H:i:s');

    // 1. ESTRAZIONE PAROLE CHIAVE
    $keywordParts = $this->extractKeywordsArray($searchQuery);
    $externalContext = "";
$modelSelected = $post['model'] ?? 'dashboard-ai';
$modelKey = strtolower($modelSelected);

        if (!empty($keywordParts) and  $modelKey== 'dashboard-ai') {
    // Carichiamo SOLO i dati operativi, ignoriamo biblioteca e API doc
    $externalContext .= $this->getActivitiesContext($keywordParts, $searchQuery);
    //$externalContext .= $this->getProjectsContext($keywordParts, $searchQuery);
}
        // --- AGGIUNTA QUI: SCANSIONE PROGETTO ---
// 2. Controllo flessibile: se l'ID contiene "dashboard", attiva il RAG (Qdrant)
if (strpos($modelKey, 'dashboard') !== false) {
            $projectPath = Yii::getAlias('@app');
            $externalContext .= "\n\n[STRUTTURA FILE PROGETTO LOCALE]:\n";
            $externalContext .= $this->getProjectStructure($projectPath);
// 2. Controllo flessibile: se l'ID contiene "dashboard", attiva il RAG (Qdrant)
if (strpos($modelKey, 'dashboard') !== false) {
            $semanticContext= $this->getSemanticContext($message);
       Yii::error("CONTEXT RECUPERATO DA QDRANT: " . substr($semanticContext, 0, 500), 'AI_DEBUG');

if (!empty($semanticContext)) {
    $externalContext .= $semanticContext;
}
            }
            }

    // 3. SALVATAGGIO E GESTIONE CONVERSAZIONE
    $convId = $this->saveConversation($convId, $message);
    $userMsg = new AiMessage([
        'conversation_id' => $convId,
        'role' => 'user',
        'content' => $message 
    ]);
    $userMsg->save(false);

    // 4. COSTRUZIONE PROMPT PER OLLAMA
    $messages = [];
        // SYSTEM PROMPT DINAMICO
   if (strpos($modelKey, 'dashboard') !== false) {
$messages[] = [
    'role' => 'system',
    'content' => "Sei un analista software esperto del framework Yii2.
                  Riceverai dei frammenti di codice etichettati con 'CATEGORIA' e 'INIZIO FILE'.
                  
                  REGOLE DI ANALISI:
                  1. Se l'utente chiede del CONTROLLER, cerca i dati nei file dentro '@app/controllers'.
                  2. Se l'utente chiede della LOGICA DATABASE, guarda i file in '@app/models'.
                  3. Se l'utente chiede dell'INTERFACCIA o HTML, guarda i file in '@app/views'.
                  4. Se trovi funzioni con lo stesso nome in file diversi, specifica sempre a quale file ti riferisci.
                  5. Non confondere il codice PHP del Controller con il codice HTML della View."
];
               } else {
            $messages[] = [
                'role' => 'system',
                'content' => "Sei un assistente AI generico. Rispondi in modo utile e cordiale"
            ];
        }
    $history = AiMessage::find()
        ->where(['conversation_id' => $convId])
        ->orderBy('created_at DESC')
        ->limit(4)
        ->all();
    $history = array_reverse($history);

    foreach ($history as $m) {
        $messages[] = ['role' => $m->role, 'content' => $m->content];
    }

    if (!empty($externalContext)) {
        $lastIdx = count($messages) - 1;
        $messages[$lastIdx]['content'] .= "\n\n[DATI REALI DAL CRM]:\n" . $externalContext;
    }

    // --- SCRITTURA LOG: RICHIESTA ---
    $fullPayload = json_encode($messages, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $logRequest = "\n{$timestamp} - START REQUEST\n";
    $logRequest .= "Lunghezza Payload: " . strlen($fullPayload) . " caratteri\n";
    $logRequest .= "Payload:\n" . $fullPayload . "\n";
    file_put_contents($logFile, $logSeparator . $logRequest, FILE_APPEND);

    $startTime = microtime(true);

    // 5. CHIAMATA A OLLAMA
    try {
        //$reply = Yii::$app->ollama->chat('dashboard-ai', $messages); 
Yii::error("PROMPT FINALE INVIATO: " . print_r($messages, true), 'AI_DEBUG');
$reply = Yii::$app->ollama->chat($modelSelected, $messages);

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        // --- SCRITTURA LOG: RISPOSTA ---
        $logResponse = "\n{$timestamp} - RESPONSE RECEIVED\n";
        $logResponse .= "Tempo impiegato: {$executionTime} secondi\n";
        $logResponse .= "Risposta:\n" . ($reply ? $reply : "VUOTA") . "\n";
        file_put_contents($logFile, $logResponse . $logSeparator, FILE_APPEND);

        if (!$reply) {
            return ['success' => false, 'error' => "L'IA non ha risposto (Timeout CPU)."];
        }

        // 6. SALVATAGGIO RISPOSTA
        $aiMsg = new AiMessage([
            'conversation_id' => $convId,
            'role' => 'assistant',
            'content' => $reply
        ]);
        $aiMsg->save();

        return ['success' => true,        'ai' => $modelSelected, 'reply' => $reply, 'conversation_id' => $convId];

    } catch (\Exception $e) {
        $logError = "\n{$timestamp} - ERROR\n" . $e->getMessage() . "\n";
        file_put_contents($logFile, $logError . $logSeparator, FILE_APPEND);
        return ['success' => false, 'error' => "Errore critico: " . $e->getMessage()];
    }
}







    private function getProjectStructure($path)
    {
        // Aggiungi la \ davanti alle classi native di PHP
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $structure = "STRUTTURA PROGETTO DASHBOARD:\n";
  $exclude = ['vendor', 'runtime', '.git', 'assets', '.history', '.continue', 'web','vendor','nv_vendor'];

        foreach ($iterator as $item) {
            // DIRECTORY_SEPARATOR non ha bisogno di \ perché è una costante globale, 
            // ma per sicurezza alcuni preferiscono metterla
            $relativePath = str_replace($path . DIRECTORY_SEPARATOR, '', $item->getPathname());

            foreach ($exclude as $skip) {
                if (strpos($relativePath, $skip) === 0) continue 2;
            }

            if ($item->isDir()) {
                $structure .= "[DIR]  $relativePath\n";
            } else {
                if (in_array($item->getExtension(), ['php', 'js', 'css'])) {
                    $structure .= "[FILE] $relativePath\n";
                }
            }
        }
        return $structure;
    }













    public function oldactionSendMessage()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
set_time_limit(600); 
    ini_set('max_execution_time', 600);

        $post = Yii::$app->request->post();
        $message = $post['message'] ?? '';
        $convId = ($post['conversation_id'] === 'null' || empty($post['conversation_id'])) ? null : $post['conversation_id'];
        $searchQuery = strtolower(trim($message));

        // 1. GESTIONE FILE
        $fileContent = $this->handleFileUpload();

        // 2. RECUPERO CONTESTO ESTERNO (VTIGER DB6) - ORA RESTITUISCE UN ARRAY DI PAROLE
        $keywordParts = $this->extractKeywordsArray($searchQuery);
        $externalContext = "";

        if (empty($keywordParts)) {
            $externalContext .= "\n[DATI CRM]: Non ho potuto cercare su Vtiger perché non ho identificato nomi utili nella frase.\n";
        } else {
            $externalContext .= $this->getProjectsContext($keywordParts, $searchQuery);
            $externalContext .= $this->getActivitiesContext($keywordParts, $searchQuery);
        // ---> AGGIUNGI QUESTA RIGA <---
            $externalContext .= $this->getApiDocContext($keywordParts, $searchQuery);
            // ---> AGGIUNGI QUESTA RIGA PER ATTIVARE ARCA <---
            //$externalContext .= $this->getArcaDocContext($keywordParts, $searchQuery);
            // NUOVO: Cerca nella "Biblioteca" dei file JSON (Manuali Arca/SDK)
$externalContext .= $this->getLibraryContext($searchQuery);
            }

        // 3. GESTIONE CONVERSAZIONE E MESSAGGIO UTENTE
        $convId = $this->saveConversation($convId, $message);

        $userMsg = new AiMessage([
            'conversation_id' => $convId,
            'role' => 'user',
            'content' => $message . $fileContent
        ]);
        $userMsg->save(false);

// 4. PREPARAZIONE MESSAGGI
$messages = [];
$messages[] = [
    'role' => 'system',
    'content' => "Sei Dashboard-Expert. Rispondi in modo tecnico e conciso usando i dati forniti."
];
$history = AiMessage::find()
            ->where(['conversation_id' => $convId])
            ->orderBy('created_at DESC') // Ordiniamo dal più recente
            ->all();

// Aggiungiamo la cronologia salvata (che ora sarà pulita)
foreach ($history as $m) {
    $messages[] = ['role' => $m->role, 'content' => $m->content];
}

// AGGIUNGIAMO IL CONTESTO SOLO ALLA CHIAMATA ATTUALE, NON LO SALVIAMO NEL DB DEL MESSAGGIO UTENTE
if (!empty($externalContext)) {
    // Invece di modificare l'ultimo messaggio dell'array $messages (che è già nel DB),
    // creiamo un messaggio virtuale di supporto o lo appendiamo solo per Ollama
    $lastIdx = count($messages) - 1;
    $messages[$lastIdx]['content'] .= "\n\n[DATI DI CONTESTO ATTUALE]:\n" . $externalContext;
}
        $history = AiMessage::find()
            ->where(['conversation_id' => $convId])
            ->orderBy('created_at DESC') // Ordiniamo dal più recente
            ->all();
            
foreach ($history as $m) {
    $messages[] = ['role' => $m->role, 'content' => $m->content];
}

// Se c'è contesto esterno (Arca o Vtiger), lo aggiungiamo all'ultimo messaggio utente
if (!empty($externalContext)) {
    // Riduciamo il rumore del debug per risparmiare memoria
    $messages[count($messages) - 1]['content'] .= "\n\n[DATI DI CONTESTO]:\n" . $externalContext;
}

// 5. CHIAMATA A OLLAMA
try {
    // CAMBIA QUI: Usa il modello che abbiamo creato insieme!
    $reply = Yii::$app->ollama->chat('dashboard-ai', $messages); 
    
    if (!$reply) {
        return ['success' => false, 'error' => "Il server IA non ha risposto."];
    }
} catch (\Exception $e) {
    return ['success' => false, 'error' => "Errore IA: " . $e->getMessage()];
}

        // 6. SALVATAGGIO RISPOSTA IA
        $aiMsg = new AiMessage([
            'conversation_id' => $convId,
            'role' => 'assistant',
            'content' => $reply
        ]);
        $aiMsg->save();

        return ['success' => true,

            'reply' => $reply, 'conversation_id' => $convId];
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

    /**
     * Ritorna un array di parole chiave "pulite"
     */
    /**
     * Ritorna un array di parole chiave "pulite"
     */
    private function extractKeywordsArray($searchQuery)
    {
        $stopWords = [
            'quanti', 'quante', 'ticket', 'ha', 'in', 'tutto', 'e', 'mi', 'puoi', 'fare', 'un', 'una', 'uno',
            'riassunto', 'di', 'tutti', 'tutte', 'i', 'suoi', 'dati', 'azienda', 'cliente', 'clienti', 'ditta',
            'progetto', 'progetti', 'interventi', 'intervento', 'attività', 'il', 'la', 'lo', 'gli', 'le',
            'per', 'su', 'con', 'fammi', 'delle', 'sulle', 'questo', 'questa', 'relativamente', 'serve', 'elenco',
            'ultimi', 'ultime', 'quali', 'sono', 'che', 'del', 'della', 'dei', 'degli', 'al', 'allo', 'alla',
            'dammi', 'trova', 'cerca', 'vedere', 'mostrami', 'storico', 'ore', 'da', 'a',
            'abbiamo', 'avete', 'hanno', 'vorrei', 'sapere', 'dimmi', 'elencami', 'fatto', 'fatti',
            'stato', 'stati', 'aperti', 'chiusi', 'totale', 'totali', 'quali', 'qual', 'quale',
            'ci', 'ne', 'registrati', 'inseriti', 'come', 'quando', 'dove', 'perche', 'dettaglio', 'dettagli',
            'invece', 'cosa', 'fa', 'fanno', 'spiegami'
        ];

        // ---> LA MODIFICA MAGICA È QUI <---
        // Invece di togliere solo punti e virgole, questa espressione regolare rimuove 
        // TUTTO ciò che non è una lettera o un numero, sostituendolo con uno spazio.
        $cleanStr = preg_replace('/[^\p{L}\p{N}_]+/u', ' ', $searchQuery);
        
        $parole = explode(' ', $cleanStr);
        $keywordParts = [];

        foreach ($parole as $p) {
            $p = trim($p);
            // Limitiamo a parole > 2 caratteri
            if (strlen($p) > 2 && !in_array($p, $stopWords)) {
                $keywordParts[] = strtolower($p);
            }
        }

        // Ritorna le parole univoche sotto forma di array
        return array_unique($keywordParts);
    }


private function getSemanticContext($userMessage)
{
    $client = new \yii\httpclient\Client(['transport' => 'yii\httpclient\CurlTransport']);
    $logFile = Yii::getAlias('@runtime/logs/rag_debug.log');
    $timestamp = date('Y-m-d H:i:s');
    
    file_put_contents($logFile, "=== SESSIONE RAG: $timestamp ===\nDOMANDA: $userMessage\n", FILE_APPEND);

    // 1. EMBEDDING
    try {
        $ollamaRes = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://127.0.0.1:11434/api/embeddings')
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setData(['model' => 'nomic-embed-text', 'prompt' => $userMessage])
            ->send();

        if (!$ollamaRes->isOk) return "";
        $embedding = $ollamaRes->data['embedding'];
    } catch (\Exception $e) {
        return "";
    }

    // 2. RICERCA QDRANT (Aumentiamo il limite a 20 per i calcoli)
    $searchParams = [
        'vector' => $embedding,
        'limit' => 100, 
        'with_payload' => true
    ];

    try {
        $qdrantRes = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://127.0.0.1:6333/collections/progetto_dashdemo/points/search')
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setData($searchParams)
            ->send();

        $results = $qdrantRes->data;

        $context = "";
        $logData = "RISULTATI QDRANT TROVATI: " . count($results['result']) . "\n";

        foreach ($results['result'] as $idx => $point) {
            $p = $point['payload'];
            // Se è un record del CRM, estraiamo i dati tecnici per i calcoli
            if (($p['type'] ?? '') === 'crm_record' || ($p['type'] ?? '') === 'crm_full_record') {
                $fileName = $p['soggetto'] ?? 'Record CRM';
                $valoreOra = $p['oredelta'] ?? 0;
                $custom1 = $p['Custom1'] ?? 'N/D';
                
                $context .= "--- DATA RECORD CRM ---\n";
                $context .= "CLIENTE: {$fileName} | ORE: {$valoreOra} | CUSTOM1: {$custom1}\n";
                $context .= "CONTENUTO: " . ($p['text'] ?? '') . "\n\n";
            } else {
                // Se è codice sorgente
                $fileName = $p['filename'] ?? 'File';
                $context .= "--- FILE CODICE: $fileName ---\n" . ($p['text'] ?? '') . "\n\n";
            }
            $logData .= "  [$idx] $fileName (Score: {$point['score']})\n";
        }

        file_put_contents($logFile, $logData . "==========================================\n\n", FILE_APPEND);
        return $context;

    } catch (\Exception $e) {
        file_put_contents($logFile, "❌ CRASH QDRANT: " . $e->getMessage() . "\n", FILE_APPEND);
        return "";
    }
}

private function oldiesgetSemanticContext($userMessage)
{
    $client = new \yii\httpclient\Client(['transport' => 'yii\httpclient\CurlTransport']);
    $logFile = Yii::getAlias('@runtime/logs/rag_debug.log');
    $timestamp = date('Y-m-d H:i:s');
    
    // LOG DI AVVIO: Se vedi questo, la funzione è partita
    $initLog = "=== SESSIONE RAG: $timestamp ===\nDOMANDA: $userMessage\n";
    file_put_contents($logFile, $initLog, FILE_APPEND);

    // 1. ESTRAZIONE NOME FILE (Case Insensitive per sicurezza)
    $forcedFile = null;
    if (preg_match('/([a-zA-Z0-9_]+\.php|[a-zA-Z0-9_]+Controller)/i', $userMessage, $matches)) {
        $forcedFile = $matches[1];
        if (stripos($forcedFile, 'Controller') !== false && stripos($forcedFile, '.php') === false) {
            $forcedFile .= ".php";
        }
    }
    file_put_contents($logFile, "FILE IDENTIFICATO: " . ($forcedFile ?? 'NESSUNO') . "\n", FILE_APPEND);

    // 2. EMBEDDING
    try {
        $ollamaRes = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://127.0.0.1:11434/api/embeddings')
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setData(['model' => 'nomic-embed-text', 'prompt' => $userMessage])
            ->send();

        if (!$ollamaRes->isOk) {
            file_put_contents($logFile, "❌ ERRORE OLLAMA: " . $ollamaRes->content . "\n", FILE_APPEND);
            return "";
        }
        $embedding = $ollamaRes->data['embedding'];
    } catch (\Exception $e) {
        file_put_contents($logFile, "❌ CRASH OLLAMA: " . $e->getMessage() . "\n", FILE_APPEND);
        return "";
    }

    // 3. RICERCA QDRANT
    $searchParams = [
        'vector' => $embedding,
        'limit' => 3,
        'with_payload' => true
    ];

    if ($forcedFile) {
        $searchParams['filter'] = [
            'must' => [
                ['key' => 'filename',
                 'match' => ['text' => $forcedFile]]
            ]
        ];
    }

    try {
        $qdrantRes = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('http://127.0.0.1:6333/collections/progetto_dashdemo/points/search')
            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
            ->setData($searchParams)
            ->send();

        // FIX CRUCIALE: Assicuriamoci che i dati siano un array
        $results = is_array($qdrantRes->data) ? $qdrantRes->data : json_decode($qdrantRes->content, true);

        if (!$qdrantRes->isOk || !isset($results['result'])) {
            file_put_contents($logFile, "❌ ERRORE QDRANT: " . $qdrantRes->content . "\n", FILE_APPEND);
            return "";
        }

        $context = "";
        $foundCount = count($results['result']);
        $logData = "RISULTATI QDRANT: $foundCount\n";

        foreach ($results['result'] as $idx => $point) {
            $fileName = $point['payload']['filename'] ?? 'N/D';
            $textChunk = $point['payload']['text'] ?? '';
            $score = $point['score'] ?? 0;

            $logData .= "  [$idx] $fileName (Score: $score)\n";
            $context .= "--- INIZIO FRAMMENTO FILE: $fileName ---\n$textChunk\n--- FINE FRAMMENTO ---\n\n";
        }

        $logData .= "==========================================\n\n";
        file_put_contents($logFile, $logData, FILE_APPEND);
        return $context;

    } catch (\Exception $e) {
        file_put_contents($logFile, "❌ CRASH QDRANT: " . $e->getMessage() . "\n", FILE_APPEND);
        return "";
    }
}
private function getProjectsContext($keywordParts, $searchQuery)
{
    if (empty($keywordParts)) return "";

    $scoreSelects = [];
    $params = [];

    foreach ($keywordParts as $i => $word) {
        $scoreSelects[] = "(CASE WHEN soggetto LIKE :w$i THEN 2 ELSE 0 END) + " .
            "(CASE WHEN codiceprogetto LIKE :w$i THEN 2 ELSE 0 END)";
        $params[":w$i"] = "%$word%";
    }
    $scoreSql = implode(' + ', $scoreSelects);

    $query = "SELECT soggetto, dataevento, oredelta, codiceprogetto, xtipologia, ($scoreSql) as relevance_score 
              FROM x_vistaprog 
              WHERE ($scoreSql) > 0 
              ORDER BY relevance_score DESC, dataevento DESC 
              LIMIT 50";

    try {
        $dati = Yii::$app->db6->createCommand($query, $params)->queryAll();

        if (!empty($dati)) {
            $oreProgetto = 0;
            $listaProgetti = "";

            foreach ($dati as $index => $p) {
                $oreProgetto += (float)$p['oredelta'];

                // Prendiamo solo i primi 10 per il dettaglio testuale
                if ($index < 10) {
                    $dataF = date('d/m/Y', strtotime($p['dataevento']));
                    $listaProgetti .= "- Progetto: {$p['codiceprogetto']} | Ore: {$p['oredelta']} | Tipo: {$p['xtipologia']} (Data: $dataF)\n";
                }
            }

            // Costruiamo l'output testuale leggero
            $output = "\n[ANALISI PROGETTI PER: " . strtoupper($dati[0]['soggetto']) . "]\n";
            $output .= "Statistiche Progetti: Totale ore: " . round($oreProgetto, 2) . " | Numero voci trovate: " . count($dati) . "\n";
            $output .= "\n[ELENCO DETTAGLIO PROGETTI]:\n" . $listaProgetti;

            return $output;
        }
    } catch (\Exception $e) {
        return "\n[ERRORE LETTURA PROGETTI]: " . $e->getMessage() . "\n";
    }
    return "";
}

    /**
     * Motore di Ricerca Punteggio per x_vistaprog
     */
    private function old_getProjectsContext($keywordParts, $searchQuery)
    {
        if (empty($keywordParts)) return "";

        $scoreSelects = [];
        $params = [];

        // Costruiamo il punteggio: diamo 2 punti se la parola è nel soggetto o progetto
        foreach ($keywordParts as $i => $word) {
            $scoreSelects[] = "(CASE WHEN soggetto LIKE :w$i THEN 2 ELSE 0 END) + " .
                "(CASE WHEN codiceprogetto LIKE :w$i THEN 2 ELSE 0 END)";
            $params[":w$i"] = "%$word%";
        }
        $scoreSql = implode(' + ', $scoreSelects);

        // HAVING relevance_score > 0 scarta tutti i record che non matchano nulla!
        $query = "SELECT x_vistaprog.*,($scoreSql) as relevance_score 
                  FROM x_vistaprog 
                  HAVING relevance_score > 0 
                  ORDER BY relevance_score DESC, dataevento DESC 
                  ";

        try {
            $dati = Yii::$app->db6->createCommand($query, $params)->queryAll();

            if (!empty($dati)) {
                $oreProgetto = 0;
                foreach ($dati as $p) {
                    $oreProgetto += (float)$p['oredelta'];
                }
                $riassunto = [
                    'totale_ore_progetti' => $oreProgetto,
                    'numero_voci_progetto_trovate' => count($dati),
                    'miglior_corrispondenza' => $dati[0]['soggetto'] . ' (' . $dati[0]['codiceprogetto'] . ')'
                ];
                return "\n[RIASSUNTO NUMERICO PROGETTI (DA PHP)]:\n" . json_encode($riassunto) .
                    "\n[DETTAGLIO PROGETTI DA CRM]:\n" . json_encode(array_slice($dati, 0, 10)) . "\n";
            }
        } catch (\Exception $e) {
            return "\n[ERRORE LETTURA PROGETTI]: " . $e->getMessage() . "\n";
        }
        return "";
    }

private function getActivitiesContext($keywordParts, $searchQuery)
{
    // 1. GESTIONE CASO "CLASSIFICA GLOBALE" (Se l'IA manda 'all' o 'null')
    if (empty($keywordParts) || in_array('all', $keywordParts) || in_array('null', $keywordParts)) {
        $sqlGlobal = "SELECT TOP 10 soggetto, COUNT(*) as totale, SUM(CAST(oredelta AS FLOAT)) as ore 
                      FROM xestrazione 
                      WHERE oggetto NOT LIKE '%:P2000:P2000%' 
                      GROUP BY soggetto 
                      ORDER BY totale DESC";
        
        try {
            $classifica = Yii::$app->db6->createCommand($sqlGlobal)->queryAll();
            $out = "=== CLASSIFICA GLOBALE TOP 10 CLIENTI ===\n";
            foreach ($classifica as $r) {
                $out .= "- {$r['soggetto']}: {$r['totale']} ticket (" . round($r['ore'], 2) . " ore)\n";
            }
            return $out . "==========================================\n";
        } catch (\Exception $e) {
            return "Errore classifica globale: " . $e->getMessage();
        }
    }

    // --- DA QUI IN POI LA TUA LOGICA ORIGINALE RIMANE IDENTICA ---
    $logFile = Yii::$app->getAlias('@runtime/logs/ai_chat_custom.log');
    $scoreSelects = [];
    $params = [];

    foreach ($keywordParts as $i => $word) {
        $scoreSelects[] = "(CASE WHEN soggetto LIKE :w$i THEN 2 ELSE 0 END) + " .
                          "(CASE WHEN codiceprogetto LIKE :w$i THEN 2 ELSE 0 END) + " .
                          "(CASE WHEN oggetto LIKE :w$i THEN 1 ELSE 0 END)";
        $params[":w$i"] = "%$word%";
    }
    $scoreSql = implode(' + ', $scoreSelects);

    // Abbiamo aggiunto il filtro :P2000:P2000 anche qui per sicurezza
    $query = "SELECT * FROM xestrazione WHERE ($scoreSql) > 0 
              AND oggetto NOT LIKE '%:P2000:P2000%' 
              ORDER BY dataevento DESC";

    try {
        $dati = Yii::$app->db6->createCommand($query, $params)->queryAll();
        if (empty($dati)) return "\n[DATI CRM]: Nessun record trovato.\n";

        // --- IL TUO MULTI-AGGREGATORE (Invariato) ---
        $totali = ['ore' => 0, 'ticket' => count($dati)];
        $stats = [
            'tecnici' => [], 'stati' => [], 'aree' => [], 'tipi' => [],
        ];

        foreach ($dati as $row) {
            $ora = (float)($row['oredelta'] ?? 0);
            $totali['ore'] += $ora;
            $t = $row['utente_destinatario'] ?? $row['tecnico'] ?? 'N.D.';
            $stats['tecnici'][$t] = ($stats['tecnici'][$t] ?? 0) + $ora;
            $s = strtoupper($row['STATUS'] ?? $row['codicestatoevento'] ?? 'N.D.');
            $stats['stati'][$s] = ($stats['stati'][$s] ?? 0) + 1;
            $a = $row['AREA'] ?? 'Generica';
            $stats['aree'][$a] = ($stats['aree'][$a] ?? 0) + 1;
            $tp = $row['xtipologia'] ?? 'N.D.';
            $stats['tipi'][$tp] = ($stats['tipi'][$tp] ?? 0) + 1;
        }

        $cliente = strtoupper($dati[0]['soggetto'] ?? 'CLIENTE');
        $output = "=== REPORT ANALITICO DEFINITIVO PER $cliente ===\n";
        $output .= "RIASSUNTO: {$totali['ticket']} ticket totali per " . round($totali['ore'], 2) . " ore.\n\n";

        $output .= "DISTRIBUZIONE PER STATO:\n";
        foreach ($stats['stati'] as $stato => $num) $output .= "- $stato: $num ticket\n";

        $output .= "\nTOP TECNICI (CARICO ORE):\n";
        arsort($stats['tecnici']);
        foreach (array_slice($stats['tecnici'], 0, 3) as $nome => $h) $output .= "- $nome: " . round($h, 2) . " ore\n";

        $output .= "\nAREE DI INTERVENTO PREVALENTI:\n";
        arsort($stats['aree']);
        foreach (array_slice($stats['aree'], 0, 3) as $area => $num) $output .= "- $area: $num ticket\n";

        $output .= "\nTIPOLOGIE ATTIVITÀ:\n";
        arsort($stats['tipi']);
        foreach (array_slice($stats['tipi'], 0, 3) as $tipo => $num) $output .= "- $tipo: $num ticket\n";
        
        $output .= "===============================================\n";

        file_put_contents($logFile, "REPORT GENERATO PER $cliente: " . $totali['ticket'] . " ticket.\n", FILE_APPEND);
        return $output;

    } catch (\Exception $e) {
        return "ERRORE ANALISI: " . $e->getMessage();
    }
}
    /**
     * Motore di Ricerca Punteggio per xestrazione
     */
    private function old_getActivitiesContext($keywordParts, $searchQuery)
    {
        if (empty($keywordParts)) return "";

        $scoreSelects = [];
        $params = [];

        // Punteggio: 2 punti per soggetto/progetto, 1 punto per l'oggetto del ticket
        foreach ($keywordParts as $i => $word) {
            $scoreSelects[] = "(CASE WHEN soggetto LIKE :w$i THEN 2 ELSE 0 END) + " .
                "(CASE WHEN codiceprogetto LIKE :w$i THEN 2 ELSE 0 END) + " .
                "(CASE WHEN oggetto LIKE :w$i THEN 1 ELSE 0 END)";
            $params[":w$i"] = "%$word%";
        }
        $scoreSql = implode(' + ', $scoreSelects);

        $query = "SELECT soggetto, dataevento, TIPOEVENTO, oredelta, codicestatoevento, oggetto, descrizioneprogetto, ($scoreSql) as relevance_score 
                  FROM xestrazione 
                  HAVING relevance_score > 0 
                  ORDER BY relevance_score DESC, dataevento DESC 
                  ";

        try {
            $dati = Yii::$app->db6->createCommand($query, $params)->queryAll();

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
                    'miglior_corrispondenza_cliente' => $dati[0]['soggetto'],
                    'totale_interventi_estratti' => count($dati),
                    'ticket_chiusi' => $chiusi,
                    'ticket_in_corso_o_aperti' => $aperti,
                    'ore_totali_lavorate' => $ore
                ];

                $ultimeAttivita = array_slice($dati, 0, 15);

                return "\n[RIASSUNTO NUMERICO ATTIVITÀ CALCOLATO DA PHP]:\n" . json_encode($riassunto) .
                    "\n[DETTAGLIO RECENTI ATTIVITÀ DA CRM]:\n" . json_encode($ultimeAttivita) . "\n";
            } else {
                return "\n[DATI CRM]: Nessuna attività trovata per le parole chiave ricercate.\n";
            }
        } catch (\Exception $e) {
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

    public function actionCheckStatus()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $connection = @fsockopen('127.0.0.1', 11434, $errno, $errstr, 2);
        if (is_resource($connection)) {
            fclose($connection);
            return ['online' => true];
        } else {
            return ['online' => false, 'error' => $errstr];
        }
    }


    /**
     * Motore di Ricerca per la Documentazione API (File Locali)
     */
    /**
     * Motore di Ricerca per la Documentazione API (File Locali)
     */
    /**
     * Motore di Ricerca per la Documentazione API (File Locali)
     */
   /**
     * Motore di Ricerca per la Documentazione API (File Locali)
     */
    private function getApiDocContext($keywordParts, $searchQuery)
    {
        $triggerWords = ['codice', 'api', 'documentazione', 'classe', 'funzione', 'metodo', 'dashboard', 'doc', 'controller'];
        $isDocRequest = false;
        foreach ($triggerWords as $tw) {
            if (strpos($searchQuery, $tw) !== false) {
                $isDocRequest = true;
                break;
            }
        }

        if (!$isDocRequest || empty($keywordParts)) {
            return "";
        }

        Yii::warning("RICERCA DOCUMENTAZIONE API AVVIATA", 'AI_CHAT');
        $docPath = Yii::getAlias('@webroot/uploads/doc_api'); 
        $context = "";

        if (is_dir($docPath)) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($docPath));
            $foundFiles = [];

            $searchWords = [];
            foreach ($keywordParts as $word) {
                if (strlen($word) > 5 && !in_array($word, ['documentazione', 'classe', 'metodo', 'funzione'])) {
                    $searchWords[] = strtolower($word);
                }
            }

            if (!empty($searchWords)) {
                foreach ($iterator as $file) {
                    if ($file->isFile() && $file->getExtension() === 'html') {
                        $filename = strtolower($file->getFilename());
                        foreach ($searchWords as $word) {
                            if (strpos($filename, $word) !== false) {
                                $foundFiles[] = $file->getPathname();
                                break;
                            }
                        }
                    }
                }
            }

            $foundFiles = array_slice($foundFiles, 0, 2);

            foreach ($foundFiles as $filePath) {
                $html = file_get_contents($filePath);
                
                // RIMOZIONE CHIRURGICA DEI MENU
                // Eliminiamo esplicitamente la navigazione di phpDocumentor
                $html = preg_replace('#<nav(.*?)>(.*?)</nav>#is', '', $html);
                $html = preg_replace('#<div id="leftColumn"(.*?)>(.*?)</div>#is', '', $html); // Menu laterale tipico
                $html = preg_replace('#<div class="sidebar"(.*?)>(.*?)</div>#is', '', $html); // Altra variante
                $html = preg_replace('#<header(.*?)>(.*?)</header>#is', '', $html);
                
                // Proviamo a estrarre SOLO il contenuto principale (se esiste)
                if (preg_match('#<div id="content"(.*?)>(.*?)</div>\s*<div id="footer"#is', $html, $matches)) {
                    $html = $matches[2]; // Prende solo quello che c'è tra content e footer
                } elseif (preg_match('#<main(.*?)>(.*?)</main>#is', $html, $matches)) {
                    $html = $matches[2];
                }

                $testoPulito = strip_tags($html);
                $testoPulito = preg_replace('/\s+/', ' ', $testoPulito);

                // Limitiamo a 8000 caratteri, che è un limite di sicurezza ottimale per i modelli 1B
                $estratto = substr(trim($testoPulito), 0, 15000);

                $context .= "\n[ESTRATTO DOCUMENTAZIONE API DA " . basename($filePath) . "]:\n" . $estratto . "\n";
            }
        }

        if (!empty($context)) {
            // Manteniamo il debug per verificare che la pulizia abbia funzionato
            file_put_contents(Yii::getAlias('@runtime/logs/debug_doc.txt'), $context);
            return $context;
        } else {
            return "\n[DATI API]: Nessun file di documentazione specifico trovato per i termini tecnici usati.\n";
        }
    }
    /**
     * Motore di Ricerca per Manuali Arca (Ricerca ALL'INTERNO dei file HTML)
     */
   /**
     * Motore di Ricerca Avanzato: Arca Evolution + Visual FoxPro Knowledge Base
     */
 private function getArcaDocContext($keywordParts, $searchQuery)
{
    $jsonPath = Yii::getAlias('@webroot/uploads/arca/manuale_indexed.json');
    if (!file_exists($jsonPath)) return "";

    // Carichiamo l'indice (operazione fulminea rispetto alla scansione file)
    $manuale = json_decode(file_get_contents($jsonPath), true);
    if (!$manuale) return "";

    $fileScores = [];
    $searchQuery = strtolower($searchQuery);

    foreach ($manuale as $index => $item) {
        $punteggio = 0;
        $title = strtolower($item['title']);
        $content = strtolower($item['content']);

        foreach ($keywordParts as $word) {
            $word = strtolower($word);
            
            // 1. SUPER BONUS TITOLO (Il cuore del sistema)
            // Se la parola è nel titolo (es. "SCAN"), diamo 2000 punti
            if (strpos($title, $word) !== false) {
                $punteggio += 2000;
            }

            // 2. BONUS PAROLA INTERA NEL TESTO
            // Usiamo regex per trovare la parola esatta, non "scanned" dentro "scan"
            if (preg_match("/\b" . preg_quote($word, '/') . "\b/", $content)) {
                $punteggio += 100;
            }
        }

        if ($punteggio > 50) {
            $fileScores[$index] = $punteggio;
        }
    }

    // Ordiniamo per punteggio
    arsort($fileScores);
    
    // Prendiamo i 2 migliori risultati (massimo 3 per non affogare Phi-3)
    $topIndices = array_slice(array_keys($fileScores), 0, 1);

    $context = "";
    foreach ($topIndices as $idx) {
        $item = $manuale[$idx];
        $context .= "\n[FONTE: {$item['title']} - Cartella: 
        {$item['folder']}]:\n{$item['content']}\n";
    }

    // Debug opzionale: vedi nel log cosa sta inviando all'IA
    if (!empty($context)) {
        Yii::warning("CONTESTO TROVATO: " . count($topIndices) . " documenti.", 'AI_CHAT');
    }

    return $context;
}






private function cleanUtf8($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = $this->cleanUtf8($value);
        }
    } elseif (is_string($data)) {
        // Rimuove sequenze UTF-8 invalide e caratteri non stampabili
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}






public function actionIndexmanuals()
{
    set_time_limit(0);
    ini_set('memory_limit', '1024M');
    
    $basePath = Yii::getAlias('@webroot/uploads/arca');
    $outputFile = $basePath . '/manuale_indexed.json';
    $results = [];

    if (!is_dir($basePath)) return "ERRORE: La cartella $basePath non esiste!";

    $files = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($files as $file) {
        if ($file->getFilename() === 'manuale_indexed.json') continue;
        $ext = strtolower($file->getExtension());

        if (in_array($ext, ['htm', 'html', 'txt'])) {
            $path = $file->getPathname();
            $content = @file_get_contents($path);
            if (empty($content)) continue;

            // Pulizia Codifica
            $content = mb_convert_encoding($content, 'UTF-8', 'ISO-8859-1, Windows-1252, UTF-8');
            $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);

            // Estrazione Titolo
            $title = $file->getFilename();
            if (preg_match('/<title>(.*?)<\/title>/is', $content, $matches)) {
                $title = trim(strip_tags($matches[1]));
            }

            // Pulizia Testo (Rimuoviamo script, stili e tag HTML)
            $cleanText = preg_replace('/<(script|style)[^>]*?>.*?<\/\\1>/si', '', $content);
            $cleanText = strip_tags($cleanText);
            $cleanText = html_entity_decode($cleanText, ENT_QUOTES, 'UTF-8');
            $cleanText = preg_replace('/\s+/', ' ', $cleanText); // Normalizza spazi e invii
            $cleanText = trim($cleanText);

            // --- LOGICA DI CHUNKING (Spezzettamento) ---
            // Dividiamo il testo in pezzi di circa 1500 caratteri
            // Sovrapponiamo i pezzi di 200 caratteri per non perdere il contesto tra un pezzo e l'altro
            $chunkSize = 1500;
            $overlap = 200;
            $start = 0;
            $chunkCount = 0;

            while ($start < strlen($cleanText)) {
                $chunkText = substr($cleanText, $start, $chunkSize);
                
                $results[] = [
                    'title'   => (string)$title . " (Parte " . (++$chunkCount) . ")",
                    'file'    => (string)$file->getFilename(),
                    'folder'  => (string)basename(dirname($path)),
                    'content' => (string)$chunkText,
                    'full_path' => $path
                ];

                $start += ($chunkSize - $overlap);
                if (strlen($cleanText) - $start < $overlap) break;
            }
        }
    }

    if (empty($results)) return "ATTENZIONE: Nessun file trovato!";

    // Encoding finale
    $jsonData = json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    
    if (file_put_contents($outputFile, $jsonData)) {
        return "<b>SUCCESSO!</b> Creati " . count($results) . " frammenti di conoscenza.";
    }
    return "ERRORE: Scrittura fallita.";
}


private function autoUpdateIndex()
{
    $basePath = Yii::getAlias('@webroot/uploads/arca');
    $jsonPath = $basePath . '/manuale_indexed.json';

    // Se il JSON non esiste o la cartella è stata modificata dopo l'ultima indicizzazione
    if (!file_exists($jsonPath) || filemtime($basePath) > filemtime($jsonPath)) {
        Yii::info("Nuovi file rilevati in Arca Library. Aggiornamento indice...", 'AI_CHAT');
        $this->actionIndexmanuals(); // Richiama la tua funzione di indicizzazione
    }
}

private function getLibraryContext($searchQuery) 
{
    // --- PARTE A: AUTO-AGGIORNAMENTO CONOSCENZA ---
    $basePath = Yii::getAlias('@webroot/uploads/arca');
    $jsonPath = $basePath . '/manuale_indexed.json';

    // Se aggiungi un file nella cartella, questa riga lo rileva e aggiorna il JSON al volo
    if (!file_exists($jsonPath) || (filemtime($basePath) > filemtime($jsonPath))) {
        Yii::info("Rilevate modifiche alla cartella Arca. Aggiornamento indice...", 'AI_CHAT');
        $this->actionIndexmanuals(); // Rigenera il manuale_indexed.json
    }

    // --- PARTE B: RICERCA NELL'INDICE ---
    if (!file_exists($jsonPath)) return "";
    $manuale = json_decode(file_get_contents($jsonPath), true);
    if (!$manuale) return "";

    $keywordParts = $this->extractKeywordsArray($searchQuery);
    $fileScores = [];

    // 1. SCORING: Cerchiamo i paragrafi più pertinenti
    foreach ($manuale as $index => $item) {
        $punteggio = 0;
        $title = strtolower($item['title'] ?? '');
        $content = strtolower($item['content'] ?? '');

        foreach ($keywordParts as $word) {
            if (strpos($title, $word) !== false) $punteggio += 500;
            if (strpos($content, $word) !== false) $punteggio += 100;
        }
        if ($punteggio > 0) $fileScores[$index] = $punteggio;
    }

    arsort($fileScores);
    
    // 2. ESTRAZIONE: Massimo 2 risultati e limite caratteri per la velocità
    $topIndices = array_slice(array_keys($fileScores), 0, 2);
    $context = "";
    $totalChars = 0;
    $maxChars = 2048; // Limite critico per evitare timeout su CPU

    foreach ($topIndices as $idx) {
        $item = $manuale[$idx];
        $testoDoc = trim($item['content']);
        
        // Se il documento è un "muro di testo", lo tagliamo subito
        if (strlen($testoDoc) > 1000) {
            $testoDoc = substr($testoDoc, 0, 1000) . "...";
        }

        $context .= "\n[FONTE BIBLIOTECA: " . strtoupper($item['title']) . "]\n";
        $context .= $testoDoc . "\n";
        
        $totalChars += strlen($testoDoc);
        if ($totalChars >= $maxChars) break;
    }

    return !empty($context) ? "\n--- CONTESTO DOCUMENTALE ESTRATTO ---\n" . $context : "";
}

}
