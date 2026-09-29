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
     * Usa le pivot tables planning_personale e planning_veicoli (M2M).
     * Include la ditta esterna associata ad ogni attività.
     */
    public function actionPlanning()
    {
        $req = Yii::$app->request;
        $dal = $req->get('dal', date('Y-m-01'));
        $al = $req->get('al', date('Y-m-t'));
        $dipendente_id = $req->get('personale_id');
        $veicolo_id = $req->get('veicolo_id');
        $stato = $req->get('stato');
        $dittaEsterna = $req->get('ditta_esterna');

        // Query principale: un record per planning.
        // Cognomi/nomi e targhe/mezzi sono aggregati in due sotto-query separate:
        // joinandoli direttamente ogni targa verrebbe ripetuta una volta per
        // ogni dipendente (prodotto cartesiano).
        $persone = (new \yii\db\Query())
            ->select([
                'x.planning_id',
                new \yii\db\Expression("STRING_AGG(dip.cognome, ', ') WITHIN GROUP (ORDER BY dip.cognome, dip.nome) AS cognomi"),
                new \yii\db\Expression("STRING_AGG(dip.nome, ', ') WITHIN GROUP (ORDER BY dip.cognome, dip.nome) AS nomi"),
            ])
            ->from(['x' => (new \yii\db\Query())
                ->select('planning_id, personale_id')
                ->distinct()
                ->from('planning_personale')])
            ->leftJoin('personale dip', 'dip.id = x.personale_id')
            ->groupBy('x.planning_id');

        $mezzi = (new \yii\db\Query())
            ->select([
                'x.planning_id',
                new \yii\db\Expression("STRING_AGG(v.targa, ', ') WITHIN GROUP (ORDER BY v.targa) AS targhe"),
                new \yii\db\Expression("STRING_AGG(v.marca_modello, ', ') WITHIN GROUP (ORDER BY v.targa) AS mezzi"),
            ])
            ->from(['x' => (new \yii\db\Query())
                ->select('planning_id, veicolo_id')
                ->distinct()
                ->from('planning_veicoli')])
            ->leftJoin('veicoli v', 'v.id = x.veicolo_id')
            ->groupBy('x.planning_id');

        $query = (new \yii\db\Query())
            ->select([
                'pl.id',
                'pl.data_attivita',
                'pl.ora_inizio',
                'pl.ora_fine',
                'pl.indirizzo',
                'pl.descrizione',
                'sc.descrizione as stato_completamento',
                'sc.colore as stato_colore',
                'pl.giro',
                'de.descrizione as ditta_esterna_desc',
                'pers.cognomi',
                'pers.nomi',
                'mezzi.targhe',
                'mezzi.mezzi',
            ])
            ->from('planning pl')
            ->leftJoin('stato_completamento sc', 'sc.id = pl.stato_completamento_id')
            ->leftJoin('dittaesterna de', 'de.codice = pl.ditta_esterna')
            ->leftJoin(['pers' => $persone], 'pers.planning_id = pl.id')
            ->leftJoin(['mezzi' => $mezzi], 'mezzi.planning_id = pl.id')
            ->where(['between', 'pl.data_attivita', $dal, $al]);

        if (!empty($dipendente_id)) {
            $query->andWhere(['in', 'pl.id', (new \yii\db\Query())
                ->select('planning_id')->from('planning_personale')
                ->where(['personale_id' => $dipendente_id])
            ]);
        }
        if (!empty($veicolo_id)) {
            $query->andWhere(['in', 'pl.id', (new \yii\db\Query())
                ->select('planning_id')->from('planning_veicoli')
                ->where(['veicolo_id' => $veicolo_id])
            ]);
        }
        if (!empty($stato)) {
            $query->andWhere(['sc.id' => $stato]);
        }
        if (!empty($dittaEsterna)) {
            $query->andWhere(['pl.ditta_esterna' => $dittaEsterna]);
        }

        $query->orderBy(new \yii\db\Expression("pl.data_attivita ASC, pl.giro ASC, pers.cognomi ASC"));

        $dati = $query->all();

        // Liste per i filtri
        $listaDipendenti = ArrayHelper::map(
            Personale::find()->where(['stato_attivo' => 1])->orderBy('cognome')->all(),
            'id', function($m) { return $m->cognome . ' ' . $m->nome; }
        );
        $listaVeicoli = ArrayHelper::map(
            \app\models\Veicoli::find()->where(['!=', 'stato_veicolo', 'Dismesso'])->orderBy('targa')->all(),
            'id', function($m) { return $m->targa . ' (' . $m->marca_modello . ')'; }
        );
        $listaDitte = ArrayHelper::map(
            \app\models\DittaEsterna::find()->orderBy('descrizione')->all(),
            'codice', function($m) { return $m->codice . ' - ' . $m->descrizione; }
        );
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
            'dittaEsterna' => $dittaEsterna,
            'listaDipendenti' => $listaDipendenti,
            'listaVeicoli' => $listaVeicoli,
            'listaDitte' => $listaDitte,
            'listaStati' => $listaStati,
        ]);
    }

    /**
     * Report Consuntivo Ore per Ditta Esterna.
     * Mostra ore/giornate/dipendenti utilizzati per ogni ditta esterna,
     * incrociando planning -> planning_personale -> presenze -> personale.
     */
    public function actionConsuntivoDitta()
    {
        $req = Yii::$app->request;
        $dal = $req->get('dal', date('Y-m-01'));
        $al = $req->get('al', date('Y-m-t'));
        $dittaEsterna = $req->get('ditta_esterna');

        // Query: incrocia presenze con planning per ottenere la ditta esterna
        $query = (new \yii\db\Query())
            ->select([
                'de.codice as ditta_codice',
                'de.descrizione as ditta_descrizione',
                'dip.id as personale_id',
                'dip.cognome',
                'dip.nome',
                'dip.tariffa_oraria',
                new \yii\db\Expression('COUNT(DISTINCT p.data_presenza) AS giorni_lavorati'),
                new \yii\db\Expression('ROUND(SUM(p.ore_lavorate), 1) AS totale_ore'),
                new \yii\db\Expression('ROUND(SUM(p.ore_lavorate * dip.tariffa_oraria), 2) AS costo_totale'),
            ])
            ->from('presenze p')
            ->innerJoin('personale dip', 'dip.id = p.personale_id')
            ->innerJoin('planning_personale pp', 'pp.personale_id = dip.id')
            ->innerJoin('planning pl', 'pl.id = pp.planning_id AND pl.data_attivita = p.data_presenza')
            ->innerJoin('dittaesterna de', 'de.codice = pl.ditta_esterna')
            ->where(['between', 'pl.data_attivita', $dal, $al]);

        if (!empty($dittaEsterna)) {
            $query->andWhere(['pl.ditta_esterna' => $dittaEsterna]);
        }

        $query->groupBy('de.codice, de.descrizione, dip.id, dip.cognome, dip.nome, dip.tariffa_oraria');
        $query->orderBy(['de.descrizione' => SORT_ASC, 'dip.cognome' => SORT_ASC]);

        $dati = $query->all();

        // Riepilogo per ditta esterna
        $riepilogoDitte = [];
        foreach ($dati as $riga) {
            $codice = $riga['ditta_codice'];
            if (!isset($riepilogoDitte[$codice])) {
                $riepilogoDitte[$codice] = [
                    'descrizione' => $riga['ditta_descrizione'],
                    'totale_ore' => 0,
                    'totale_giorni' => 0,
                    'totale_dipendenti' => 0,
                    'totale_costo' => 0,
                    'dipendenti' => [],
                ];
            }
            $riepilogoDitte[$codice]['totale_ore'] += $riga['totale_ore'];
            $riepilogoDitte[$codice]['totale_giorni'] += $riga['giorni_lavorati'];
            $riepilogoDitte[$codice]['totale_costo'] += $riga['costo_totale'];
            $riepilogoDitte[$codice]['dipendenti'][$riga['personale_id']] = true;
        }
        foreach ($riepilogoDitte as &$rd) {
            $rd['totale_dipendenti'] = count($rd['dipendenti']);
            unset($rd['dipendenti']);
        }
        unset($rd);

        // KPI globali
        $totaleOreGlobali = array_sum(array_column($dati, 'totale_ore'));
        $totaleGiorniGlobali = count(array_unique(array_map(function($r) { return $r['personale_id'] . '_' . $r['ditta_codice']; }, $dati)));
        $totaleDipendentiGlobali = count(array_unique(array_column($dati, 'personale_id')));
        $totaleCostoGlobale = array_sum(array_column($dati, 'costo_totale'));

        // Query dettaglio attività: singole attività con cliente, indirizzo, note
        $queryDettaglio = (new \yii\db\Query())
            ->select([
                'de.descrizione as ditta_descrizione',
                'pl.data_attivita',
                'pl.giro',
                'dip.cognome',
                'dip.nome',
                'pl.cd_cf',
                'pl.indirizzo',
                'pl.descrizione as note_attivita',
                'pl.ora_inizio',
                'pl.ora_fine',
            ])
            ->from('planning pl')
            ->innerJoin('dittaesterna de', 'de.codice = pl.ditta_esterna')
            ->innerJoin('planning_personale pp', 'pp.planning_id = pl.id')
            ->innerJoin('personale dip', 'dip.id = pp.personale_id')
            ->where(['between', 'pl.data_attivita', $dal, $al]);

        if (!empty($dittaEsterna)) {
            $queryDettaglio->andWhere(['pl.ditta_esterna' => $dittaEsterna]);
        }

        $queryDettaglio->orderBy(['de.descrizione' => SORT_ASC, 'dip.cognome' => SORT_ASC, 'pl.data_attivita' => SORT_ASC, 'pl.giro' => SORT_ASC]);
        $datiDettaglio = $queryDettaglio->all();

        // Lookup clienti da db5 (tabella cf su ADB_UFFICIO2000)
        $codiciCf = array_unique(array_filter(array_column($datiDettaglio, 'cd_cf')));
        $mappaClienti = [];
        if (!empty($codiciCf)) {
            $cfQuery = (new \yii\db\Query())
                ->select(['Cd_CF', 'Descrizione'])
                ->from('cf')
                ->where(['in', 'Cd_CF', $codiciCf]);
            $cfRows = $cfQuery->all(Yii::$app->db5);
            foreach ($cfRows as $cfRow) {
                $mappaClienti[$cfRow['Cd_CF']] = $cfRow['Descrizione'];
            }
        }

        // Arricchisco i dati con il nome cliente
        foreach ($datiDettaglio as &$riga) {
            $riga['cliente'] = $mappaClienti[$riga['cd_cf']] ?? null;
        }
        unset($riga);

        $listaDitte = ArrayHelper::map(
            \app\models\DittaEsterna::find()->orderBy('descrizione')->all(),
            'codice', function($m) { return $m->codice . ' - ' . $m->descrizione; }
        );

        return $this->render('consuntivo_ditta', [
            'dati' => $dati,
            'datiDettaglio' => $datiDettaglio,
            'riepilogoDitte' => $riepilogoDitte,
            'dal' => $dal,
            'al' => $al,
            'dittaEsterna' => $dittaEsterna,
            'listaDitte' => $listaDitte,
            'totaleOreGlobali' => $totaleOreGlobali,
            'totaleDipendentiGlobali' => $totaleDipendentiGlobali,
            'totaleCostoGlobale' => $totaleCostoGlobale,
        ]);
    }

    /**
     * Report Attività con raggruppamento per squadra e expand/collapse.
     * Visualizzazione navigabile con filtri e tabella a gruppi collassabili.
     */
    public function actionRapportoAttivita()
    {
        $req = Yii::$app->request;
        $dal = $req->get('dal', date('Y-m-01'));
        $al = $req->get('al', date('Y-m-t'));
        $dipendente_id = $req->get('personale_id');
        $veicolo_id = $req->get('veicolo_id');
        $dittaEsterna = $req->get('ditta_esterna');

        $query = (new \yii\db\Query())
            ->select([
                'pl.id',
                'pl.data_attivita',
                'pl.ora_inizio',
                'pl.ora_fine',
                'pl.indirizzo',
                'pl.descrizione',
                'sc.descrizione as stato_completamento',
                'sc.colore as stato_colore',
                'pl.giro',
                'de.descrizione as ditta_esterna_desc',
                new \yii\db\Expression("STRING_AGG(CONCAT(dip.cognome, ' ', dip.nome), ', ') WITHIN GROUP (ORDER BY dip.cognome) AS nominativi"),
                new \yii\db\Expression("STRING_AGG(v.targa, ', ') WITHIN GROUP (ORDER BY dip.cognome) AS targhe"),
                'pl.cd_cf',
                'pl.qta_operai',
            ])
            ->from('planning pl')
            ->leftJoin('stato_completamento sc', 'sc.id = pl.stato_completamento_id')
            ->leftJoin('dittaesterna de', 'de.codice = pl.ditta_esterna')
            ->leftJoin('planning_personale pp', 'pp.planning_id = pl.id')
            ->leftJoin('personale dip', 'dip.id = pp.personale_id')
            ->leftJoin('planning_veicoli pv', 'pv.planning_id = pl.id')
            ->leftJoin('veicoli v', 'v.id = pv.veicolo_id')
            ->where(['between', 'pl.data_attivita', $dal, $al]);

        if (!empty($dipendente_id)) {
            $query->andWhere(['in', 'pl.id', (new \yii\db\Query())
                ->select('planning_id')->from('planning_personale')
                ->where(['personale_id' => $dipendente_id])
            ]);
        }
        if (!empty($veicolo_id)) {
            $query->andWhere(['in', 'pl.id', (new \yii\db\Query())
                ->select('planning_id')->from('planning_veicoli')
                ->where(['veicolo_id' => $veicolo_id])
            ]);
        }
        if (!empty($dittaEsterna)) {
            $query->andWhere(['pl.ditta_esterna' => $dittaEsterna]);
        }

        $query->groupBy('pl.id, pl.data_attivita, pl.ora_inizio, pl.ora_fine, pl.indirizzo, pl.descrizione, sc.descrizione, sc.colore, pl.giro, de.descrizione, pl.cd_cf, pl.qta_operai');
        $query->orderBy(new \yii\db\Expression('pl.data_attivita ASC, MIN(dip.cognome) ASC, pl.giro ASC'));

        $dati = $query->all();

        // Ordina e deduplica targhe (il JOIN le moltiplica)
        foreach ($dati as &$r) {
            $tArr = array_unique(array_filter(array_map('trim', explode(',', $r['targhe'] ?? ''))));
            sort($tArr);
            $r['targhe'] = implode(', ', $tArr);
        }
        unset($r);

        // Lookup clienti
        $codiciCf = array_unique(array_filter(array_column($dati, 'cd_cf')));
        $mappaClienti = [];
        if (!empty($codiciCf)) {
            $cfRows = (new \yii\db\Query())
                ->select(['Cd_CF', 'Descrizione'])
                ->from('cf')
                ->where(['in', 'Cd_CF', $codiciCf])
                ->all(Yii::$app->db5);
            foreach ($cfRows as $cfRow) {
                $mappaClienti[$cfRow['Cd_CF']] = $cfRow['Descrizione'];
            }
        }

        // Costruisci righe con nominativiKey per il raggruppamento
        $rows = [];
        foreach ($dati as $r) {
            $nominativi = trim($r['nominativi'] ?? '');
            $dittaNome = strtoupper($r['ditta_esterna_desc'] ?? '');
            $oprai = intval($r['qta_operai'] ?? 0);

            // Chiave di raggruppamento: stessi nominativi + stessa ditta
            $nomiArr = array_map('trim', explode(',', $nominativi));
            sort($nomiArr);
            $nominativiKey = implode('|', $nomiArr);
            if ($dittaNome) $nominativiKey .= '|DITTA:' . $dittaNome;

            $label = $nominativi ?: ($dittaNome ?: 'Senza assegnazione');
            if ($dittaNome && $nominativi) $label .= "\nDITTA: " . $dittaNome;
            if ($oprai > 0) $label .= "\nOPERAI: " . $oprai;

            $clienteNome = strtoupper($mappaClienti[$r['cd_cf']] ?? '');

            $rows[] = [
                'id' => $r['id'],
                'data_attivita' => $r['data_attivita'],
                'dataFmt' => date('d/m/Y', strtotime($r['data_attivita'])),
                'giro' => $r['giro'] ?? '',
                'ora_inizio' => $r['ora_inizio'] ? date('H:i', strtotime($r['ora_inizio'])) : '-',
                'ora_fine' => $r['ora_fine'] ? date('H:i', strtotime($r['ora_fine'])) : '-',
                'cliente' => $clienteNome,
                'indirizzo' => $r['indirizzo'] ?? '',
                'note' => $r['descrizione'] ?? '',
                'stato' => $r['stato_completamento'] ?? 'N.D.',
                'stato_colore' => $r['stato_colore'] ?? '#6c757d',
                'targhe' => $r['targhe'] ?? '',
                'nominativi' => $label,
                'nominativiKey' => $nominativiKey,
            ];
        }

        // Raggruppa per squadra
        $grouped = [];
        $prevKey = null;
        foreach ($rows as $r) {
            if ($r['nominativiKey'] !== $prevKey) {
                $prevKey = $r['nominativiKey'];
                $grouped[] = [
                    'nominativi' => $r['nominativi'],
                    'targhe' => $r['targhe'],
                    'items' => [],
                ];
            }
            $grouped[count($grouped) - 1]['items'][] = $r;
        }

        // Liste per filtri
        $listaDipendenti = ArrayHelper::map(
            Personale::find()->where(['stato_attivo' => 1])->orderBy('cognome')->all(),
            'id', function($m) { return $m->cognome . ' ' . $m->nome; }
        );
        $listaVeicoli = ArrayHelper::map(
            \app\models\Veicoli::find()->where(['!=', 'stato_veicolo', 'Dismesso'])->orderBy('targa')->all(),
            'id', function($m) { return $m->targa . ' (' . $m->marca_modello . ')'; }
        );
        $listaDitte = ArrayHelper::map(
            \app\models\DittaEsterna::find()->orderBy('descrizione')->all(),
            'codice', function($m) { return $m->codice . ' - ' . $m->descrizione; }
        );

        return $this->render('rapporto_attivita', [
            'rows' => $rows,
            'grouped' => $grouped,
            'dal' => $dal,
            'al' => $al,
            'dipendente_id' => $dipendente_id,
            'veicolo_id' => $veicolo_id,
            'dittaEsterna' => $dittaEsterna,
            'listaDipendenti' => $listaDipendenti,
            'listaVeicoli' => $listaVeicoli,
            'listaDitte' => $listaDitte,
        ]);
    }
}