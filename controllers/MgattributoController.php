<?php

namespace app\controllers;

use Yii;
use app\models\MgAttributoArticolo;
use app\models\MgArticolo;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

/**
 * CRUD degli attributi variante articolo: marche, modelli, taglie, colori.
 * Tabella unica discriminata dal tipo.
 */
class MgattributoController extends Controller
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

    public function actionIndex($tipo = null)
    {
        $query = MgAttributoArticolo::find();
        if ($tipo && array_key_exists($tipo, MgAttributoArticolo::opzioniTipo())) {
            $query->andWhere(['tipo' => $tipo]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['tipo' => SORT_ASC, 'descrizione' => SORT_ASC]),
            'pagination' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'tipi' => MgAttributoArticolo::opzioniTipo(),
            'tipoSelezionato' => $tipo,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate($tipo = null, $from = null)
    {
        $model = new MgAttributoArticolo();
        $model->attivo = true;
        if ($tipo && array_key_exists($tipo, MgAttributoArticolo::opzioniTipo())) {
            $model->tipo = $tipo;
        }

        if ($from !== null && ($source = MgAttributoArticolo::findOne((int) $from)) !== null) {
            $model = \app\components\Duplicate::copy($source);
            if ($tipo && array_key_exists($tipo, MgAttributoArticolo::opzioniTipo())) {
                $model->tipo = $tipo;
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'tipo' => $model->tipo]);
        }

        return $this->render('create', [
            'model' => $model,
            'tipi' => MgAttributoArticolo::opzioniTipo(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', 'tipo' => $model->tipo]);
        }

        return $this->render('update', [
            'model' => $model,
            'tipi' => MgAttributoArticolo::opzioniTipo(),
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $colonne = [
            MgAttributoArticolo::TIPO_MARCA => 'id_marca',
            MgAttributoArticolo::TIPO_MODELLO => 'id_modello',
            MgAttributoArticolo::TIPO_TESSUTO => 'id_tessuto',
            MgAttributoArticolo::TIPO_TAGLIA => 'id_taglia',
            MgAttributoArticolo::TIPO_COLORE => 'id_colore',
        ];
        $col = $colonne[$model->tipo] ?? null;
        if ($col && MgArticolo::find()->where([$col => $id])->exists()) {
            Yii::$app->session->setFlash('error',
                'Impossibile eliminare l\'attributo: è utilizzato da uno o più articoli.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->delete();
        return $this->redirect(['index', 'tipo' => $model->tipo]);
    }

    /**
     * Creazione rapida di un attributo (AJAX), usata dalla form articolo.
     */
    public function actionCreateAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'error' => 'Richiesta non valida.'];
        }

        $model = new MgAttributoArticolo();
        $model->attivo = true;
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            return [
                'success' => true,
                'attributo' => [
                    'id' => (int) $model->id,
                    'tipo' => $model->tipo,
                    'codice' => $model->codice,
                    'descrizione' => $model->descrizione,
                    'etichetta' => $model->etichetta,
                ],
            ];
        }

        return ['success' => false, 'errors' => $model->getErrors()];
    }

    protected function findModel($id)
    {
        if (($model = MgAttributoArticolo::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Attributo articolo non trovato.');
    }
}
