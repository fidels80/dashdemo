<?php

namespace app\controllers;

use app\models\Gacspese;
use app\models\GacspeseSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\widgets\Pjax;
/**
 * GacspeseController implements the CRUD actions for Gacspese model.
 */
class GacspeseController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Gacspese models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel  = new GacspeseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Gacspese model.
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
     * Creates a new Gacspese model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */


public function actionCreate2($id = null, $idspesa = null )
{
$model = new Gacspese();
$isAjax = Yii::$app->request->isAjax;

 if ($model->load(Yii::$app->request->post())   ) {
     if($model->save()){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['success' => true];}
    else {
      Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
return ['success' => false];

    }
 }

if ($isAjax) {
    // Restituisci solo la vista senza layout
    $this->layout = false;
    return $this->renderAjax('ajform', [
        'model' => $model,
        'id'    => $id,
    ]);
} else {
    return $this->render('create', [
        'model' => $model,
    ]);
}




}



   public function actionCreate($id = null, $idspesa = null)
{
    $model = new Gacspese();
    $isAjax = Yii::$app->request->isAjax;

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
         if ($isAjax) {
            Yii::$app->session->setFlash('success', 'Il dato è stato salvato con successo.');

           Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
      ///     return ['success' => true];
        } else {
            Yii::$app->session->setFlash('success', 'Il dato è stato salvato con successo.');

           $sourceUrl = Yii::$app->request->get('source', ['index']);
          return $this->redirect($sourceUrl);
      //  Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//return ['success' => true];

       }
    }

    if ($isAjax) {
        // Restituisci solo la vista senza layout
        $this->layout = false;
        return $this->renderAjax('create', [
            'model' => $model,
            'id'    => $id,
        ]);
    } else {
        return $this->render('create', [
            'model' => $model,
        ]);
    }
}
    /**
     * Updates an existing Gacspese model.
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
     * Deletes an existing Gacspese model.
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
     * Finds the Gacspese model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Gacspese the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Gacspese::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionTabsdata($id)
    {
        // return '/index.php?r=gacattivita%2Fmyatt%3Fid%3D1';
        $searchModel             = new GacspeseSearch();
        $searchModel->id_sub_prv = $id;

        $dataProvider = $searchModel->search(['id_sub_prv' => $id]);

        $html = $this->renderajax('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'id'           => $id,
        ]);
        return Json::encode($html);

    }

}
