<?php

namespace app\controllers;

use Yii;
use app\models\Items;
use app\models\ItemsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ItemsController implements the CRUD actions for Items model.
 */
class ItemsController extends Controller
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
     * Lists all Items models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->getuser();

        $searchModel = new ItemsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Items model.
     * @param integer $id
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
     * Creates a new Items model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->getuser();

        $model = new Items();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Items model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->getuser();

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Items model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->
        $this->getuser();
findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Items model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Items the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $this->getuser();
        if (($model = Items::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


           public function getuser(){
$usrid = Yii::$app->user->Id;

if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['level', 'cd_cli','moduli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
//->AsArray();
$nmod = (str_replace('app\controllers', '', str_replace('Controller', '', __CLASS__)));
$nmod = (str_replace('\\', '', $nmod));

$mn=(new \yii\db\Query())
        ->select(['voce', 'url','Nmodulo'])
        ->from('xmenu')
        ->where(['Nmodulo' => $nmod])
        ->one();
}

 $arrmod=unserialize($ris['moduli']);
 yii::error($arrmod);
 yii::error($md['Nmodulo']));

$go=1;
if ($ris['level'] <>100) {
    foreach ($arrmod as  $value) {
        # code...yii::error($arrmod);
yii::error($value);

        if ($value==$md['Nmodulo']) {
            $go=1;
        }
    }
}else{
    $go=1;
}
//var_dump($ris);
if (Yii::$app->user->isGuest || $ris['level'] == null) {

$messaggio =
    "<h1>Attenzione</h1>\n\n"
    . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";

exit($messaggio);

}

if($go==0){

$messaggio =
    "<h1>Attenzione</h1>\n\n"
    . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";

exit($messaggio);



}


}
}
