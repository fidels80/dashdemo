<?php

namespace app\controllers;

use Yii;
use app\models\Docommessa;
use app\models\DocommessaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
/**
 * DocommessaController implements the CRUD actions for Docommessa model.
 */
class DocommessaController extends Controller
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
     * Lists all Docommessa models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DocommessaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pagination'=>false
        ]);
    }

    /**
     * Displays a single Docommessa model.
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
     * Creates a new Docommessa model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Docommessa();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Cd_DOCommessa]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Docommessa model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Cd_DOCommessa]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Docommessa model.
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
     * Finds the Docommessa model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Docommessa the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Docommessa::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionCreateAjax()
{
    $model = new Docommessa();

    if ($model->load(Yii::$app->request->post())) {
        // Popoliamo i campi obbligatori di sistema
        $model->UserIns = Yii::$app->user->identity->username ?? 'admin';
      //  $model->TimeIns = date('Y-m-d H:i:s');
        $model->UserUpd = $model->UserIns;
      //  $model->TimeUpd = $model->TimeIns;

        if ($model->save()) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'success' => true,
                'id' => $model->Cd_DOCommessa, // Passiamo il codice appena creato
            ];
        }
    }

    return $this->renderAjax('_form', [
        'model' => $model,
    ]);
}
/**
     * AZIONE PER MODIFICARE UNA COMMESSA VIA AJAX (Dalla Modal)
     */
    public function actionUpdateAjax($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                return [
                    'success' => true,
                    'id' => $model->Cd_DOCommessa,
                ];
            }
        }

        return $this->renderAjax('_form', [
            'model' => $model,
        ]);
    }
}
