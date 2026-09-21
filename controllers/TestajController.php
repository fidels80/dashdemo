<?php

namespace app\controllers;

use Yii;
use app\models\Testaj;
use app\models\TestajSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

/**
 * TestajController implements the CRUD actions for Testaj model.
 */
class TestajController extends Controller
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
     * Lists all Testaj models.
     * @return mixed
     */
    public function actionIndex($aj=null)
    {
        $searchModel = new TestajSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (!is_null($aj)){
$html = $this->renderajax('index', [
    'searchModel'  => $searchModel,
    'dataProvider' => $dataProvider,
    //'id'           => $id,
]);
Yii::$app->response->format = Response::FORMAT_JSON;
return ['content' => $html];


        }else{
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    }

    /**
     * Displays a single Testaj model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Testaj model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
$model = new Testaj();

if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    if ($model->validate()) {
        $testajModel             = new Testaj();
        $testajModel->attributes = $model->attributes;

        if ($testajModel->save()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => 'Errore durante il salvataggio dei dati.'];
        }
    } else {
        return ['success' => false, 'message' => 'Si sono verificati errori di convalida.'];
    }
}

// Render the form in the modal content
return $this->renderAjax('_form', [
    'model' => $model,
]);


    }

    /**
     * Updates an existing Testaj model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Testaj model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Testaj model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Testaj the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Testaj::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
