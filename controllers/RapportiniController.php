<?php

namespace app\controllers;

use Yii;
use app\models\Rapportini;
use app\models\RapportiniSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

/**
 * RapportiniController implements the CRUD actions for Rapportini model.
 */
class RapportiniController extends Controller
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
     * Lists all Rapportini models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->getuser();

        $searchModel = new RapportiniSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setPagination(false);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Rapportini model.
     * @param string $id
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
     * Versione stampabile del rapportino, aperta in una nuova scheda.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionStampa($id)
    {
        $this->getuser();

        $model = $this->findModel($id);

        $this->layout = false;
        return $this->render('@app/views/mgdocumento/rapportino_stampa', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Rapportini model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->getuser();

        $model = new Rapportini();
        $model->userid = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post())) {
            $model->userid = Yii::$app->user->id;
            $this->preparaPerSalvataggio($model);
            if ($model->save()) {
                return $this->redirect([ 'index' ]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Form di inserimento aperto in modale dalla lista.
     * Invia JSON alle richieste AJAX (chiusura modale + ricaricamento della lista),
     * altrimenti reindirizza all'elenco.
     */
    public function actionCreateaj()
    {
        $this->getuser();

        $model = new Rapportini();
        $model->userid = Yii::$app->user->id;

        $isAjax = Yii::$app->request->isAjax;

        if ($model->load(Yii::$app->request->post())) {
            // Il rapportino resta sempre riconducibile all'utente collegato.
            $model->userid = Yii::$app->user->id;
            $this->preparaPerSalvataggio($model);

            if ($model->save()) {
                if ($isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['success' => true, 'id' => (string) $model->id];
                }
                return $this->redirect(['index']);
            }
            if ($isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $errors = [];
                foreach ($model->getErrors() as $attribute => $messages) {
                    $errors[] = implode(' ', $messages);
                }
                return ['success' => false, 'errors' => $errors];
            }
        }

        if ($isAjax) {
            return $this->renderAjax('createaj', ['model' => $model]);
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Normalizza i valori provenienti dalla form prima del salvataggio.
     *
     * SQL Server (lingua italiana, DATEFORMAT dmy) non interpreta "YYYY-MM-DD":
     * la data va inviata nel formato ISO 8601 con la "T".
     */
    protected function preparaPerSalvataggio(Rapportini $model)
    {
        if ($model->data !== null && $model->data !== '') {
            $ts = strtotime((string) $model->data);
            if ($ts !== false) {
                $model->data = date('Y-m-d\TH:i:s', $ts);
            }
        }
        $this->completaDescrizioneArticolo($model);
    }

    /**
     * Recupera dall'anagrafica articoli la descrizione del codice selezionato.
     */
    protected function completaDescrizioneArticolo(Rapportini $model)
    {
        if ($model->cd_art === null || $model->cd_art === '') {
            $model->des_art = null;
            return;
        }
        $articolo = \app\models\MgArticolo::findOne(['codice' => $model->cd_art]);
        if ($articolo !== null) {
            $model->des_art = $articolo->descrizione;
        }
    }

    /**
     * Updates an existing Rapportini model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->getuser();

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $this->preparaPerSalvataggio($model);
            if ($model->save()) {
                return $this->redirect([ 'index' ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Rapportini model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->getuser();

        $model = $this->findModel($id);

        if ((int) $model->evaso === 1) {
            Yii::$app->session->setFlash('error',
                'Il rapportino è evaso in un documento e non può essere eliminato.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Rapportini model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Rapportini the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $this->getuser();

        if (($model = Rapportini::findOne($id)) !== null) {
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

    $mn = (new \yii\db\Query())
        ->select(['voce', 'url', 'Nmodulo'])
        ->from('xmenu')
        ->where(['upper(Nmodulo)' => strtoupper($nmod)])
        ->one();
}

$arrmod = unserialize($ris['moduli']??'');
$go = 0;
if (($ris['level'] ?? 0)!= 100) {
    if (is_array($arrmod)) {
        foreach ($arrmod as $value) {
            //   yii::error('---------{'.strtoupper($value).'}----{'.strtoupper($mn['voce']).'}----{');
            if (strtoupper($value) == strtoupper($mn['voce'])) {
                $go = 1;
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
