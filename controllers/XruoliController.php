<?php

namespace app\controllers;

use Yii;
use app\models\Xruoli;
use app\models\XRuoliSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * XruoliController implements the CRUD actions for Xruoli model.
 */
class XruoliController extends Controller
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
     * Lists all Xruoli models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new XRuoliSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Xruoli model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cd_ruolo)
    {
        return $this->render('view', [
            'model' => $this->findModel($cd_ruolo),
        ]);
    }

    /**
     * Creates a new Xruoli model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Xruoli();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $searchModel = new XRuoliSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination = false;
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('createaj', [
                'model' => $model,
            ]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionCreateaj()
    {
        $model = new Xruoli();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => true];
            }
        }
       
            return $this->renderAjax('createaj', [
                'model' => $model,
            ]);
         
    }
    /**
     * Updates an existing Xruoli model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($cd_ruolo)
    {
        $model = $this->findModel($cd_ruolo);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'cd_ruolo' => $model->cd_ruolo]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Xruoli model.
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
     * Finds the Xruoli model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Xruoli the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Xruoli::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
