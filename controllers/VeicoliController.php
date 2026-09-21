<?php

namespace app\controllers;

use Yii;
use app\models\Veicoli;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\AllFiles;
/**
 * VeicoliController implements the CRUD actions for Veicoli model.
 */
class VeicoliController extends Controller
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
     * Lists all Veicoli models.
     * @return mixed
     */
    public function actionIndex()
    {
        // Recuperiamo tutti i veicoli dal database
        $models = \app\models\Veicoli::find()->all();

        // Passiamo la variabile 'models' alla vista index
        return $this->render('index', [
            'models' => $models,
        ]);
    }

    /**
     * Displays a single Veicoli model.
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
     * Creates a new Veicoli model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Veicoli();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Veicoli model.
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
     * Deletes an existing Veicoli model.
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
     * Finds the Veicoli model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Veicoli the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Veicoli::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function xxxxxxactionUploadFile($id)
    {
        $model = $this->findModel($id);
        $fileModel = new \app\models\AllFiles();

        if ($fileModel->load(Yii::$app->request->post())) {
            $uploadedFile = \yii\web\UploadedFile::getInstance($fileModel, 'f_content');

            if ($uploadedFile && $fileModel->upload($uploadedFile)) {
                $fileModel->id_padre = $id;
                $fileModel->entita = 'veicoli';
                $fileModel->nomefile = $uploadedFile->baseName;
                $fileModel->estensione = $uploadedFile->extension;

                // LEGGI IL CONTENUTO BINARIO DEL FILE
                $path = '../web/uploads/' . str_replace(' ', '_', $uploadedFile->baseName) . '.' . $uploadedFile->extension;
                $fileModel->f_content = file_get_contents($path);

                if ($fileModel->save(false)) {
                    Yii::$app->session->setFlash('success', "File caricato e salvato nel database.");
                    return $this->redirect(['view', 'id' => $id]);
                }
            }
        }

 

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('upload', [
            'model' => $model,
            'fileModel' => $fileModel,
        ]);
        }
        return $this->render('upload', [
            'model' => $model,
            'fileModel' => $fileModel,
        ]);


    }
    public function actionUploadFile($id)
{
    $model = $this->findModel($id);
    $fileModel = new \app\models\AllFiles();

    if ($fileModel->load(Yii::$app->request->post())) {
        $uploadedFile = \yii\web\UploadedFile::getInstance($fileModel, 'f_content');

        if ($uploadedFile && $fileModel->upload($uploadedFile)) {
            $fileModel->id_padre = $id;
            $fileModel->entita = 'veicoli';
            $fileModel->nomefile = $uploadedFile->baseName;
            $fileModel->estensione = $uploadedFile->extension;
 // Salviamo anche la tipologia documento
            // Salvataggio contenuto binario
            $path = '../web/uploads/' . str_replace(' ', '_', $uploadedFile->baseName) . '.' . $uploadedFile->extension;
            if (file_exists($path)) {
                $fileModel->f_content = file_get_contents($path);
            }

            if ($fileModel->save( )) {
                Yii::$app->session->setFlash('success', "File caricato con successo.");
                return $this->redirect(['view', 'id' => $id]);
            }else {
        // Se NON salva, stampa gli errori per capire cosa c'è che non va
       die( "Errore salvataggio: " . implode(', ', $fileModel->getFirstErrors()));
    }

        }
    }

    // Se è una richiesta AJAX (dalla modale), usa renderAjax
    if (Yii::$app->request->isAjax) {
        return $this->renderAjax('upload', [
            'model' => $model,
            'fileModel' => $fileModel,
        ]);
    }

    return $this->render('upload', [
        'model' => $model,
        'fileModel' => $fileModel,
    ]);
}
    
}
