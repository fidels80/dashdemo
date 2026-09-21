<?php
//sk-proj-FnOsylU2J285veec9bf7T3BlbkFJIYOZuT9oUNEgkxvXGziL     api chatgpt
namespace app\controllers;

use Yii;
use app\models\Folders;
use app\models\FoldersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Allfiles;
use app\models\AllfilesSearch;
use yii\web\UploadedFile;
/**
 * FoldersController implements the CRUD actions for Folders model.
 */
class FoldersController extends Controller
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
     * Lists all Folders models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FoldersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render(
            'index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single Folders model.
     *
     * @param  integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render(
            'view', [
            'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Creates a new Folders model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate($parent_id = null)
    {
        $model = new Folders();

        if ($model->load(Yii::$app->request->post())) {
            if ($parent_id !== null) {
                $parent = Folders::findOne($parent_id);
                if ($parent) {
                    $model->prependTo($parent);
                }
            }
      // Carica il fileaddw
        $model->f_content = UploadedFile::getInstance($model, 'f_content');
        if ($model->f_content) {
            $content = file_get_contents($model->f_content->tempName);
            $model->f_content = $content;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Il record è stato salvato con successo.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Si è verificato un errore durante il salvataggio.');
            }
        }

        return $this->render(
            'create', [
            'model' => $model,
            ]
        );
    }
}


    /**
     * Updates an existing Folders model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param  integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) ) {
      // Carica il file
        $model->f_content = UploadedFile::getInstance($model, 'f_content');

        // Verifica se il file è stato caricato con successo
        if ($model->f_content) {
            // Leggi il contenuto del file come binario
            $content = file_get_contents($model->f_content->tempName);

            // Salva il contenuto del file nel campo f_content come binario
            $model->f_content = $content;
        }
 $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render(
            'update', [
            'model' => $model,
            ]
        );
    }

    /**
     * Deletes an existing Folders model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param  integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Folders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param  integer $id
     * @return Folders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Folders::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionAjupd()
    {
        // $this->getuser();
        //


          yii::info('Richiesta AJAX ricevuta: ' . Yii::$app->request->method . ' ' . Yii::$app->request->url, 'ajaxRequest');

    // Altri codici del controller...

    // Esempio di registrazione di ulteriori informazioni nel log
    yii::info('Altri dettagli qui...', 'ajaxRequest');
        $tmodel = new   \yii\base\DynamicModel(['nota','files','origine','id_padre','tab']);
        $tmodel->addRule('nota', 'string');
        $tmodel->addRule('origine', 'string');
        $tmodel->addRule('id_padre', 'integer');
        $tmodel->addRule('files', 'file', ['maxFiles' => 1]);
        $tmodel->addRule('tab', 'string');
        //  mb_internal_encoding( 'UTF-8' );
        if ($tmodel->load(Yii::$app->request->post())) {
            // $x = $model->f_content;
            //   $tmpfile=UploadedFile::getInstance($model, 'f_content');
            $tmodel->files = UploadedFile::getInstances($tmodel, 'files');
            $a='';
            $atc2 = $tmodel->files;
            yii::error($tmodel);
            foreach ($atc2 as $file) {
                // $file->saveAs(
                // $t=$t.
                $xmodel = new Allfiles();
                $tmpfile=$file;
                $a=$a. (($file->baseName . '.' . $file->extension));
                //return  print_r($a);
                //$h = file_get_contents($tmpfile->tempName);
                //'0x'.bin2hex((binary)
                //pack("0x*",
                //pack('0x', bin2hex(file_get_contents($tmpfile->tempName)));
                // Lettura del contenuto binario del file
                $fileContent = file_get_contents($tmpfile->tempName);
                // Conversione in binario per la memorizzazione nel campo VARBINARY del database
                $xmodel->f_content = $fileContent !== false ? $fileContent : null;
                 $h = (binary) (file_get_contents($tmpfile->tempName));
                $xmodel->nomefile = basename($tmpfile);
                $xmodel->origine = 'S';
                $xmodel->estensione = pathinfo($tmpfile, PATHINFO_EXTENSION);
                $xmodel->setAttribute('f_content', $h);
                $xmodel->setAttribute('nota', $tmodel->nota);
                $xmodel->setAttribute('id_padre', $tmodel->id_padre);
                $xmodel->setAttribute('entita', is_null($tmodel->tab) ?'DOTES': $tmodel->tab);
           
          
                if ($xmodel->save(false)) {
                      // Salvataggio riuscito
                yii::warning($xmodel);
                 Yii::$app->session->setFlash('success', 'Il file  è stato salvato con successo.');
                    //  return var_dump($xmodel);
                    return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
                } else {
                    // Gestione degli errori di salvataggio
                    return (var_dump($model->getErrors()));
                    // Esegui azioni per gestire gli errori, ad esempio log degli errori o visualizzazione di un messaggio di errore all'utente
                }

            }
  

            
        }

        return $this->renderAjax(
            '_formm', [
            'model' => $tmodel,
            ]
        );
    }
}
