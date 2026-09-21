<?php

namespace app\controllers;

use Yii;
use app\models\Uecarticoli;
use app\models\UecarticoliSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UecarticoliController implements the CRUD actions for Uecarticoli model.
 */
class UecarticoliController extends Controller
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
     * Lists all Uecarticoli models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->getuser();
        $searchModel = new UecarticoliSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Uecarticoli model.
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
     * Creates a new Uecarticoli model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->getuser();
        $model = new Uecarticoli();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Uecarticoli model.
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
     * Deletes an existing Uecarticoli model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->getuser();
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Uecarticoli model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Uecarticoli the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Uecarticoli::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionList($q = null, $id = null)
    {
        $this->getuser();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        if (!is_null($q)) {
            // $query = new Query;
            // $query->select('Cd_Ar as id,[Cd_Ar]+\'-\'+[Descrizione] as text')
            //select (['ID', 'CONCAT(ID,\' \',Descrizione) AS text'])
            //        ->from('AR')
            //    ->where(['like', 'descrizione', $q])
            //    ->OrWhere(['like','Cd_Ar',$q])
            //    ->limit(20);
            //$command = $query->createCommand();
            //$data = $command->queryAll();

            $artdett = Uecarticoli::find()
                ->select(['[codice] as id', '[descrizione] as text'])
                ->where(['like', 'descrizione', $q])
                ->orWhere(['like', 'codice', $q])
                ->asArray()
                ->limit(20)
                ->all();

            $out['results'] = array_values($artdett);
        } elseif ($id > 0) {
            $out['results'] = ['id' => $id, 'text' =>
            Uecarticoli::find($id)->descrizione];
        }
        return $out;
    }


    public function getuser()
    {
        $usrid = Yii::$app->user->Id;
        if (null !== $usrid) {
            $ris = (new \yii\db\Query())
                ->select(['level', 'cd_cli', 'moduli'])
                ->from('user')
                ->where(['id' => $usrid])
                ->one();
            $nmod = (str_replace('app\controllers', '', str_replace('Controller', '', __CLASS__)));
            $nmod = (str_replace('\\', '', $nmod));
            $nmod = strtoupper($nmod);
            $mn   = (new \yii\db\Query())
                ->select(['voce', 'url', 'Nmodulo'])
                ->from('xmenu')
                ->where(['upper(Nmodulo)' => strtoupper($nmod)])
                ->one();
        }
        $arrmod = unserialize($ris['moduli'] ?? '');
        $go     = 0;
        if (($ris['level'] ?? 0) != 100) {
            if (is_array($arrmod)) {
                foreach ($arrmod as $value) {
                    if ($value=='Articoli Uni'){
                        $go=1;
                    }
                }
            }
        } else {
            $go = 1;
        }
        if (Yii::$app->user->isGuest || null == $ris['level']) {
            $messaggio =
                "<h1>Attenzione</h1>\n\n"
                . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";
            exit($messaggio);
        }
        if (0 == $go) {
            $messaggio =
                "<h1>Attenzione</h1>\n\n"
                . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";
            exit($messaggio);
        }
    }


}
