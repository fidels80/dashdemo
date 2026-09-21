<?php

namespace app\controllers;

use Yii;
use app\models\Sottocommessa;
use app\models\SottocommessaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Json;
use app\models\DOCommessa;
/**
 * SottocommessaController implements the CRUD actions for Sottocommessa model.
 */
class SottocommessaController extends Controller
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
     * Lists all Sottocommessa models.
     * @return mixed
     */
   public function actionIndex()
{
    $searchModel = new SottocommessaSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    // Disabilita la paginazione di Yii per far lavorare DataTables su tutto il set
    $dataProvider->pagination = false;

    return $this->render('index', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
}

    /**
     * Displays a single Sottocommessa model.
     * @param string $id
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
     * Creates a new Sottocommessa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
public function actionCreate()
{
    $model = new Sottocommessa();

    if ($model->load(Yii::$app->request->post())) {
        //$model->UserIns = Yii::$app->user->identity->username;
        //$model->UserUpd = Yii::$app->user->identity->username;
        //$model->TimeIns = date('Y-m-d H:i:s');
        //$model->TimeUpd = date('Y-m-d H:i:s');
        
        if ($model->save()) {
            return $this->redirect(['index']);
        }
    }

    return $this->render('create', [
        'model' => $model,
    ]);
}

    /**
     * Updates an existing Sottocommessa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', ]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Sottocommessa model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Sottocommessa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Sottocommessa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Sottocommessa::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionGac(){
        return $this->render('gac');
    }

        public function actionTabsdata() {
            $searchModel  = new SottocommessaSearch();
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $html = $this->renderPartial('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
        return Json::encode($html);
    }
}

