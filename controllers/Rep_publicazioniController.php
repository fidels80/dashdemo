<?php

namespace app\controllers;

use Yii;
use app\models\Rep_publicazioni;
use app\models\Rep_publicazioniSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Sc;
use app\models\ScSearch;

/**
 * Rep_publicazioniController implements the CRUD actions for Rep_publicazioni model.
 */
class Rep_publicazioniController extends Controller
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
     * Lists all Rep_publicazioni models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new Rep_publicazioniSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionPub_marca()
    {
        $this->getuser();

        $searchModel = new Rep_publicazioniSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('pub_marca', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            
        ]);
    }

        public function actionPub_data()
    {
        $this->getuser();

        $searchModel = new Rep_publicazioniSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('pub_data', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



   public function actionPub_commesse()
    {
        $this->getuser();

        $searchModel = new ScSearch();
        $queryParams['pageSize'] = 10;

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('pub_commesse', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }




    /**
     * Displays a single Rep_publicazioni model.
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
     * Creates a new Rep_publicazioni model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
    
    public function actionCreate()
    {
        $model = new Rep_publicazioni();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id_DORig]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    
     * Updates an existing Rep_publicazioni model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id_DORig]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    
     * Deletes an existing Rep_publicazioni model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    
     * Finds the Rep_publicazioni model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rep_publicazioni the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rep_publicazioni::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }



public function getuser(){
$usrid = Yii::$app->user->Id;

if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['level', 'cd_cli', 'moduli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
//->AsArray();
    $nmod = (str_replace('app\controllers', '', str_replace('Controller', '', __CLASS__)));
    $nmod = (str_replace('\\', '', $nmod));
//yii::error('---------{'.$nmod.'}----');

    $nmod = strtoupper($nmod);
//yii::error($nmod);
//die ($nmod);
    $mn = (new \yii\db\Query())
        ->select(['voce', 'url', 'Nmodulo'])
        ->from('xmenu')
        ->where(['upper(Nmodulo)' => strtoupper($nmod)])
        ->one();
}
 
$arrmod = unserialize($ris['moduli']??'');
 
//yii::warning($arrmod);
//yii::warning($mn);

$go = 0;
if (($ris['level'] ??0)!= 100) {
    if (is_array($arrmod)) {
        foreach ($arrmod as $value) {
            //   yii::error('---------{'.strtoupper($value).'}----{'.strtoupper($mn['voce']).'}----{');
            if (strtoupper($value) == strtoupper(($mn['voce']?? 'default value'))) {
                $go = 1;
            }
            if ($nmod=='DOC_HEAD' || strtoupper($value)=='ELENCO' ){
$go = 1;
//yii::warning(strtoupper($value));


            }
        }
    }

} else {
    $go = 1;
}
//var_dump($ris);
if (Yii::$app->user->isGuest || $ris['level'] == null) {

    $messaggio =
        "<h1>Attenzione</h1>\n\n"
        . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";

    exit($messaggio);

}

if ($go == 0) {

    $messaggio =
        "<h1>Attenzione</h1>\n\n"
        . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";

    exit($messaggio);

}



}

}
