<?php

namespace app\controllers;

use Yii;
use app\models\Gacmateriali;
use app\models\GacmaterialiSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
 
use yii\helpers\Json;
use yii\web\Response;

/**
 * GacmaterialiController implements the CRUD actions for Gacmateriali model.
 */
class GacmaterialiController extends Controller
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
     * Lists all Gacmateriali models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GacmaterialiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Gacmateriali model.
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
     * Creates a new Gacmateriali model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id=null)
    {
        $model = new Gacmateriali();

if ($model->load(Yii::$app->request->post()) && $model->save()) {
    // Il salvataggio è avvenuto con successo
    // Puoi fare altre operazioni qui se necessario
if (Yii::$app->request->isAjax) {
//
    // Restituisci una risposta JSON per la gestione della risposta AJAX
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return ['success' => true, 'message' => 'Salvataggio avvenuto con successo!'];
} else {
    // Il salvataggio ha fallito o si è verificato un errore
    // Puoi fare altre operazioni qui se necessario

    // Restituisci una risposta JSON per la gestione della risposta AJAX
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return ['success' => false, 'message' => 'Si è verificato un errore durante il salvataggio. Riprova più tardi.'];
}
}

if (Yii::$app->request->isAjax) {
//

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
     * Updates an existing Gacmateriali model.
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
     * Deletes an existing Gacmateriali model.
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
     * Finds the Gacmateriali model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Gacmateriali the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Gacmateriali::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionTabsdata($id){
   // return '/index.php?r=gacattivita%2Fmyatt%3Fid%3D1';
$searchModel  = new GacmaterialiSearch();
$searchModel->id_sub_prv = $id;

$dataProvider = $searchModel->search(['id_sub_prv' => $id]);

 
$html= $this->renderajax('index', [
    'searchModel'  => $searchModel,
    'dataProvider' => $dataProvider,
    'id'=>$id
]);
return Json::encode($html);


}
}
