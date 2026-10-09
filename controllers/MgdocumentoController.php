<?php

namespace app\controllers;

use Yii;
use app\models\MgDocumento;
use app\models\MgDocumentoRiga;
use app\models\MgDocumentoRigaDettaglio;
use app\models\MgTipoDocumento;
use app\models\MgLotto;
use app\models\MgMatricola;
use app\models\MgAnagrafica;
use app\models\MgArticolo;
use app\models\MgMetodoPagamento;
use app\models\MgScadenza;
use app\models\MgAliquotaIva;
use app\models\MgAttributoArticolo;
use app\models\MgUnitaMisura;
use app\models\MgMagazzino;
use app\models\MgMovimentoMagazzino;
use app\models\MgSottocommessa;
use app\models\Rapportini;
use app\components\FatturaElettronica;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Gestione documenti del microgestionale (testata + righe + scadenze).
 */
class MgdocumentoController extends Controller
{
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

    public function actionIndex()
    {
        $request = Yii::$app->request;

        $query = MgDocumento::find();

        $idTipo = $request->get('id_tipo');
        $anno = $request->get('anno');
        $idAna = $request->get('id_anagrafica');
        $q = trim((string) $request->get('q'));

        if (!empty($idTipo)) {
            $query->andWhere(['id_tipo' => $idTipo]);
        }
        if (!empty($anno)) {
            $query->andWhere(['anno' => $anno]);
        }
        if (!empty($idAna)) {
            $query->andWhere(['id_anagrafica' => $idAna]);
        }
        if ($q !== '') {
            $query->andWhere(['or',
                ['like', 'descrizione', $q],
                ['like', 'codice_tipo', $q],
                ['like', 'numero', $q],
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->with('anagrafica')->orderBy(['anno' => SORT_DESC, 'numero' => SORT_DESC]),
            'pagination' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'tipi' => MgTipoDocumento::map(),
            'anagrafiche' => MgAnagrafica::map(),
            'filters' => ['id_tipo' => $idTipo, 'anno' => $anno, 'id_anagrafica' => $idAna, 'q' => $q],
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate($from = null)
    {
        $model = new MgDocumento();
        $model->data = date('Y-m-d');
        $model->anno = (int) date('Y');

        if (Yii::$app->request->get('id_tipo')) {
            $model->id_tipo = Yii::$app->request->get('id_tipo');
            $model->numero = MgDocumento::proponiNumero($model->id_tipo, $model->anno, $model->data);
        }

        $righe = [];
        if ($from !== null && ($source = MgDocumento::findOne((int) $from)) !== null) {
            $model = \app\components\Duplicate::copy($source);
            $model->numero = MgDocumento::proponiNumero($model->id_tipo, $model->anno, $model->data);
            $model->totale = 0;
            foreach ($source->righe as $riga) {
                $copia = \app\components\Duplicate::copy($riga);
                $copia->id_documento = null;
                $copia->id_rapportino = null;
                $copieDettagli = [];
                foreach ($riga->dettagli as $dettaglio) {
                    $copiaDettaglio = \app\components\Duplicate::copy($dettaglio);
                    $copiaDettaglio->id_documento_riga = null;
                    $copieDettagli[] = $copiaDettaglio;
                }
                $copia->populateRelation('dettagli', $copieDettagli);
                $righe[] = $copia;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save(false)) {
                $this->saveRighe($model, Yii::$app->request->post('righe', []));
                $model->generaScadenze();
                return $this->redirect([ 'index' ]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'tipi' => MgTipoDocumento::mapAttivi(),
            'tipiCreaScadenze' => $this->creaScadenzePerTipo(),
            'tipiDestinazione' => $this->destinazioniPerTipo(),
            'anagrafiche' => $this->anagrafichePerTipo($model->id_tipo),
            'anagraficheMetodi' => $this->metodiAnagrafichePerTipo($model->id_tipo),
            'anagraficheIva' => $this->ivaAnagrafichePerTipo($model->id_tipo),
            'metodi' => MgMetodoPagamento::mapAttivi(),
            'aliquote' => MgAliquotaIva::mapAttivi(),
            'unita' => MgUnitaMisura::mapAttivi(),
            'sottocommesse' => MgSottocommessa::mapEtichette(),
            'magazzini' => MgMagazzino::mapAttivi(),
            'tipiMagazzini' => MgTipoDocumento::mapMagazzini(),
            'tipiMostraVarianti' => MgTipoDocumento::mapMostraVarianti(),
            'tipiPrelevaRapportini' => MgTipoDocumento::mapFlag('preleva_rapportini'),
            'tipiCreaArticoli' => MgTipoDocumento::mapFlag('crea_articoli'),
            'tipiCreaAnagrafiche' => MgTipoDocumento::mapFlag('crea_anagrafiche'),
            'tipiMostraMatrice' => MgTipoDocumento::mapFlag('mostra_matrice'),
            'tipiGestioneSeriali' => MgTipoDocumento::mapFlag('gestione_seriali'),
            'tipiGestioneDataConsegna' => MgTipoDocumento::mapFlag('gestione_data_consegna'),
            'tipiGestioneLotti' => MgTipoDocumento::mapFlag('gestione_lotti'),
            'modelliMatrice' => $this->modelliConArticoli(),
            'righe' => $righe,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->hasScadenzePagate()) {
            Yii::$app->session->setFlash('error',
                'Il documento non è modificabile: esistono scadenze già pagate. Annullare il pagamento per poterlo modificare.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save(false)) {
                $this->saveRighe($model, Yii::$app->request->post('righe', []));
                $model->generaScadenze();
                return $this->redirect([ 'index' ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'tipi' => MgTipoDocumento::mapAttivi(),
            'tipiCreaScadenze' => $this->creaScadenzePerTipo(),
            'tipiDestinazione' => $this->destinazioniPerTipo(),
            'anagrafiche' => $this->anagrafichePerTipo($model->id_tipo),
            'anagraficheMetodi' => $this->metodiAnagrafichePerTipo($model->id_tipo),
            'anagraficheIva' => $this->ivaAnagrafichePerTipo($model->id_tipo),
            'metodi' => MgMetodoPagamento::mapAttivi(),
            'aliquote' => MgAliquotaIva::mapAttivi(),
            'unita' => MgUnitaMisura::mapAttivi(),
            'sottocommesse' => MgSottocommessa::mapEtichette(),
            'magazzini' => MgMagazzino::mapAttivi(),
            'tipiMagazzini' => MgTipoDocumento::mapMagazzini(),
            'tipiMostraVarianti' => MgTipoDocumento::mapMostraVarianti(),
            'tipiPrelevaRapportini' => MgTipoDocumento::mapFlag('preleva_rapportini'),
            'tipiCreaArticoli' => MgTipoDocumento::mapFlag('crea_articoli'),
            'tipiCreaAnagrafiche' => MgTipoDocumento::mapFlag('crea_anagrafiche'),
            'tipiMostraMatrice' => MgTipoDocumento::mapFlag('mostra_matrice'),
            'tipiGestioneSeriali' => MgTipoDocumento::mapFlag('gestione_seriali'),
            'tipiGestioneDataConsegna' => MgTipoDocumento::mapFlag('gestione_data_consegna'),
            'tipiGestioneLotti' => MgTipoDocumento::mapFlag('gestione_lotti'),
            'modelliMatrice' => $this->modelliConArticoli(),
            'righe' => $model->righe,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->hasScadenzePagate()) {
            Yii::$app->session->setFlash('error',
                'Il documento non è eliminabile: esistono scadenze già pagate.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        // Il numero torna libero: eliminando la testata le righe/scadenze seguono (CASCADE).
        $rapportini = MgDocumentoRiga::find()
            ->select('id_rapportino')
            ->where(['id_documento' => $model->id])
            ->andWhere(['not', ['id_rapportino' => null]])
            ->column();

        $model->delete();

        if (!empty($rapportini)) {
            Rapportini::updateAll(['evaso' => 0], ['id' => array_values($rapportini)]);
        }

        return $this->redirect(['index']);
    }

    /**
     * Genera (e restituisce) l'XML della fattura elettronica del documento.
     * Con $download=1 il file viene proposto in download, altrimenti è
     * mostrato inline (anteprima).
     */
    public function actionGeneraXml($id, $download = 0)
    {
        $model = $this->findModel($id);

        if (!$model->tipo || !$model->tipo->elettronico) {
            Yii::$app->session->setFlash('error',
                'Il tipo documento non è configurato come documento elettronico.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        try {
            $fe = new FatturaElettronica($model);
            $xml = $fe->genera();
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error',
                'Impossibile generare l\'XML: ' . $e->getMessage());
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->headers->set('Content-Type', 'application/xml; charset=UTF-8');
        if ($download) {
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $fe->nomeFile() . '"');
        } else {
            $response->headers->set('Content-Disposition', 'inline; filename="' . $fe->nomeFile() . '"');
        }

        return $xml;
    }

    /**
     * Cambia lo stato di una scadenza tra 'aperta' e 'pagata'.
     */
    public function actionToggleScadenza($id)
    {
        $scadenza = MgScadenza::findOne($id);
        if ($scadenza === null) {
            throw new NotFoundHttpException('Scadenza non trovata.');
        }

        $scadenza->stato = ($scadenza->stato === 'pagata') ? 'aperta' : 'pagata';
        $scadenza->save(false, ['stato']);

        return $this->redirect(['view', 'id' => $scadenza->id_documento]);
    }

    /**
     * Restituisce il prossimo numero libero per tipo/anno/data (AJAX).
     */
    public function actionProponiNumero($id_tipo, $anno = null, $data = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $anno = $anno ?: (int) date('Y');
        $data = $data ?: date('Y-m-d');

        return [
            'success' => true,
            'numero' => MgDocumento::proponiNumero($id_tipo, $anno, $data),
        ];
    }

    /**
     * Anagrafiche intestatarie coerenti con la destinazione del tipo documento (AJAX).
     */
    public function actionAnagrafiche($id_tipo = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $destinazione = $this->destinazionePerTipo($id_tipo);
        $rows = MgAnagrafica::find()
            ->with('aliquotaIva')
            ->where($destinazione === MgTipoDocumento::DEST_FORNITORE ? ['is_fornitore' => 1] : ['is_cliente' => 1])
            ->orderBy(['ragione_sociale' => SORT_ASC])
            ->all();

        $out = [];
        foreach ($rows as $a) {
            $out[] = [
                'id' => (int) $a->id,
                'ragione_sociale' => $a->ragione_sociale,
                'id_metodo_pagamento' => $a->id_metodo_pagamento ? (int) $a->id_metodo_pagamento : null,
                'iva_perc' => $a->aliquotaIva ? (float) $a->aliquotaIva->percentuale : null,
            ];
        }

        return ['success' => true, 'destinazione' => $destinazione, 'anagrafiche' => $out];
    }

    /**
     * Anteprima delle scadenze generate da un metodo di pagamento (AJAX).
     */
    public function actionAnteprimaScadenze($id_metodo = null, $data = null, $totale = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $metodo = $id_metodo ? MgMetodoPagamento::findOne($id_metodo) : null;
        if (!$metodo) {
            return ['success' => true, 'scadenze' => []];
        }

        $data = $data ?: date('Y-m-d');
        $totale = (float) $totale;

        $out = [];
        foreach ($metodo->calcolaScadenze($data) as $r) {
            $out[] = [
                'progressivo' => $r['progressivo'],
                'data' => $r['data'],
                'data_label' => date('d/m/Y', strtotime($r['data'])),
                'percentuale' => $r['percentuale'],
                'importo' => round($totale * $r['percentuale'] / 100, 2),
            ];
        }

        return ['success' => true, 'scadenze' => $out];
    }

    /**
     * Salva le righe del documento ricalcolando i totali e rigenerando i
     * movimenti di magazzino (le righe eliminate portano con sé i movimenti
     * per cascata).
     */
    private function saveRighe($model, $righe)
    {
        $precedenti = MgDocumentoRiga::find()
            ->select('id_rapportino')
            ->where(['id_documento' => $model->id])
            ->andWhere(['not', ['id_rapportino' => null]])
            ->column();

        MgDocumentoRiga::deleteAll(['id_documento' => $model->id]);

        $tipo = MgTipoDocumento::findOne($model->id_tipo);
        $codiceTipo = $model->codice_tipo ?: ($tipo ? $tipo->codice : null);

        $ord = 0;
        $nuovi = [];
        foreach ((array) $righe as $r) {
            if (empty($r['descrizione']) && empty($r['id_articolo']) && empty($r['id_rapportino'])) {
                continue;
            }
            $riga = new MgDocumentoRiga();
            $riga->id_documento = $model->id;
            $riga->id_articolo = !empty($r['id_articolo']) ? $r['id_articolo'] : null;
            $riga->id_rapportino = !empty($r['id_rapportino']) ? $r['id_rapportino'] : null;
            $riga->id_sottocommessa = !empty($r['id_sottocommessa']) ? $r['id_sottocommessa'] : null;
            $riga->id_magazzino_partenza = !empty($r['id_magazzino_partenza']) ? $r['id_magazzino_partenza'] : null;
            $riga->id_magazzino_arrivo = !empty($r['id_magazzino_arrivo']) ? $r['id_magazzino_arrivo'] : null;
            $riga->codice_articolo = $r['codice_articolo'] ?? null;
            $riga->codice_tipo = $codiceTipo;
            $riga->descrizione = $r['descrizione'] ?? null;
            $riga->id_unita_misura = !empty($r['id_unita_misura']) ? $r['id_unita_misura'] : null;
            $riga->um = !empty($r['um']) ? $r['um'] : null;
            $riga->taglia = !empty($r['taglia']) ? $r['taglia'] : null;
            $riga->colore = !empty($r['colore']) ? $r['colore'] : null;
            $riga->tessuto = !empty($r['tessuto']) ? $r['tessuto'] : null;
            $riga->fattore = ($r['fattore'] ?? '') === '' ? 1 : $r['fattore'];
            $riga->qta = ($r['qta'] ?? '') === '' ? 0 : $r['qta'];
            $riga->prezzo = ($r['prezzo'] ?? '') === '' ? 0 : $r['prezzo'];
            $riga->sconto = ($r['sconto'] ?? '') === '' ? 0 : $r['sconto'];

            // Aliquota IVA: se indicata prevale la percentuale dell'aliquota;
            // altrimenti si prova ad agganciare l'aliquota dalla percentuale.
            $riga->id_aliquota_iva = !empty($r['id_aliquota_iva']) ? (int) $r['id_aliquota_iva'] : null;
            if ($riga->id_aliquota_iva) {
                $aliquota = MgAliquotaIva::findOne($riga->id_aliquota_iva);
                $riga->iva = $aliquota ? (float) $aliquota->percentuale
                    : (($r['iva'] ?? '') === '' ? 0 : $r['iva']);
            } else {
                $riga->iva = ($r['iva'] ?? '') === '' ? 0 : $r['iva'];
                $aliquota = MgAliquotaIva::find()
                    ->where(['percentuale' => (float) $riga->iva])
                    ->orderBy(['attivo' => SORT_DESC, 'id' => SORT_ASC])
                    ->one();
                if ($aliquota) {
                    $riga->id_aliquota_iva = $aliquota->id;
                }
            }
            $riga->ordine = $ord++;

            // Codice articolo e unità di misura assenti: default dall'articolo.
            if (empty($riga->codice_articolo) || empty($riga->id_unita_misura) || empty($riga->um)) {
                $dati = MgMovimentoMagazzino::datiArticolo($riga);
                if (empty($riga->codice_articolo)) {
                    $riga->codice_articolo = $dati['codice_articolo'];
                }
                if (empty($riga->id_unita_misura)) {
                    $riga->id_unita_misura = $dati['id_unita_misura'];
                }
                if (empty($riga->um)) {
                    $riga->um = $dati['um'];
                }
            }

            $riga->save(false);
            $this->saveDettagli($riga, $r['dettagli'] ?? [], $tipo);
            MgMovimentoMagazzino::creaDaRiga($riga, $tipo);
            if ($riga->id_rapportino) {
                $nuovi[] = (string) $riga->id_rapportino;
            }
        }

        $model->calcolaTotale();
        $this->allineaEvasione($precedenti, $nuovi);
    }

    /**
     * Salva i dettagli (seriali / date consegna) di una riga documento.
     * Vengono gestiti solo se il tipo documento ha almeno uno dei due flag
     * attivi; le righe vuote vengono scartate.
     */
    private function saveDettagli($riga, $dettagli, $tipo)
    {
        if (!$tipo || (!$tipo->gestione_seriali && !$tipo->gestione_data_consegna && !$tipo->gestione_lotti)) {
            return;
        }

        MgDocumentoRigaDettaglio::deleteAll(['id_documento_riga' => $riga->id]);

        $ord = 0;
        foreach ((array) $dettagli as $d) {
            $seriale = trim((string) ($d['seriale'] ?? ''));
            $data = trim((string) ($d['data_consegna'] ?? ''));
            $idLotto = !empty($d['id_lotto']) ? (int) $d['id_lotto'] : null;
            $idMatricola = !empty($d['id_matricola']) ? (int) $d['id_matricola'] : null;
            if ($idMatricola !== null && $seriale === '') {
                $matricola = MgMatricola::findOne($idMatricola);
                if ($matricola) {
                    $seriale = (string) $matricola->matricola;
                }
            }
            if ($seriale === '' && $data === '' && $idLotto === null && $idMatricola === null) {
                continue;
            }

            $dettaglio = new MgDocumentoRigaDettaglio();
            $dettaglio->id_documento_riga = $riga->id;
            $dettaglio->seriale = $seriale !== '' ? $seriale : null;
            $dettaglio->id_matricola = $idMatricola;
            $dettaglio->id_lotto = $idLotto;
            $dettaglio->data_consegna = $data !== '' ? $data : null;
            $dettaglio->qta = ($d['qta'] ?? '') === '' ? 1 : $d['qta'];
            $dettaglio->ordine = $ord++;
            $dettaglio->save(false);
        }
    }

    /**
     * Allinea il flag "evaso" dei rapportini: evaso se collegato a una riga
     * del documento, libero se la riga è stata rimossa.
     */
    private function allineaEvasione($precedenti, $nuovi)
    {
        $precedenti = array_values(array_filter(array_map('strval', (array) $precedenti)));
        $nuovi = array_values(array_unique(array_filter(array_map('strval', (array) $nuovi))));

        if (!empty($nuovi)) {
            Rapportini::updateAll(['evaso' => 1], ['id' => $nuovi]);
        }

        $nuoviNorm = array_map('strtolower', $nuovi);
        $daLiberare = [];
        foreach ($precedenti as $p) {
            if (!in_array(strtolower($p), $nuoviNorm, true)) {
                $daLiberare[] = $p;
            }
        }
        if (!empty($daLiberare)) {
            Rapportini::updateAll(['evaso' => 0], ['id' => $daLiberare]);
        }
    }

    /**
     * Elenco dei rapportini prelevabili (non evasi) in formato JSON (AJAX).
     * Esclude quelli già collegati al documento in modifica.
     */
    public function actionRapportiniDisponibili($id_documento = null, $q = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $query = Rapportini::find()->where(['not', ['evaso' => 1]]);

        if (!empty($id_documento)) {
            $gia = MgDocumentoRiga::find()
                ->select('id_rapportino')
                ->where(['id_documento' => $id_documento])
                ->andWhere(['not', ['id_rapportino' => null]])
                ->column();
            if (!empty($gia)) {
                $query->andWhere(['not in', 'id', $gia]);
            }
        }

        $q = trim((string) $q);
        if ($q !== '') {
            $query->andWhere(['or',
                ['like', 'cd_cli', $q],
                ['like', 'commessa', $q],
                ['like', 'cd_art', $q],
                ['like', 'des_art', $q],
                ['like', 'note', $q],
            ]);
        }

        $rows = $query->orderBy(['data' => SORT_DESC, 'numero' => SORT_DESC])->all();

        $codiciCli = [];
        $codiciComm = [];
        $codiciArt = [];
        foreach ($rows as $r) {
            if ($r->cd_cli !== null && $r->cd_cli !== '') {
                $codiciCli[$r->cd_cli] = $r->cd_cli;
            }
            if ($r->commessa !== null && $r->commessa !== '') {
                $codiciComm[$r->commessa] = $r->commessa;
            }
            if ($r->cd_art !== null && $r->cd_art !== '') {
                $codiciArt[$r->cd_art] = $r->cd_art;
            }
        }

        $clienti = [];
        if (!empty($codiciCli)) {
            $clienti = MgAnagrafica::find()
                ->select(['codice', 'ragione_sociale'])
                ->where(['codice' => array_values($codiciCli)])
                ->indexBy('codice')
                ->column();
        }

        $commesse = [];
        if (!empty($codiciComm)) {
            $commesse = MgSottocommessa::find()
                ->select(['codice', 'descrizione'])
                ->where(['codice' => array_values($codiciComm)])
                ->indexBy('codice')
                ->column();
        }

        $artMap = [];
        if (!empty($codiciArt)) {
            foreach (MgArticolo::find()->where(['codice' => array_values($codiciArt)])->all() as $a) {
                $artMap[$a->codice] = $a;
            }
        }

        $out = [];
        foreach ($rows as $r) {
            $a = ($r->cd_art !== null && $r->cd_art !== '' && isset($artMap[$r->cd_art])) ? $artMap[$r->cd_art] : null;
            $out[] = [
                'id' => (string) $r->id,
                'numero' => (int) $r->numero,
                'data' => $r->data ? date('d/m/Y', strtotime((string) $r->data)) : '',
                'cd_cli' => $r->cd_cli,
                'cliente' => $clienti[$r->cd_cli] ?? $r->cd_cli,
                'commessa' => $r->commessa,
                'commessa_desc' => $commesse[$r->commessa] ?? $r->commessa,
                'cd_art' => $r->cd_art,
                'des_art' => $r->des_art,
                'qta' => (float) $r->qta,
                'ora_in' => $this->soloOra($r->ora_in),
                'ora_out' => $this->soloOra($r->ora_out),
                'note' => (string) $r->note,
                'id_articolo' => $a ? (int) $a->id : null,
                'prezzo' => $a ? (float) $a->prezzo : 0,
                'iva' => $a ? (float) $a->iva : 0,
                'um' => $a && $a->um ? $a->um : '',
            ];
        }

        return ['success' => true, 'rapportini' => $out];
    }

    /**
     * Dettaglio di un rapportino in HTML per la modale (AJAX).
     */
    public function actionRapportino($id)
    {
        $model = Rapportini::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Rapportino non trovato.');
        }

        return $this->renderAjax('_rapportino_dettaglio', [
            'model' => $model,
        ]);
    }

    /**
     * Versione stampabile del rapportino, aperta in una nuova scheda.
     */
    public function actionRapportinoStampa($id)
    {
        $model = Rapportini::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Rapportino non trovato.');
        }

        $this->layout = false;
        return $this->render('rapportino_stampa', [
            'model' => $model,
        ]);
    }

    /**
     * Estrae "HH:MM" da un valore TIME di SQL Server.
     */
    private function soloOra($valore)
    {
        if ($valore === null || $valore === '') {
            return '';
        }
        return substr((string) $valore, 0, 5);
    }

    /**
     * Destinazione ('cliente'|'fornitore') associata a un tipo documento.
     */
    private function destinazionePerTipo($idTipo)
    {
        if ($idTipo) {
            $tipo = MgTipoDocumento::findOne($idTipo);
            if ($tipo) {
                return $tipo->destinazione;
            }
        }
        return MgTipoDocumento::DEST_CLIENTE;
    }

    /**
     * Mappa id_anagrafica => id_metodo_pagamento per le anagrafiche coerenti col tipo.
     */
    private function metodiAnagrafichePerTipo($idTipo)
    {
        $destinazione = $this->destinazionePerTipo($idTipo);
        $rows = MgAnagrafica::find()
            ->where($destinazione === MgTipoDocumento::DEST_FORNITORE ? ['is_fornitore' => 1] : ['is_cliente' => 1])
            ->all();

        $map = [];
        foreach ($rows as $a) {
            $map[(int) $a->id] = $a->id_metodo_pagamento ? (int) $a->id_metodo_pagamento : null;
        }
        return $map;
    }

    /**
     * Mappa id_tipo => crea_scadenze (0/1) per la form documento.
     */
    private function creaScadenzePerTipo()
    {
        $rows = MgTipoDocumento::find()->select(['id', 'crea_scadenze'])->all();
        $map = [];
        foreach ($rows as $t) {
            $map[(int) $t->id] = (int) $t->crea_scadenze;
        }
        return $map;
    }

    /**
     * Mappa id_tipo => destinazione ('cliente'|'fornitore') per la form documento.
     */
    private function destinazioniPerTipo()
    {
        $rows = MgTipoDocumento::find()->select(['id', 'destinazione'])->all();
        $map = [];
        foreach ($rows as $t) {
            $map[(int) $t->id] = $t->destinazione;
        }
        return $map;
    }

    /**
     * Mappa id_anagrafica => percentuale aliquota IVA del soggetto (o null).
     */
    private function ivaAnagrafichePerTipo($idTipo)
    {
        $destinazione = $this->destinazionePerTipo($idTipo);
        $rows = MgAnagrafica::find()
            ->with('aliquotaIva')
            ->where($destinazione === MgTipoDocumento::DEST_FORNITORE ? ['is_fornitore' => 1] : ['is_cliente' => 1])
            ->all();

        $map = [];
        foreach ($rows as $a) {
            $map[(int) $a->id] = $a->aliquotaIva ? (float) $a->aliquotaIva->percentuale : null;
        }
        return $map;
    }

    /**
     * Mappa id_anagrafica => ragione_sociale per le anagrafiche coerenti col tipo.
     */
    private function anagrafichePerTipo($idTipo)
    {
        return MgAnagrafica::mapForDestinazione($this->destinazionePerTipo($idTipo));
    }

    /**
     * Matrice taglie per un modello (AJAX): righe = tessuto+colore, colonne = taglie.
     * Ogni cella riporta l'articolo (variante) da usare come riga documento.
     */
    public function actionMatriceTaglie($id_modello = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $modello = $id_modello ? MgAttributoArticolo::findOne($id_modello) : null;
        if (!$modello) {
            return ['success' => false, 'error' => 'Modello non valido.'];
        }

        $matrice = MgArticolo::matriceTaglie($modello->id);

        return [
            'success' => true,
            'modello' => ['id' => (int) $modello->id, 'descrizione' => $modello->descrizione],
            'taglie' => $matrice['taglie'],
            'righe' => $matrice['righe'],
        ];
    }

    /**
     * Modelli che hanno almeno un articolo, per la matrice taglie.
     */
    private function modelliConArticoli()
    {
        $ids = MgArticolo::find()
            ->select('id_modello')
            ->where(['not', ['id_modello' => null]])
            ->distinct()
            ->column();

        if (empty($ids)) {
            return [];
        }

        return ArrayHelper::map(
            MgAttributoArticolo::find()->where(['id' => $ids])->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            function ($m) {
                return $m->etichetta;
            }
        );
    }

    /**
     * Lotti disponibili per l'articolo di una riga (AJAX).
     */
    public function actionLotti($id_articolo = null, $codice_articolo = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = [];
        foreach (MgLotto::perArticolo($id_articolo, $codice_articolo) as $lotto) {
            $out[] = $this->lottoJson($lotto);
        }

        return ['success' => true, 'lotti' => $out];
    }

    /**
     * Creazione rapida di un lotto per l'articolo di una riga (AJAX).
     */
    public function actionCreaLotto()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'error' => 'Richiesta non valida.'];
        }

        $model = new MgLotto();
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            return ['success' => true, 'lotto' => $this->lottoJson($model)];
        }

        return ['success' => false, 'errors' => $model->getErrors()];
    }

    /**
     * Serializza un lotto per le risposte AJAX della form documento.
     */
    private function lottoJson($lotto)
    {
        $etichetta = $lotto->codice_lotto;
        if (!empty($lotto->descrizione)) {
            $etichetta .= ' - ' . $lotto->descrizione;
        }
        if (!empty($lotto->data_scadenza)) {
            $etichetta .= ' (scad. ' . date('d/m/Y', strtotime((string) $lotto->data_scadenza)) . ')';
        }

        return [
            'id' => (int) $lotto->id,
            'codice_lotto' => $lotto->codice_lotto,
            'descrizione' => $lotto->descrizione,
            'data_scadenza' => $lotto->data_scadenza ? date('d/m/Y', strtotime((string) $lotto->data_scadenza)) : '',
            'nota' => $lotto->nota,
            'etichetta' => $etichetta,
        ];
    }

    /**
     * Matricole disponibili per l'articolo di una riga (AJAX).
     */
    public function actionMatricole($id_articolo = null, $codice_articolo = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = [];
        foreach (MgMatricola::perArticolo($id_articolo, $codice_articolo) as $matricola) {
            $out[] = $this->matricolaJson($matricola);
        }

        return ['success' => true, 'matricole' => $out];
    }

    /**
     * Creazione rapida di una matricola per l'articolo di una riga (AJAX).
     */
    public function actionCreaMatricola()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'error' => 'Richiesta non valida.'];
        }

        $model = new MgMatricola();
        $model->attivo = true;
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            return ['success' => true, 'matricola' => $this->matricolaJson($model)];
        }

        return ['success' => false, 'errors' => $model->getErrors()];
    }

    /**
     * Serializza una matricola per le risposte AJAX della form documento.
     */
    private function matricolaJson($matricola)
    {
        return [
            'id' => (int) $matricola->id,
            'matricola' => $matricola->matricola,
            'descrizione' => $matricola->descrizione,
            'nota' => $matricola->nota,
            'etichetta' => $matricola->etichetta,
        ];
    }

    protected function findModel($id)
    {
        if (($model = MgDocumento::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Documento non trovato.');
    }
}
