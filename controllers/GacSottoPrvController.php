<?php

namespace app\controllers;

use Yii;
use app\models\Gacsottoprv;
use app\models\GacsottoprvSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\testaj;
use app\models\TestajSearch;

/**
 * GacsottoprvController implements the CRUD actions for Gacsottoprv model.
 */
class GacsottoprvController extends Controller
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
     * Lists all Gacsottoprv models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GacsottoprvSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Gacsottoprv model.
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
     * Creates a new Gacsottoprv model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Gacsottoprv();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_sub_prv]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Gacsottoprv model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
public function actionUpdate($id)
{
    $model = $this->findModel($id);

    // Verifica se il record è bloccato da un altro utente
    if ($model->is_locked && $model->locked_by != Yii::$app->user->identity->username) {
       // throw new \yii\web\ForbiddenHttpException('Il record è attualmente bloccato da un altro utente.');
    return $this->redirect(['view', 'id' => $model->id_sub_prv]);

    }

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
        // Rilascio dello stato di blocco dopo l'aggiornamento
        $model->is_locked = false;
        $model->locked_by = null;
        $model->save();

        return $this->redirect(['view', 'id' => $model->id_sub_prv]);
    }

    // Impostazione dello stato di blocco prima dell'aggiornamento
    $model->is_locked = true;
    $model->setAttribute('is_locked',true);
    $model->setAttribute('locked_by' , Yii::$app->user->identity->username);
   // return var_dump($errors = $model->getErrors()
    if ($model->save()==false){
        return var_dump($errors = $model->getErrors());
    }

    return $this->render('update', [
        'model' => $model,
    ]);
}


    /**
     * Deletes an existing Gacsottoprv model.
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
     * Finds the Gacsottoprv model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Gacsottoprv the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Gacsottoprv::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


     public function actionTestab()
    {
$searchModel  = new TestajSearch();
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

return $this->render('tabbed', [
    'searchModel'  => $searchModel,
    'tes' => $dataProvider,
]);

    }

}
