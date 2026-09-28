<?php

namespace app\controllers;

use Yii;
use app\models\MgDocumento;
use app\models\MgDocumentoRiga;
use app\models\MgTipoDocumento;
use app\models\MgAnagrafica;
use app\models\MgArticolo;
use app\models\MgMetodoPagamento;
use app\models\MgScadenza;
use app\models\MgAliquotaIva;
use app\models\MgAttributoArticolo;
use app\models\MgUnitaMisura;
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

    public function actionCreate()
    {
        $model = new MgDocumento();
        $model->data = date('Y-m-d');
        $model->anno = (int) date('Y');

        if (Yii::$app->request->get('id_tipo')) {
            $model->id_tipo = Yii::$app->request->get('id_tipo');
            $model->numero = MgDocumento::proponiNumero($model->id_tipo, $model->anno, $model->data);
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
            'tipiMostraVarianti' => MgTipoDocumento::mapMostraVarianti(),
            'modelliMatrice' => $this->modelliConArticoli(),
            'righe' => [],
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
            'tipiMostraVarianti' => MgTipoDocumento::mapMostraVarianti(),
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
        $model->delete();

        return $this->redirect(['index']);
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
     * Salva le righe del documento ricalcolando i totali.
     */
    private function saveRighe($model, $righe)
    {
        MgDocumentoRiga::deleteAll(['id_documento' => $model->id]);

        $ord = 0;
        foreach ((array) $righe as $r) {
            if (empty($r['descrizione']) && empty($r['id_articolo'])) {
                continue;
            }
            $riga = new MgDocumentoRiga();
            $riga->id_documento = $model->id;
            $riga->id_articolo = !empty($r['id_articolo']) ? $r['id_articolo'] : null;
            $riga->codice_articolo = $r['codice_articolo'] ?? null;
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
            $riga->iva = ($r['iva'] ?? '') === '' ? 0 : $r['iva'];
            $riga->ordine = $ord++;
            $riga->save(false);
        }

        $model->calcolaTotale();
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

    protected function findModel($id)
    {
        if (($model = MgDocumento::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Documento non trovato.');
    }
}
