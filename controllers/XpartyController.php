<?php

namespace app\controllers;

use Yii;
use app\models\Xparty;
use app\models\XpartySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * XpartyController implements the CRUD actions for Xparty model.
 */
class XpartyController extends Controller
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
     * Lists all Xparty models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new XpartySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Xparty model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($cd_party)
    {
        return $this->render('view', [
            'model' => $this->findModel($cd_party),
        ]);
    }

    /**
     * Creates a new Xparty model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Xparty();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $searchModel = new XpartySearch();
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
        $model = new Xparty();

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
     * Updates an existing Xparty model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($cd_party)
    {
        $model = $this->findModel($cd_party);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $searchModel = new XpartySearch();
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
     * Deletes an existing Xparty model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($cd_party)
    {
        $this->findModel($cd_party)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Xparty model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Xparty the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($cd_party)
    {
        if (($model = Xparty::findOne($cd_party)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
