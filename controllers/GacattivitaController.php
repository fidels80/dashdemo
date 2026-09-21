<?php

namespace app\controllers;

use Yii;
use app\models\Gacattivita;
use app\models\GacattivitaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

/**
 * GacattivitaController implements the CRUD actions for Gacattivita model.
 */
class GacattivitaController extends Controller
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
     * Lists all Gacattivita models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GacattivitaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Gacattivita model.
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
     * Creates a new Gacattivita model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id=null)
    {
        $model = new Gacattivita();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_attivita]);
        }


if (Yii::$app->request->isAjax){
//

return $this->renderAjax('create', [
            'model' => $model,
             'id'=>$id
        ]);

}else{
        return $this->render('create', [
            'model' => $model,
        ]);
    }


    }

    /**
     * Updates an existing Gacattivita model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_attivita]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Gacattivita model.
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

public function actionTabsdata($id){
   // return '/index.php?r=gacattivita%2Fmyatt%3Fid%3D1';
$searchModel  = new GacattivitaSearch();
$searchModel->id_sub_prv = $id;

$dataProvider = $searchModel->search(['id_sub_prv' => $id]);

 
$html= $this->renderajax('index', [
    'searchModel'  => $searchModel,
    'dataProvider' => $dataProvider,
    'id'=>$id
]);
return Json::encode($html);


}
    /**
     * Finds the Gacattivita model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Gacattivita the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Gacattivita::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');

    }
 


}
