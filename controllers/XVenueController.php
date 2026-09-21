<?php

namespace app\controllers;

use Yii;
use app\models\Xvenue;
use app\models\XvenueSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * XvenueController implements the CRUD actions for Xvenue model.
 */
class XvenueController extends Controller
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
     * Lists all Xvenue models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new XvenueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
$dataProvider->pagination=false;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Xvenue model.
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
     * Creates a new Xvenue model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Xvenue();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $searchModel = new XvenueSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination = false;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function actionCreateaj($èajax=null)
    {
        $model = new Xvenue();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->isAjax ||   ($èajax !== null && $èajax == 1) ) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return $this->redirect(
                    Yii::$app->request->referrer);
            }
        }

        return $this->renderAjax('createaj', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing Xvenue model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $searchModel = new XvenueSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination = false;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Xvenue model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        // Controlla se l'ID è presente nella tabella x_tappe.citta
        $exists = (new \yii\db\Query())
            ->from('adb_auxcoop.dbo.x_tappe')
            ->where(['citta' => $id])
            ->exists();

        if ($exists) {
            Yii::$app->session->setFlash('error', 'Impossibile eliminare: la città è utilizzata in una o più tappe.');
            return $this->redirect(['index']);
        }

        // Se non è referenziata, allora si può cancellare
        $model->delete();
        Yii::$app->session->setFlash('success', 'Record eliminato correttamente.');

        return $this->redirect(['index']);
    }


    /**
     * Finds the Xvenue model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Xvenue the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Xvenue::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionXcaricacitta($q = null)
    {
        $db = Yii::$app->db5;
        $query = new \yii\db\Query;
        $query->select(['cd_citta as id', 'descrizione as text'])
            ->from('x_citta')
            ->where(['like', 'descrizione', $q]);
           // ->limit(20);

        $command = $query->createCommand($db);
        $data = $command->queryAll();
        //var_dump($data); // Aggiungi questa linea per verificare
        //die();


        return \yii\helpers\Json::encode(['items' => $data]);
    }
}
