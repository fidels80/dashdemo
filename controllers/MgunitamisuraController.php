<?php

namespace app\controllers;

use Yii;
use app\models\MgUnitaMisura;
use app\models\MgArticoloUm;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

/**
 * CRUD delle unità di misura (codice, descrizione) condivise tra gli articoli.
 */
class MgunitamisuraController extends Controller
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
        $dataProvider = new ActiveDataProvider([
            'query' => MgUnitaMisura::find()->orderBy(['codice' => SORT_ASC]),
            'pagination' => false,
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate()
    {
        $model = new MgUnitaMisura();
        $model->attivo = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (MgArticoloUm::find()->where(['id_unita_misura' => $id])->exists()) {
            Yii::$app->session->setFlash('error',
                'Impossibile eliminare l\'unità di misura: è utilizzata da uno o più articoli.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Creazione rapida di un'unità di misura (AJAX), usata dalle form articolo/documento.
     */
    public function actionCreateAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'error' => 'Richiesta non valida.'];
        }

        $model = new MgUnitaMisura();
        $model->attivo = true;
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            return [
                'success' => true,
                'unita' => [
                    'id' => (int) $model->id,
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
        if (($model = MgUnitaMisura::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Unità di misura non trovata.');
    }
}
