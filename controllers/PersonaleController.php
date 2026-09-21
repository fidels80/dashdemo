<?php

namespace app\controllers;

use Yii;
use app\models\Personale;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PersonaleController gestisce tutte le operazioni CRUD (Crea, Leggi, Aggiorna, Elimina) 
 * relative all'anagrafica del dipendente.
 * * Include la gestione delle tariffe personalizzate e lo stato di attività del personale
 * richiesti dalle specifiche di progetto.
 */
class PersonaleController extends Controller
{
/**
     * Configura i comportamenti (behaviors) del controller.
     * Imposta il VerbFilter per limitare l'accesso alla cancellazione solo tramite metodo POST.
     * * @return array
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
     * Visualizza l'elenco completo del personale aziendale.
     * Utilizza un ActiveDataProvider con paginazione disabilitata per delegare
     * la gestione della visualizzazione alla libreria Client-Side DataTables.
     * * @return mixed
     */
    public function actionIndex()
    {
        // Creiamo il provider come avevi fatto tu
        $dataProvider = new ActiveDataProvider([
            'query' => Personale::find(),
            'pagination' => false, // Disabilitiamo la paginazione di Yii perché se ne occupa DataTables (lato client)
        ]);

        // Passiamo 'models' alla vista estraendoli dal dataProvider
        return $this->render('index', [
            'models' => $dataProvider->getModels(),
        ]);
    }
/**
     * Visualizza la scheda dettagliata di un singolo dipendente.
     * * @param integer $id Identificativo univoco del dipendente.
     * @return mixed
     * @throws NotFoundHttpException Se il dipendente non viene trovato.
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

  /**
     * Crea un nuovo record anagrafico per un dipendente.
     * In caso di successo, reindirizza l'utente alla pagina indice.
     * * @return mixed
     */
    public function actionCreate()
    {
        $model = new Personale();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

   /**
     * Aggiorna le informazioni di un dipendente esistente.
     * Utilizzato per modificare ruoli, mansioni o tariffe orarie.
     * * @param integer $id Identificativo univoco del dipendente.
     * @return mixed
     * @throws NotFoundHttpException Se il dipendente non viene trovato.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Elimina il record di un dipendente dal database.
     * Include un controllo di integrità referenziale per impedire la cancellazione
     * di personale che possiede già presenze registrate o attività a calendario.
     * * @param integer $id Identificativo univoco del dipendente.
     * @return mixed
     * @throws NotFoundHttpException Se il dipendente non viene trovato.
     */

    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            Yii::$app->session->setFlash('success', "Dipendente eliminato con successo.");
        } catch (\yii\db\IntegrityException $e) {
            // Questo scatta se ci sono vincoli di Foreign Key (es. ha presenze registrate)
            Yii::$app->session->setFlash('error', "Impossibile eliminare: il dipendente ha dati correlati (presenze o attività).");
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', "Errore durante la cancellazione: " . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

/**
     * Ricerca il modello Personale in base alla sua chiave primaria.
     * Se il modello non viene trovato, lancia un'eccezione HTTP 404.
     * * @param integer $id Identificativo univoco del dipendente.
     * @return Personale Il modello caricato.
     * @throws NotFoundHttpException Se il modello non esiste.
     */
    protected function findModel($id)
    {
        if (($model = Personale::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionUploadFile($id)
{
    $model = $this->findModel($id);
    $fileModel = new \app\models\AllFiles();

    if ($fileModel->load(Yii::$app->request->post())) {
        $uploadedFile = \yii\web\UploadedFile::getInstance($fileModel, 'f_content');

        if ($uploadedFile && $fileModel->upload($uploadedFile)) {
            $fileModel->id_padre = $id;
            $fileModel->entita = 'personale'; // Entità specifica per il personale
            $fileModel->nomefile = $uploadedFile->baseName;
            $fileModel->estensione = $uploadedFile->extension;

            // Leggi il contenuto binario
            $path = '../web/uploads/' . str_replace(' ', '_', $uploadedFile->baseName) . '.' . $uploadedFile->extension;
            if (file_exists($path)) {
                $fileModel->f_content = file_get_contents($path);
                
                if ($fileModel->save(false)) {
                    Yii::$app->session->setFlash('success', "Documento dipendente caricato con successo.");
                    return $this->redirect(['view', 'id' => $id]);
                }
            }
        }
    }

if (Yii::$app->request->isAjax) {
        return $this->renderAjax('upload', [
            'model' => $model,
            'fileModel' => $fileModel,
        ]);
    }
    return $this->render('upload', ['model' => $model, 'fileModel' => $fileModel]);
}

}
