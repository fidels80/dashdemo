<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Personale;
use app\models\Tipologiapresenza;
use yii\helpers\ArrayHelper;



/**
 * ReportController gestisce la generazione della reportistica avanzata del sistema.
 * Fornisce strumenti per l'analisi dei costi, delle presenze del personale
 * e del monitoraggio delle attività della flotta veicoli.
 */
class ReportController extends Controller
{

/**
     * Visualizza la Dashboard principale dei report (Index).
     * Questa pagina funge da menu di scelta per l'utente tra i vari report disponibili.
     * * @return string Il rendering della vista 'index'.
     */

// NUOVA AZIONE PER LA DASHBOARD DEI REPORT
    public function actionIndex()
    {
        return $this->render('index');
    }
    /**
     * Genera il Report dettagliato delle Presenze e dei relativi Costi.
     * Estrae i dati incrociando le ore lavorate con le tariffe orarie personalizzate dei dipendenti.
     * * @return string Il rendering della vista 'presenze' con i dati filtrati.
     */
    public function actionPresenze()
    {
        // 1. Raccogliamo i parametri dal form (GET)
        $req = Yii::$app->request;
        $dal = $req->get('dal', date('Y-m-01')); // Default: primo del mese
        $al = $req->get('al', date('Y-m-t'));    // Default: fine del mese
        $dipendente_id = $req->get('personale_id'); // Può essere un array se usiamo multi-select
        $causale = $req->get('causale');

        // 2. Costruiamo la query dinamicamente
        $query = (new \yii\db\Query())
            ->select([
                'p.data_presenza',
                'dip.cognome',
                'dip.nome',
                'p.ora_ingresso',
                'p.ora_uscita',
                'p.ore_lavorate',
                'p.tipo_assenza',
                'tp.descrizione as desc_causale',
                'dip.tariffa_oraria',
                '(p.ore_lavorate * dip.tariffa_oraria) as costo_totale'
            ])
            ->from('presenze p')
            ->leftJoin('personale dip', 'dip.id = p.personale_id')
            ->leftJoin('tipologia_presenza tp', 'tp.codice = p.tipo_assenza')
            ->where(['between', 'p.data_presenza', $dal, $al]);

        // Aggiungiamo i filtri opzionali solo se l'utente li ha compilati
        if (!empty($dipendente_id)) {
            $query->andWhere(['p.personale_id' => $dipendente_id]);
        }
        if (!empty($causale)) {
            $query->andWhere(['p.tipo_assenza' => $causale]);
        }

        $query->orderBy(['p.data_presenza' => SORT_DESC, 'dip.cognome' => SORT_ASC]);
        
        // Eseguiamo la query
        $dati = $query->all();

        // 3. Prepariamo le liste per le tendine del filtro
        $listaDipendenti = ArrayHelper::map(Personale::find()->where(['stato_attivo' => 1])->orderBy('cognome')->all(), 'id', function($m) { return $m->cognome . ' ' . $m->nome; });
        $listaCausali = ArrayHelper::map(Tipologiapresenza::find()->orderBy('descrizione')->all(), 'codice', function($m) { return $m->codice . ' - ' . $m->descrizione; });

        return $this->render('presenze', [
            'dati' => $dati,
            'dal' => $dal,
            'al' => $al,
            'dipendente_id' => $dipendente_id,
            'causale' => $causale,
            'listaDipendenti' => $listaDipendenti,
            'listaCausali' => $listaCausali,
        ]);
    }

/**
     * Genera il Report del Planning e dell'attività della Flotta Veicoli.
     * Permette di analizzare lo storico delle assegnazioni mezzi e personale per luogo e stato attività.
     * * @return string Il rendering della vista 'planning' con i dati filtrati.
     */
    public function actionPlanning()
    {
        // 1. Raccogliamo i parametri dal form (GET)
        $req = Yii::$app->request;
        $dal = $req->get('dal', date('Y-m-01')); 
        $al = $req->get('al', date('Y-m-t'));    
        $dipendente_id = $req->get('personale_id'); // Array multi-select
        $veicolo_id = $req->get('veicolo_id');      // Array multi-select
        $stato = $req->get('stato');                // Array multi-select

        // 2. Costruiamo la query dinamicamente
        $query = (new \yii\db\Query())
            ->select([
                'pl.data_attivita',
                'pl.ora_inizio',
                'pl.ora_fine',
                'pl.indirizzo',
                'pl.descrizione',
                'pl.stato_completamento',
                'dip.cognome',
                'dip.nome',
                'v.targa',
                'v.marca_modello'
            ])
            ->from('planning pl')
            ->leftJoin('personale dip', 'dip.id = pl.personale_id')
            ->leftJoin('veicoli v', 'v.id = pl.veicolo_id')
            ->where(['between', 'pl.data_attivita', $dal, $al]);

        // Applichiamo i filtri se selezionati
        if (!empty($dipendente_id)) {
            $query->andWhere(['pl.personale_id' => $dipendente_id]);
        }
        if (!empty($veicolo_id)) {
            $query->andWhere(['pl.veicolo_id' => $veicolo_id]);
        }
        if (!empty($stato)) {
            $query->andWhere(['pl.stato_completamento' => $stato]);
        }

        $query->orderBy(['pl.data_attivita' => SORT_DESC, 'pl.ora_inizio' => SORT_ASC]);
        
        // Eseguiamo la query
        $dati = $query->all();

        // 3. Prepariamo le liste per le tendine
        $listaDipendenti = ArrayHelper::map(Personale::find()->where(['stato_attivo' => 1])->orderBy('cognome')->all(), 'id', function($m) { return $m->cognome . ' ' . $m->nome; });
        $listaVeicoli = ArrayHelper::map(\app\models\Veicoli::find()->where(['!=', 'stato_veicolo', 'Dismesso'])->orderBy('targa')->all(), 'id', function($m) { return $m->targa . ' (' . $m->marca_modello . ')'; });
        $listaStati = [
            'Pianificato' => 'Pianificato',
            'In Corso' => 'In Corso',
            'Completato' => 'Completato',
            'Annullato' => 'Annullato',
        ];

        return $this->render('planning', [
            'dati' => $dati,
            'dal' => $dal,
            'al' => $al,
            'dipendente_id' => $dipendente_id,
            'veicolo_id' => $veicolo_id,
            'stato' => $stato,
            'listaDipendenti' => $listaDipendenti,
            'listaVeicoli' => $listaVeicoli,
            'listaStati' => $listaStati,
        ]);
    }
}