<?php

namespace app\controllers;

use Yii;
use app\models\MgDocumento;
use app\models\MgDocumentoRiga;
use app\models\MgTipoDocumento;
use app\models\MgAnagrafica;
use app\models\MgArticolo;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Gestione documenti del microgestionale (testata + righe).
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
            'query' => $query->orderBy(['anno' => SORT_DESC, 'numero' => SORT_DESC]),
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
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'tipi' => MgTipoDocumento::mapAttivi(),
            'anagrafiche' => MgAnagrafica::map(),
            'articoli' => MgArticolo::map(),
            'righe' => [],
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save(false)) {
                $this->saveRighe($model, Yii::$app->request->post('righe', []));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'tipi' => MgTipoDocumento::mapAttivi(),
            'anagrafiche' => MgAnagrafica::map(),
            'articoli' => MgArticolo::map(),
            'righe' => $model->righe,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        // Il numero torna libero: eliminando la testata le righe seguono (CASCADE).
        $model->delete();

        return $this->redirect(['index']);
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
            $riga->qta = $r['qta'] ?? 0;
            $riga->prezzo = $r['prezzo'] ?? 0;
            $riga->sconto = $r['sconto'] ?? 0;
            $riga->iva = $r['iva'] ?? 0;
            $riga->ordine = $ord++;
            $riga->save(false);
        }

        $model->calcolaTotale();
    }

    protected function findModel($id)
    {
        if (($model = MgDocumento::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Documento non trovato.');
    }
}
