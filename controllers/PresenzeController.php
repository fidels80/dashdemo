<?php

namespace app\controllers;

use Yii;
use app\models\Presenze;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PresenzeController implements the CRUD actions for Presenze model.
 */
class PresenzeController extends Controller
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
     * Lists all Presenze models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Presenze(); // Modello vuoto per la form di inserimento
        $models = Presenze::find()->with('personale')->orderBy(['data_presenza' => SORT_DESC])->all();

        // Se la form viene inviata (salvataggio rapido)
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->refresh();
        }

        return $this->render('index', [
            'model' => $model,   // <--- Ora la variabile $model esiste!
            'models' => $models,
        ]);
    }
    /**isplays a single Presenze model.
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
     * Creates a new Presenze model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
   public function actionCreate($data_presenza = null, $personale_id = null)
    {
        $model = new Presenze();

        // Se ricevo i parametri dal doppio click, li pre-imposto nel modello!
        if ($data_presenza !== null) {
            $model->data_presenza = $data_presenza;
        }
        if ($personale_id !== null) {
            $model->personale_id = $personale_id;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Presenza registrata con successo.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing Presenze model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Presenze model.
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
     * Finds the Presenze model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Presenze the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Presenze::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
