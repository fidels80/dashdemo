<?php

namespace app\controllers;

use Yii;
use app\models\Doc_head;
use app\models\Doc_headSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Doc_headController implements the CRUD actions for Doc_head model.
 */
class Doc_headController extends Controller
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
     * Lists all Doc_head models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->getuser();

        $searchModel = new Doc_headSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Doc_head model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $this->getuser();

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Doc_head model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->getuser();

        $model = new Doc_head();

        if ($model->load(Yii::$app->request->post())  ) {
            $t = Yii::$app->runAction('log/set', ['data' => $model,
    'op' => $model->className() . '-->' . $this->action->id]);
$model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Doc_head model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->getuser();

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())  ) {

$t = Yii::$app->runAction('log/set', ['data' => $model,
    'op' => $model->className() . '-->' . $this->action->id]);
$model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionConferma($id)
    {
        $this->getuser();

        $model = $this->findModel($id);
        $model->setAttribute('confermato',1);
        $t = Yii::$app->runAction('log/set', ['data' => $model,
    'op' => $model->className() . '-->' . $this->action->id]);

        $model->save();
        return $this->redirect(['view', 'id' => $model->id]);
    }


    public function actionRifiuta($id)
    {
        $this->getuser();

        $model = $this->findModel($id);
        $model->setAttribute('rifiutato',1);
        $t = Yii::$app->runAction('log/set', ['data' => $model,
    'op' => $model->className() . '-->' . $this->action->id]);

        $model->save();
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing Doc_head model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->getuser();
$t = Yii::$app->runAction('log/set', ['data' => $model,
    'op' => $model->className() . '-->' . $this->action->id]);

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Doc_head model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Doc_head the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $this->getuser();
        if (($model = Doc_head::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


public function actionrifiuta_nota($id=null){

$this->getuser();

$model = $this->findModel($id);

/*
if ($model->load(Yii::$app->request->post())) {

    $t = Yii::$app->runAction('log/set', ['data' => $model,
        'op' => $model->className() . '-->' . $this->action->id]);
    //$model->save();

    return $this->redirect(['view', 'id' => $model->id]);
}

/*return $this->renderAjax('rifiuta_nota', [
    'model' => $model,'id'=>$id,
]);*/
return $this->render('index', [
    'model' => $model,
]);



}
    

public function getuser(){
$usrid = Yii::$app->user->Id;

if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['level', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
//->AsArray();

}
//var_dump($ris);
if (Yii::$app->user->isGuest || $ris['level'] == null) {

$messaggio =
    "<h1>Attenzione</h1>\n\n"
    . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";

exit($messaggio);

}


}
}
