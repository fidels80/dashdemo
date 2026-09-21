<?php

namespace app\controllers;

use Yii;
use app\models\Tipologiapresenza;
use app\models\TipologiapresenzaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TipologiapresenzaController implements the CRUD actions for Tipologiapresenza model.
 */
class TipologiapresenzaController extends Controller
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
     * Lists all Tipologiapresenza models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TipologiapresenzaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tipologiapresenza model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($codice)
    {
        return $this->render('view', [
            'model' => $this->findModel($codice),
        ]);
    }

    /**
     * Creates a new Tipologiapresenza model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tipologiapresenza();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'codice' => $model->codice]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Tipologiapresenza model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($codice)
    {
        $model = $this->findModel($codice);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'codice' => $model->codice]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tipologiapresenza model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($codice)
    {
        $this->findModel($codice)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Tipologiapresenza model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Tipologiapresenza the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($codice)
    {
        if (($model = Tipologiapresenza::findOne($codice)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
