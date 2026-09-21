<?php

namespace app\controllers;

use Yii;
use app\models\Planning;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use kartik\mpdf\Pdf;
/**
 * PlanningController implements the CRUD actions for Planning model.
 */
class PlanningController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

/**
     * Visualizza l'elenco delle attività pianificate tramite DataProvider standard.
     * @return string Il rendering della vista 'index'.
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Planning::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
/**
     * Visualizza il dettaglio di una singola attività pianificata.
     * @param integer $id Identificativo attività.
     * @return string
     * @throws NotFoundHttpException Se l'attività non esiste.
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

/**
     * Crea un nuovo impegno nel planning.
     * Include la logica di precompilazione automatica tramite parametri GET (doppio click calendario)
     * e il sistema di controllo preventivo della disponibilità delle risorse.
     * * @param string|null $data_attivita Data passata dal calendario.
     * @param integer|null $personale_id ID dipendente filtrato.
     * @param integer|null $veicolo_id ID veicolo selezionato.
     * @return mixed
     */


public function actionCreate($data_attivita = null, $personale_id = null, $veicolo_id = null)
{
    $model = new \app\models\Planning(); // Assicurati che il namespace sia corretto

    // Precompilazione
    $model->data_attivita = $data_attivita ?: date('Y-m-d');
    if ($personale_id) $model->personale_ids = [$personale_id];
    if ($veicolo_id) $model->veicolo_id = $veicolo_id;

    if ($model->load(Yii::$app->request->post())) {
        
        // Se è una richiesta AJAX, rispondiamo in JSON
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            // Catturiamo il flag di forzatura dal frontend
            $forceSave = Yii::$app->request->post('force_save', 0);

            // Eseguiamo il controllo SOLO se non stiamo forzando il salvataggio
            if ($forceSave != 1) {
                
                // Verifichiamo se abbiamo gli orari per fare un controllo preciso
                $hasOrari = !empty($model->ora_inizio) && !empty($model->ora_fine);

                // --- CONTROLLO VEICOLO ---
                $queryVeicolo = \app\models\Planning::find()
                    ->where(['data_attivita' => $model->data_attivita, 'veicolo_id' => $model->veicolo_id]);
                
                // Se sono stati inseriti gli orari, controlliamo l'accavallamento
                if ($hasOrari) {
                    $queryVeicolo->andWhere(['<', 'ora_inizio', $model->ora_fine])
                                 ->andWhere(['>', 'ora_fine', $model->ora_inizio]);
                }
                $conflittoVeicolo = $queryVeicolo->exists();

                // --- CONTROLLO PERSONALE ---
                $conflittoPersonale = false;
                if (!empty($model->personale_ids)) {
                    $queryPersonale = \app\models\Planning::find()
                        ->alias('p')
                        ->joinWith('personali') // Assicurati che il nome della relation sia 'personali'
                        ->where(['p.data_attivita' => $model->data_attivita])
                        ->andWhere(['in', 'planning_personale.personale_id', $model->personale_ids]);
                    
                    if ($hasOrari) {
                        $queryPersonale->andWhere(['<', 'p.ora_inizio', $model->ora_fine])
                                       ->andWhere(['>', 'p.ora_fine', $model->ora_inizio]);
                    }
                    $conflittoPersonale = $queryPersonale->exists();
                }

                // --- GENERAZIONE AVVISI ---
                $warningMessages = [];
                if ($conflittoVeicolo) {
                    $warningMessages[] = 'Il <b>VEICOLO</b> selezionato risulta già impegnato per questo orario.';
                }
                if ($conflittoPersonale) {
                    $warningMessages[] = 'Uno o più <b>DIPENDENTI</b> selezionati risultano già impegnati per questo orario.';
                }

                // Se ci sono conflitti, blocchiamo il salvataggio e restituiamo un avviso
                if (!empty($warningMessages)) {
                    return [
                        'success' => false,
                        'isWarning' => true, // <-- Flag speciale per il JavaScript
                        'message' => implode('<br>', $warningMessages) . '<br><br>Vuoi salvare ugualmente ignorando il conflitto?'
                    ];
                }
            }

            // Se non ci sono conflitti, o se l'utente ha forzato il salvataggio cliccando "Salva Comunque"
            if ($model->save()) {
                return ['success' => true];
            }
            
            return ['success' => false, 'error' => 'Errore durante il salvataggio. Controlla i dati inseriti.'];
        }

        // Fallback per salvataggio standard (non AJAX)
        if ($model->save()) {
            return $this->redirect(['index']);
        }
    }

    if (Yii::$app->request->isAjax) {
        return $this->renderAjax('create', ['model' => $model]);
    }
    return $this->render('create', ['model' => $model]);
}
 public function xoldactionCreate($data_attivita = null, $personale_id = null, $veicolo_id = null)
    {
        $model = new Planning();

        // Precompilazione
        $model->data_attivita = $data_attivita ?: date('Y-m-d');
        if ($personale_id) $model->personale_ids = [$personale_id];
        if ($veicolo_id) $model->veicolo_id = $veicolo_id;

        if ($model->load(Yii::$app->request->post())) {
            // Se è una richiesta AJAX, rispondiamo in JSON
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

                // --- LOGICA CONFLITTI (Mantenuta) ---
                $conflittoVeicolo = Planning::find()
                    ->where(['data_attivita' => $model->data_attivita,
                     'veicolo_id' => $model->veicolo_id])
                    ->exists();

                $conflittoPersonale = false;
                if (!empty($model->personale_ids)) {
                    $conflittoPersonale = Planning::find()
                        ->alias('p')
                        ->joinWith('personali')
                        ->where(['p.data_attivita' => $model->data_attivita])
                        ->andWhere(['in', 'planning_personale.personale_id', $model->personale_ids])
                        ->exists();
                }

                if ($conflittoVeicolo and 1==2) { // <-- DISATTIVATO TEMPORANEAMENTE
                    return ['success' => false, 'error' => 'Il VEICOLO selezionato
                     è già impegnato per questa data.'];
                }
                if ($conflittoPersonale and 1==2) {
                    return ['success' => false, 'error' => 'Uno o più DIPENDENTI selezionati
                     sono già impegnati.'];
                }

                if ($model->save()) {
                    return ['success' => true];
                }
                
                return ['success' => false, 'error' => 'Errore durante il salvataggio.'];
            }

            // Fallback per salvataggio standard (non AJAX)
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', ['model' => $model]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function old_actionCreate($data_attivita = null, $personale_id = null, $veicolo_id = null)
    {
        $model = new Planning();
        // --- INIZIO MODIFICA: Precompilazione campi dal doppio click ---
if ($data_attivita !== null) {
            // Lasciala così com'è! (Y-m-d)
            $model->data_attivita = $data_attivita; 
        }
        // Se arriviamo dal calendario con un ID singolo, lo mettiamo nell'array multi
        if ($personale_id !== null) {
            $model->personale_ids = [$personale_id];
        }
        if ($veicolo_id !== null) {
            $model->veicolo_id = $veicolo_id;
        }
        // --- FINE MODIFICA ---
        if ($model->load(Yii::$app->request->post())) {
            // Verifica disponibilità automatica richiesta
            $conflitto = Planning::find()
                ->where(['data_attivita' => $model->data_attivita])
                ->andWhere(['or', ['personale_id' => $model->personale_id], ['veicolo_id' => $model->veicolo_id]])
                ->exists();

            if ($conflitto) {
                Yii::$app->session->setFlash('error', 'Risorsa (Personale o Veicolo) già impegnata per questa data.');
            } else if ($model->save()) {
                return $this->redirect(['index']);
            }
        }
        return $this->render('create', ['model' => $model]);
    }

/**
     * Aggiorna un'attività esistente.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
$model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post())) {
        
        // Il controllo va fatto QUI, dopo che load() ha riempito il modello
        if ($model->ditta_esterna === '') {
            $model->ditta_esterna = null;
        }

        if ($model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
    }

    return $this->render('update', [
        'model' => $model,
    ]);
    }

/**
     * Elimina un'attività pianificata.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['list']);
    }

/**
     * Ricerca un modello Planning tramite ID.
     * @param integer $id
     * @return Planning
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Planning::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
/**
     * Recupera i dati del Planning in formato JSON per FullCalendar.
     * Assegna automaticamente i colori agli eventi in base al veicolo utilizzato.
     * * @param string|null $start Data inizio periodo (ISO8601).
     * @param string|null $end Data fine periodo (ISO8601).
     * @return array Insieme di eventi formattati per FullCalendar.
     */


public function actionEventsjson($start = null, $end = null)
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    if ($start === null) $start = Yii::$app->request->get('start');
    if ($end === null) $end = Yii::$app->request->get('end');
    // Palette di colori professionali per i veicoli
    $colors = [
        '#3498db', '#2ecc71', '#9b59b6', '#f1c40f', '#e67e22',
        '#1abc9c', '#34495e', '#7f8c8d', '#ff9f43', '#54a0ff'
    ];
    try {
        // MODIFICATO: Usiamo la nuova relazione 'veicoliListRel' (e 'cliente' per ottimizzare)
        $models = Planning::find()
            ->with(['personali', 'veicoliListRel', 'cliente'])
            ->where(['between', 'data_attivita', substr($start, 0, 10), substr($end, 0, 10)])
            ->all();

        $events = [];
        foreach ($models as $m) {
            $oraInizio = $m->ora_inizio ? substr($m->ora_inizio, 0, 5) : '08:00';
            $oraFine = $m->ora_fine ? substr($m->ora_fine, 0, 5) : '18:00';

            // --- GESTIONE VEICOLI MULTIPLI ---
            // Estraiamo il primo veicolo per calcolare il colore
            $primoVeicolo = !empty($m->veicoliListRel) ? $m->veicoliListRel[0] : null;
            $colorIndex = ($primoVeicolo) ? ($primoVeicolo->id % count($colors)) : 0;
            $eventColor = ($primoVeicolo) ? $colors[$colorIndex] : '#bdc3c7';

            // Estraiamo tutte le targhe per il titolo
            $targhe = [];
            foreach ($m->veicoliListRel as $v) {
                $targhe[] = $v->targa;
            }
            $stringaTarghe = !empty($targhe) ? implode(', ', $targhe) : 'No Mezzo';

            // Recupero Nomi e Cliente
            $titoloPersonale = $m->getNominativiCompleti();
            $cliente = $m->cliente ? $m->cliente->Descrizione : 'Nessun cliente';

            $events[] = [
                'id' => $m->id,
                // Titolo dinamico con TUTTE le Targhe + Tutti i dipendenti assegnati
                'title' => '[Giro ' . $m->giro . '] ' . $cliente . " [" . $stringaTarghe . "] " . $titoloPersonale,
                'start' => $m->data_attivita . 'T' . $oraInizio,
                'end' => $m->data_attivita . 'T' . $oraFine,
                'color' => $eventColor,
                'backgroundColor' => $eventColor,
                'borderColor' => $eventColor,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'targa' => $stringaTarghe, // Passato al tooltip del calendario
                    'cliente' => $cliente,
                    'indirizzo' => $m->indirizzo ?? '',
                    'stato' => $m->stato_completamento ?? '',
                    'descrizione' => $m->descrizione ?? '',
                    'personale' => $titoloPersonale, 
                    'color' => $eventColor
                ]
            ];
        }
        return $events;
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

    public function ___actionEventsjson($start = null, $end = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($start === null) $start = Yii::$app->request->get('start');
        if ($end === null) $end = Yii::$app->request->get('end');

        // Palette di colori professionali per i veicoli
        $colors = [
            '#3498db',
            '#2ecc71',
            '#9b59b6',
            '#f1c40f',
            '#e67e22',
            '#1abc9c',
            '#34495e',
            '#7f8c8d',
            '#ff9f43',
            '#54a0ff'
        ];

        try {
            // AGGIUNTO: with(['personali', 'veicolo']) per caricare i dati in una sola query
            $models = Planning::find()
                ->with(['personali', 'veicolo'])
                ->where(['between', 'data_attivita', substr($start, 0, 10), substr($end, 0, 10)])
                ->all();

            $events = [];
            foreach ($models as $m) {
                $oraInizio = $m->ora_inizio ? substr($m->ora_inizio, 0, 5) : '08:00';
                $oraFine = $m->ora_fine ? substr($m->ora_fine, 0, 5) : '18:00';

                // Assegna il colore in base all'ID del veicolo
                $colorIndex = ($m->veicolo_id) ? ($m->veicolo_id % count($colors)) : 0;
                $eventColor = ($m->veicolo_id) ? $colors[$colorIndex] : '#bdc3c7';

                // RECUPERO NOMI MULTIPLI: usiamo la funzione che abbiamo messo nel modello
                $titoloPersonale = $m->getNominativiCompleti();
                $cliente = $m->cliente ? $m->cliente->Descrizione : 'Nessun cliente';
                $events[] = [
                    'id' => $m->id,
                    // Titolo dinamico con Targa + Tutti i dipendenti assegnati
                    'title' => '[Giro ' . $m->giro . ']' . $cliente . "[" .
                     ($m->veicolo->targa ?? 'No Mezzo') . "] "
                     . $titoloPersonale,
                    'start' => $m->data_attivita . 'T' . $oraInizio,
                    'end' => $m->data_attivita . 'T' . $oraFine,
                    'color' => $eventColor,
                    'backgroundColor' => $eventColor,
                    'borderColor' => $eventColor,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'targa' => $m->veicolo->targa ?? '',
                        'cliente' => $cliente,
                        'indirizzo' => $m->indirizzo ?? '',
                        'stato' => $m->stato_completamento ?? '',
                        'descrizione' => $m->descrizione ?? '',
                        'personale' => $titoloPersonale, // Utile per i tooltip
                        'color' => $eventColor
                    ]
                ];
            }
            return $events;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function old_actionEventsjson($start = null, $end = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($start === null) $start = Yii::$app->request->get('start');
        if ($end === null) $end = Yii::$app->request->get('end');

        // Palette di colori professionali per i veicoli
        $colors = [
            '#3498db',
            '#2ecc71',
            '#9b59b6',
            '#f1c40f',
            '#e67e22',
            '#1abc9c',
            '#34495e',
            '#7f8c8d',
            '#ff9f43',
            '#54a0ff'
        ];

        try {
            $models = Planning::find()
                ->where(['between', 'data_attivita', substr($start, 0, 10), substr($end, 0, 10)])
                ->all();

            $events = [];
            foreach ($models as $m) {
                $oraInizio = $m->ora_inizio ? substr($m->ora_inizio, 0, 5) : '08:00';
                $oraFine = $m->ora_fine ? substr($m->ora_fine, 0, 5) : '18:00';

                // Assegna il colore in base all'ID del veicolo (cicla se sono più di 10)
                $colorIndex = ($m->veicolo_id) ? ($m->veicolo_id % count($colors)) : 0;
                $eventColor = ($m->veicolo_id) ? $colors[$colorIndex] : '#bdc3c7';

                // PlanningController.php -> actionEventsjson
                $events[] = [
                    'id' => $m->id,
                    'title' => "[" . ($m->veicolo->targa ?? 'No Mezzo') . "] " . ($m->personale->cognome ?? 'N/D'),
                    'start' => $m->data_attivita . 'T' . $oraInizio,
                    'end' => $m->data_attivita . 'T' . $oraFine,
                    // Usa 'color' per impostare sia sfondo che bordo in un colpo solo
                    'color' => $eventColor,          // Proprietà standard per tutto l'evento
                    'backgroundColor' => $eventColor, // Forza esplicitamente lo sfondo
                    'borderColor' => $eventColor,     // Forza il bordo
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'targa' => $m->veicolo->targa ?? '',
                        'indirizzo'=>$m->indirizzo ?? '',
                        'stato'=>$m->stato_completamento ?? '',
                        'descrizione'=>$m->descrizione ?? '',
                        'color' => $eventColor // Usato per la legenda
                    ]
                ];
            }
            return $events;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Visualizza il planning in modalità lista testuale (Vista Agenda).
     * @return string
     */
  public function actionList()
{
    $models = Planning::find()
        ->with(['personali', 'veicolo', 'cliente', 'dittaEsternaRel'])
        ->orderBy(['data_attivita' => SORT_DESC,'Giro' => SORT_ASC ])
        ->all();

    // Carichiamo le liste per le select
    $veicoliList = ArrayHelper::map(\app\models\Veicoli::find()->all(), 'id', function($m) {
        return $m->targa . ' - ' . $m->marca_modello;
    });
    $personaleList = ArrayHelper::map(\app\models\Personale::find()->all(), 'id', function($m) {
        return $m->cognome . ' ' . $m->nome;
    });
    $clientiList = ArrayHelper::map(\app\models\CF::find()->select(['Cd_CF', 'Descrizione'])->all(), 'Cd_CF', 'Descrizione');
    $ditteList = ArrayHelper::map(\app\models\DittaEsterna::find()->all(), 'codice', 'descrizione');

    return $this->render('list', [
        'models' => $models,
        'veicoliList' => $veicoliList,
        'personaleList' => $personaleList,
        'clientiList' => $clientiList,
        'ditteList' => $ditteList,
    ]);
}
    // Assicurati di avere questo in alto nel file: use app\models\Presenze;
/**
     * Recupera i dati delle Presenze in formato JSON per il calendario Dashboard.
     * Gestisce la tematizzazione degli eventi (Verde=Lavoro, Giallo=Ferie, Rosso=Altro).
     * * @param string|null $start Data inizio.
     * @param string|null $end Data fine.
     * @param integer|null $personale_id Filtro per singolo dipendente.
     * @return array
     */
    public function actionPresenzejson($start = null, $end = null, $personale_id = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if ($start === null) $start = Yii::$app->request->get('start');
        if ($end === null) $end = Yii::$app->request->get('end');

        try {
            // Ricordati di riattivare il between per le date se vuoi limitare il caricamento!
            $query = \app\models\Presenze::find()
                ->where(['between', 'data_presenza', substr($start, 0, 10), substr($end, 0, 10)]);

            if (!empty($personale_id)) {
                $query->andWhere(['personale_id' => $personale_id]);
            }

            $models = $query->all();
            $events = [];

            foreach ($models as $m) {
                $titolo = $m->personale->cognome . ' ' . $m->personale->nome;
                $dettaglio = "Ore: " . $m->ore_lavorate;

                // Normalizziamo il testo (maiuscolo e senza spazi extra) per evitare errori
                $tipo = trim(strtoupper($m->tipo_assenza ?? ''));

                // 1. LAVORO NORMALE (Se è vuoto, NULL, o c'è scritto "PRESENTE")
                if (empty($tipo) || $tipo === 'PRESENTE') {
                    $colore = '#198754'; // Verde

                    $ingresso = $m->ora_ingresso ? substr($m->ora_ingresso, 0, 5) : '--:--';
                    $uscita = $m->ora_uscita ? substr($m->ora_uscita, 0, 5) : '--:--';
                    $dettaglio = "Ingresso: $ingresso <br>Uscita: $uscita <br>Ore Totali: " . $m->ore_lavorate;
                }
                // 2. FERIE O PERMESSI (Accetta sigle o parole intere)
                elseif (in_array($tipo, ['FER', 'PER', 'FERIE', 'PERMESSO'])) {
                    $colore = '#ffc107'; // Giallo
                    $titolo .= ' (' . $m->tipo_assenza . ')';
                    $dettaglio = "Assenza giustificata: " . $m->tipo_assenza;
                }
                // 3. MALATTIA, INFORTUNIO O ALTRO
                else {
                    $colore = '#dc3545'; // Rosso
                    $titolo .= ' (' . $m->tipo_assenza . ')';
                    $dettaglio = "Assenza: " . $m->tipo_assenza;
                }

                $events[] = [
                    'id' => 'pres_' . $m->id,
                    'title' => $titolo,
                    'start' => $m->data_presenza,
                    'allDay' => true,
                    'color' => $colore,
                     
                    // Il testo sui quadratini gialli è meglio nero, su rosso e verde è meglio bianco
                    'textColor' => ($colore == '#ffc107') ? '#000000' : '#ffffff',
                    'extendedProps' => [
                        'dettaglio' => $dettaglio,
                        'pre_id' => $m->id  // <--- METTILO QUI DENTRO!
                    ]
                ];
            }
            return $events;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    /**
     * Aggiornamento in linea da DataTables via AJAX
     */

public function actionInlineUpdate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $id = $post['id'] ?? null;
        $field = $post['field'] ?? null;
        $value = $post['value'] ?? null;

        if (!$id || !$field) {
            return ['success' => false, 'error' => 'Parametri mancanti.'];
        }

        $model = \app\models\Planning::findOne($id);
        if (!$model) {
            return ['success' => false, 'error' => 'Attività non trovata.'];
        }

        // --- GESTIONE SPECIALE RELAZIONI MOLTI-A-MOLTI ---
        
        // 1. Personale (Dipendenti Multipli)
        if ($field === 'personale_ids') {
            $model->personale_ids = empty($value) ? [] : (is_array($value) ? $value : [$value]);
        } 
        // 2. Veicoli (Mezzi Multipli) - NUOVA GESTIONE
        else if ($field === 'veicoli_ids') {
            $model->veicoli_ids = empty($value) ? [] : (is_array($value) ? $value : [$value]);
        } 
        // Campi standard (Stringhe, Interi singoli)
        else {
            // Se il valore in arrivo è una stringa vuota, lo trasformiamo in NULL.
            // Questo impedisce a SQL Server di cercare chiavi primarie con stringhe vuote.
            if ($value === '') {
                $value = null;
            }
            
            $model->$field = $value;
        }

        if ($model->save()) {
            return ['success' => true];
        }

        // Se fallisce, restituiamo gli errori del modello
        $errors = \yii\helpers\Html::errorSummary($model, ['header' => '']);
        return ['success' => false, 'error' => strip_tags($errors)];
    }


    public function originalactionInlineUpdate()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $post = Yii::$app->request->post();

        $id = $post['id'] ?? null;
        $field = $post['field'] ?? null;
        $value = $post['value'] ?? null;

        if (!$id || !$field) {
            return ['success' => false, 'error' => 'Parametri mancanti.'];
        }

        $model = \app\models\Planning::findOne($id);
        if (!$model) {
            return ['success' => false, 'error' => 'Attività non trovata.'];
        }

        // GESTIONE SPECIALE RELAZIONE 1 a N (Personale)
     // GESTIONE SPECIALE RELAZIONE 1 a N (Personale)
        if ($field === 'personale_ids') {
            $model->personale_ids = empty($value) ? [] : (is_array($value) ? $value : [$value]);
        } else {
            // --- FIX CHIAVE ESTERNA ---
            // Se il valore in arrivo è una stringa vuota, lo trasformiamo in NULL.
            // Questo impedisce a SQL Server di cercare chiavi primarie con stringhe vuote.
            if ($value === '') {
                $value = null;
            }
            
            $model->$field = $value;
        }
        if ($model->save()) {
            return ['success' => true];
        }

        // Se fallisce, restituiamo gli errori del modello
        $errors = \yii\helpers\Html::errorSummary($model, ['header' => '']);
        return ['success' => false, 'error' => strip_tags($errors)];
    }


public function old_actionDuplicate($id)
{
    $original = $this->findModel($id);
    $newModel = new \app\models\Planning();
    
    $attributes = $original->attributes;
    unset($attributes['id']);
    $newModel->attributes = $attributes;
    $newModel->setIsNewRecord(true);
    $newModel->stato_completamento = 'Da Iniziare';

// Rileva se la chiamata è AJAX
    if (Yii::$app->request->isAjax) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        if ($newModel->save()) {
            if (!empty($original->personali)) {
                foreach ($original->personali as $p) {
                    $newModel->link('personali', $p);
                }
            }

            // --- LA MAGIA È QUI: Evitiamo che Yii2 rompa la pagina ricaricando jQuery e Bootstrap ---
            Yii::$app->assetManager->bundles = [
                'yii\web\JqueryAsset' => false,
                'yii\web\YiiAsset' => false,
                'yii\bootstrap5\BootstrapAsset' => false,
                'yii\bootstrap5\BootstrapPluginAsset' => false,
            ];

            // Ricreiamo le liste
            $veicoliList = \yii\helpers\ArrayHelper::map(\app\models\Veicoli::find()->where("UPPER(stato_veicolo) = 'DISPONIBILE'")->all(), 'id', function($model) { return $model->targa . ' - ' . $model->marca_modello; });
            $personaleList = \yii\helpers\ArrayHelper::map(\app\models\Personale::find()->all(), 'id', function($model) { return $model->cognome . ' ' . $model->nome; });
            $clientiList = \yii\helpers\ArrayHelper::map(\app\models\CF::find()->all(), 'Cd_CF', 'Descrizione');
            $ditteList = \yii\helpers\ArrayHelper::map(\app\models\DittaEsterna::find()->all(), 'codice', 'descrizione');

            // Genera la riga
            $html = $this->renderAjax('_riga', [
                'm' => $newModel,
                'veicoliList' => $veicoliList,
                'personaleList' => $personaleList,
                'clientiList' => $clientiList,
                'ditteList' => $ditteList,
                'highlight' => true
            ]);

            return ['success' => true, 'html' => $html]; 
        }
        return ['success' => false, 'error' => implode(', ', $newModel->getFirstErrors())];
    }
}
public function actionStampa($data = null)
{
    if (!$data) {
        $data = date('Y-m-d');
    }

    $models = \app\models\Planning::find()
        ->with(['veicolo', 'cliente', 'personali']) 
        ->where(['data_attivita' => $data])
        ->orderBy(['veicolo_id' => SORT_ASC, 'giro' => SORT_ASC])
        ->all();

    $planningRaggruppato = [];
    foreach ($models as $m) {
        $key = $m->veicolo ? $m->veicolo->marca_modello . " (" . $m->veicolo->targa . ")" : "Nessun Mezzo";
        
        if (!isset($planningRaggruppato[$key])) {
            $planningRaggruppato[$key] = [
                'attivita' => [],
                'nomi_personale' => [] 
            ];
        }
        
        // CORREZIONE: Inseriamo il modello dentro la sotto-chiave 'attivita'
        $planningRaggruppato[$key]['attivita'][] = $m;

        // Raccogliamo i nomi distinti
        foreach ($m->personali as $p) {
            $nomeCompleto = strtoupper($p->cognome . ' ' . $p->nome);
            $planningRaggruppato[$key]['nomi_personale'][$p->id] = $nomeCompleto;
        }
    }

    $content = $this->renderPartial('_pdf_quotidiano', [
        'planningRaggruppato' => $planningRaggruppato,
        'data' => $data,
    ]);

    $pdf = new Pdf([
        'mode' => Pdf::MODE_UTF8, 
        'format' => Pdf::FORMAT_A4, 
        'orientation' => Pdf::ORIENT_PORTRAIT, 
        'destination' => Pdf::DEST_BROWSER, 
        'content' => $content,  
        'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
        'cssInline' => '
            .pdf-container { font-family: "Helvetica", "Arial", sans-serif; }
            .table-main { border: 1px solid #000; width: 100%; border-collapse: collapse; }
            .table-main th, .table-main td { border: 1px solid #000; padding: 8px; }
            .header-info { border: 1px solid #000; margin-bottom: 10px; width: 100%; }
            .header-info td { border: 1px solid #000; padding: 5px; }
            .bg-grey { background-color: #f2f2f2; font-weight: bold; }
        ',
        'options' => ['title' => 'Procedure Giornaliere'],
        'methods' => [ 
            'SetHeader' => ['Ufficio 2000||Stampato il: ' . date('d/m/Y H:i')], 
            'SetFooter' => ['Pagina {PAGENO}'],
        ]
    ]);

    return $pdf->render(); 
}


public function actionDuplicate($id)
{
    $original = $this->findModel($id);
    $newModel = new \app\models\Planning();
    
    $attributes = $original->attributes;
    unset($attributes['id']); // Rimuoviamo la chiave primaria per generarne una nuova
    $newModel->attributes = $attributes;
    $newModel->setIsNewRecord(true);
    $newModel->stato_completamento = 'Da Iniziare';

    // Rileva se la chiamata è AJAX
    if (Yii::$app->request->isAjax) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        try {
            if ($newModel->save()) {
                // 1. Duplicazione dei Dipendenti associati (Logica Esistente)
                if (!empty($original->personali)) {
                    foreach ($original->personali as $p) {
                        $newModel->link('personali', $p);
                    }
                }

                // --- NUOVA LOGICA: Duplicazione dei Veicoli Multipli associati ---
                if (!empty($original->veicoliListRel)) {
                    foreach ($original->veicoliListRel as $v) {
                        $newModel->link('veicoliListRel', $v);
                    }
                }

                // Disattiviamo i bundle per evitare conflitti nell'HTML iniettato
                Yii::$app->assetManager->bundles = [
                    'yii\web\JqueryAsset' => false,
                    'yii\web\YiiAsset' => false,
                    'yii\bootstrap5\BootstrapAsset' => false,
                    'yii\bootstrap5\BootstrapPluginAsset' => false,
                ];

                // Ricreiamo le liste per la riga dinamica
                $veicoliList = \yii\helpers\ArrayHelper::map(\app\models\Veicoli::find()->where("UPPER(stato_veicolo) = 'DISPONIBILE'")->all(), 'id', function($model) { return $model->targa . ' - ' . $model->marca_modello; });
                $personaleList = \yii\helpers\ArrayHelper::map(\app\models\Personale::find()->all(), 'id', function($model) { return $model->cognome . ' ' . $model->nome; });
                $clientiList = \yii\helpers\ArrayHelper::map(\app\models\CF::find()->all(), 'Cd_CF', 'Descrizione');
                $ditteList = \yii\helpers\ArrayHelper::map(\app\models\DittaEsterna::find()->all(), 'codice', 'descrizione');

                // Genera la riga
                $html = $this->renderAjax('_riga', [
                    'm' => $newModel,
                    'veicoliList' => $veicoliList,
                    'personaleList' => $personaleList,
                    'clientiList' => $clientiList,
                    'ditteList' => $ditteList,
                    'highlight' => true
                ]);

                // Passiamo anche il nuovo ID al frontend
                return ['success' => true, 'html' => $html, 'newId' => $newModel->id]; 
            }
            
            $erroriFormattati = [];
            foreach ($newModel->getErrors() as $attribute => $errors) {
                $erroriFormattati[] = $newModel->getAttributeLabel($attribute) . ': ' . implode(', ', $errors);
            }
            return [
                'success' => false, 
                'error' => 'Errore di Validazione: ' . implode(' | ', $erroriFormattati)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false, 
                'error' => 'Eccezione Database: ' . $e->getMessage()
            ];
        }
    }

    // --- LOGICA PER RICHIESTE STANDARD (NON AJAX) ---
    if ($newModel->save()) {
        // Colleghiamo i personali nella richiesta standard
        if (!empty($original->personali)) {
            foreach ($original->personali as $p) {
                $newModel->link('personali', $p);
            }
        }
        
        // --- NUOVA LOGICA: Colleghiamo i veicoli multipli anche nella richiesta standard ---
        if (!empty($original->veicoliListRel)) {
            foreach ($original->veicoliListRel as $v) {
                $newModel->link('veicoliListRel', $v);
            }
        }
        
        Yii::$app->session->setFlash('success', "Record duplicato con successo. Ti trovi sulla nuova scheda.");
        return $this->redirect(['view', 'id' => $newModel->id]);
    } else {
        Yii::$app->session->setFlash('error', "Impossibile duplicare il record. Errori: " . implode(', ', $newModel->getFirstErrors()));
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }
}

public function actionCopyDay()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;

        if ($request->isAjax && $request->isPost) {
            $sourceDate = $request->post('source_date');
            $targetDate = $request->post('target_date');

            if (empty($sourceDate) || empty($targetDate)) {
                return ['success' => false, 'error' => 'Entrambe le date sono obbligatorie.'];
            }

            // Troviamo tutte le attività della giornata di origine caricando anche le relazioni
            $plannings = \app\models\Planning::find()
                ->with(['personali', 'veicoliListRel'])
                ->where(['data_attivita' => $sourceDate])
                ->all();

            if (empty($plannings)) {
                return ['success' => false, 'error' => 'Nessuna attività trovata nella data di origine selezionata.'];
            }

            $count = 0;
            // Usiamo una transazione DB: o salva tutto correttamente, o annulla tutto (evita dati a metà)
            $transaction = Yii::$app->db->beginTransaction();

            try {
                foreach ($plannings as $original) {
                    $newModel = new \app\models\Planning();
                    
                    $attributes = $original->attributes;
                    unset($attributes['id']); // Rimuoviamo la chiave primaria
                    
                    $newModel->attributes = $attributes;
                    $newModel->setIsNewRecord(true);
                    
                    // Modifichiamo i dati chiave per la nuova giornata
                    $newModel->data_attivita = $targetDate;
                    $newModel->stato_completamento = 'Da Iniziare';

                    if ($newModel->save()) {
                        // 1. Duplichiamo le assegnazioni del Personale
                        if (!empty($original->personali)) {
                            foreach ($original->personali as $p) {
                                $newModel->link('personali', $p);
                            }
                        }
                        
                        // 2. Duplichiamo le assegnazioni dei Veicoli
                        if (!empty($original->veicoliListRel)) {
                            foreach ($original->veicoliListRel as $v) {
                                $newModel->link('veicoliListRel', $v);
                            }
                        }
                        $count++;
                    } else {
                        $transaction->rollBack();
                        $errors = \yii\helpers\Html::errorSummary($newModel, ['header' => '']);
                        return ['success' => false, 'error' => 'Errore durante la copia: ' . strip_tags($errors)];
                    }
                }
                
                $transaction->commit();
                return ['success' => true, 'message' => "Operazione completata! Sono state duplicate $count attività."];
                
            } catch (\Exception $e) {
                $transaction->rollBack();
                return ['success' => false, 'error' => 'Errore critico: ' . $e->getMessage()];
            }
        }
        
        return ['success' => false, 'error' => 'Richiesta non valida.'];
    }

}
