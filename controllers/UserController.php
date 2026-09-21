<?php

namespace app\controllers;

use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\User;
use app\models\UserSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\httpclient\Client;
//use mikehaertl\wkhtmlto\Pdf;
//use mPDF\mPDF;
use kartik\mpdf\Pdf;


/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
       
       $this->getuser();
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        if ($model->load(Yii::$app->request->post())) {
            // 1. Forza il livello a 70
            $model->level = 70;
            // 2. RIMOZIONE TOTALE GESTIONE FILE PER EVITARE CONFLITTO sql_variant
            // Rimuoviamo l'attributo dall'elenco di quelli da salvare
            $model->file = null;
            // Questo comando forza Yii a non includere proprio la colonna nella query SQL
            // 2. RIMUOVIAMO l'attributo 'file' dall'oggetto prima del salvataggio
            // Questo impedisce a Yii di includerlo nella query INSERT
            unset($model->file);

            // 3. Serializzazione campi array
            if (is_array($model->moduli)) {
                $model->moduli = serialize($model->moduli);
            }
            if (is_array($model->reports)) {
                $model->reports = serialize($model->reports);
            }
            if (is_array($model->cd_cli)) {
                $model->cd_cli = serialize($model->cd_cli);
            }

            // 4. Password e Sicurezza
            $postData = Yii::$app->request->post('User');
            if (!empty($postData['password'])) {
                $model->password_hash = Yii::$app->security->generatePasswordHash($postData['password']);
                $model->generateAuthKey();
            }

            // 5. Salvataggio
            // Passiamo la lista esplicita dei campi da NON includere se necessario, 
            // ma unsetAttribute dovrebbe bastare.
            if ($model->save(false)) {

                // --- INVIO EMAIL DI ATTIVAZIONE ---
             if (1==1){
                try {
                    Yii::$app->mailer->compose(
                        ['html' => 'attivato-html', 'text' => 'attivato-text'],
                        ['user' => $model]
                    )
                        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                        ->setTo($model->email)
                        ->setBcc('supporto@programma2000.it')
                        ->setSubject('Benvenuto sul portale ' . Yii::$app->name)
                        ->send();

                    Yii::$app->session->setFlash('success', "Utente creato con successo!");
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('warning', "Utente creato, ma errore invio mail.");
                }

                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                // Gestione errori
                $errors = \yii\helpers\ArrayHelper::flatten($model->getErrors());
                Yii::$app->session->setFlash('error', "Errore: <br><ul><li>" . implode("</li><li>", $errors) . "</li></ul>");

                // Ripristino dati per la form
                $model->moduli = @unserialize($model->moduli) ?: $model->moduli;
                $model->reports = @unserialize($model->reports) ?: $model->reports;
                $model->cd_cli = @unserialize($model->cd_cli) ?: $model->cd_cli;
            }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function old_actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {

            // 1. Forza il livello a 70 per i nuovi utenti
            $model->level = 70;

            // 2. Gestione File (Avatar)
           // $model->file = \yii\web\UploadedFile::getInstance($model, 'file');
           // $uploadedFile = \yii\web\UploadedFile::getInstance($model, 'file');

            // Assicuriamoci che l'attributo 'file' che va al DB sia NULL per ora
            $model->file = null;
            // 3. Serializzazione campi array per il database
            if (is_array($model->moduli)) {
                $model->moduli = serialize($model->moduli);
            }
            if (is_array($model->reports)) {
                $model->reports = serialize($model->reports);
            }
            if (is_array($model->cd_cli)) {
                $model->cd_cli = serialize($model->cd_cli);
            }

            // 4. Gestione Password e Sicurezza
            $postData = Yii::$app->request->post('User');
            if (!empty($postData['password'])) {
                $model->password_hash = Yii::$app->security->generatePasswordHash($postData['password']);
                $model->generateAuthKey();
            }

            // 5. Salvataggio nel Database
            if ($model->save()) {

                // Upload dell'immagine se presente
                if ($model->file) {
                    //$model->upload();
                }

                // --- INVIO EMAIL DI ATTIVAZIONE ---
                try {
                    Yii::$app->mailer->compose(
                        ['html' => 'attivato-html', 'text' => 'attivato-text'],
                        ['user' => $model]
                    )
                        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                        ->setTo($model->email)
                        ->setBcc('supporto@programma2000.it') // Copia conoscenza nascosta fissa
                        ->setSubject('Benvenuto sul portale ' . Yii::$app->name)
                        ->send();

                    Yii::$app->session->setFlash('success', "Utente creato e email di attivazione inviata.");
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash('warning', "Utente creato, ma si è verificato un errore nell'invio della mail.");
                    Yii::error("Errore invio mail creazione: " . $e->getMessage());
                }

                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                // Gestione errori di validazione
                $errors = \yii\helpers\ArrayHelper::flatten($model->getErrors());
                Yii::$app->session->setFlash('error', "Errore durante il salvataggio: <br><ul><li>" . implode("</li><li>", $errors) . "</li></ul>");

                // Unserialize per ripresentare i dati corretti nel form in caso di errore
                $model->moduli = @unserialize($model->moduli) ?: $model->moduli;
                $model->reports = @unserialize($model->reports) ?: $model->reports;
                $model->cd_cli = @unserialize($model->cd_cli) ?: $model->cd_cli;
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $usrid = Yii::$app->user->Id;
        $ris = user::find()
            ->select(['level', 'cd_cli', 'id'])
            ->where(['id' => $usrid])
            ->asArray()
            ->one();

        if ($id != Yii::$app->user->Id and $ris['level'] < 81) {
            die('ACCESSSO NON AUTORIZZATO ');

        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // var_dump(implode($model->moduli));
            // die(  print_R( $_POST['User']['file']));
            $chkfile = UploadedFile::getInstance($model, 'file');

            if ($chkfile != null) {
                $model->file = UploadedFile::getInstance($model, 'file');
            } else {
//die('filenull');
                $model->file = $model->getOldAttribute('file');
            }
            if (is_array($model->moduli)) {
                $model->setAttribute("moduli", serialize($model->moduli));
                //implode('|', $model->moduli));
            }
            if (is_array($model->reports)) {
                $model->setAttribute("reports", serialize($model->reports));
                //implode('|', $model->reports));
            }
            if (is_array($model->cd_cli)) {
                $model->setAttribute("cd_cli", serialize($model->cd_cli));
                //implode('|', $model->reports));
            }
            //    die(print_r($_POST['User']['password']));

            if (isset($_POST['User']['password'])) {

                if ($_POST['User']['password'] != null || trim($_POST['User']['password']) != '') {
                    $model->setAttribute('password_hash',
                        Yii::$app->security->generatePasswordHash($_POST['User']['password']));
                    //        die(print_r(Yii::$app->security->generatePasswordHash($model->password)));
                }

            }
            $v = Yii::$app->user->Id;
            $ris = user::find()
                ->select(['level', 'cd_cli', 'id'])
                ->where(['id' => $usrid])
                ->asArray()
                ->one();
//die(print_R($model->file));

            $t = Yii::$app->runAction('log/set', ['data' => $model,
                'op' => $model->className() . '-->' . $this->action->id]);

            if (1==1) {

                isset($user) ?: $user = $model;

                Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => 'attivato-html',
                            'text' => 'attivato-text'],
                        ['user' => $user]
                    )
                    ->setFrom([Yii::$app->params['supportEmail'] =>
                        Yii::$app->name . ' robot'])
                    ->setTo($model->email)
                    ->setBcc('supporto@programma2000.it') // <--- AGGIUNTO QUI IL CCN FISSO
                    ->setSubject('Aggiornamento  del portale ' . Yii::$app->name)
                    ->send();

            }

            $model->lastlogin = $model->getOldAttribute('lastlogin');
                $chk_limit=
                //user::find()
                // ->where(["level<>100 and status=10 and cd_cli is not null"])
                //->count(["id"]);
                // ->asArray;
              //  ->one();
           (new \yii\db\Query())
    ->from('user')
    ->where('level<>100 and status=10 and cd_cli is not null and ischief is null')
    ->count();

//die(var_dump($chk_limit));

                if($chk_limit>=15 && $model->getOldAttribute('cd_cli')==null &&
                $model->cd_cli != null
                )
            {

              //  die('superato il limite di utenti attivabili!!!');
               
              Yii::$app->session->setFlash('limitutenti');

              return $this->redirect(['index']);

            }




            // Se il salvataggio va a buon fine (usando save(false) se salti le rules)
            if ($model->save()) {

                if ($chkfile != null) {
                    $model->upload();
                }

                Yii::$app->session->setFlash('success', "Utente aggiornato con successo!");

                return $this->redirect(['index']);
            } else {
                // IL SALVATAGGIO È FALLITO: CREIAMO IL FLASH MESSAGE
                $errors = \yii\helpers\ArrayHelper::flatten($model->getErrors());
                Yii::$app->session->setFlash('error', "Errore durante l'aggiornamento: <br><ul><li>" . implode("</li><li>", $errors) . "</li></ul>");

                // IMPORTANTISSIMO: Dobbiamo fare l'unserialize dei campi array altrimenti la form va in errore quando si ricarica
                $model->moduli = @unserialize($model->moduli) ?: $model->moduli;
                $model->reports = @unserialize($model->reports) ?: $model->reports;
                $model->cd_cli = @unserialize($model->cd_cli) ?: $model->cd_cli;

                // Lasciamo che il codice prosegua in basso per fare il "return $this->render('update'...)"
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        return false;
        $model = $this->findModel($id);
        $t = Yii::$app->runAction('log/set', ['data' => $model,
            'op' => $model->className() . '-->' . $this->action->id]);

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function getuser()
    {
        $usrid = Yii::$app->user->Id;

        if ($usrid !== null) {
            $ris = (new \yii\db\Query())
                ->select(['level', 'cd_cli', 'moduli'])
                ->from('user')
                ->where(['id' => $usrid])
                ->one();
//->AsArray();
$messaggio =
    "<h1>Attenzione</h1>\n\n"
    . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";



 $ris ?? exit($messaggio);


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
        $messaggio =
    "<h1>Attenzione</h1>\n\n"
    . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";

$ris ?? exit($messaggio);

        $arrmod = unserialize($ris['moduli']);
        $go = 0;
        if ($ris['level'] != 100) {
            if (is_array($arrmod)) {
                foreach ($arrmod as $value) {
                    //   yii::error('---------{'.strtoupper($value).'}----{'.strtoupper($mn['voce']).'}----{');
                    if (strtoupper($value) == strtoupper($mn['voce'] ?? '')) {
                        $go = 1;
                    }
                    if ($ris['level'] >= 81 and (strtoupper($value)) == 'UTENTI'){
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
    public function actionRp()
    {
        $mmodel = new PasswordResetRequestForm();
        if ($mmodel->load(Yii::$app->request->post())) {
            //return $model;
            /*
            Yii::$app->mailer->compose()
            ->setFrom('somebody@domain.com')
            ->setTo('myemail@yourserver.com')
            ->setSubject('Email sent from Yii2-Swiftmailer')
            ->send();
             */
            if ($mmodel->sendEmail()) {
                // $mmodel->sendEmail();
                //print_R(Yii::$app->mailer);
                Yii::$app->session->setFlash('inviato');
                //  $t = Yii::$app->runAction('log/set', ['data' => $mmodel,
                //'op' => $model->className() . '-->' . $this->action->id]);

            } else {
                Yii::$app->session->setFlash('errore');
//$t = Yii::$app->runAction('log/set', ['data' => $mmodel,
//    'op' => $mmodel->className() . '-->' . $this->action->id]);
//return print_r($mmodel->sendEmail());

            }
            //   return   $this->refresh();

            return $this->goHome();

            /*
            }else{

            return "ERRORE DI INVIO EMAIL";
            }
             */
            /*&& $model->validate()) {

        if ($model->sendEmail()) {
        Yii::$app->session->setFlash('success',
        'Check your email for further instructions.');
        die();
        return  $model;

        } else {
        Yii::$app->session->setFlash('error',
        'Sorry, we are unable to reset password for email provided.');
        }*/
        } else {
            return $this->render('rp', ['model' => $mmodel]);
        }
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    /*  public function actionResetp($token)
    {
    try {
    //  $model = new ResetPasswordForm($token);
    if ($token !== null) {
    $ris = user::find()
    // ->select(['level', 'cd_cli'])

    ->where(['password_reset_token' => $token])
    ->one();
    $model = $ris;
    }
    //  yii::warning($model);
    } catch (InvalidParamException $e) {
    throw new BadRequestHttpException($e->getMessage());
    }

    if ($model->load(Yii::$app->request->post())
    && $model->validate()
    && $model->resetPassword()) {
    Yii::$app->session->setFlash('success', 'New password was saved.');

    return $this->goHome();
    }

    return $this->render('preset', [
    'model' => $model,
    ]);
    }*/

    public function actionResetp($token)
    {
        $model = new ResetPasswordForm($token);
        if ($model->load(Yii::$app->request->post())
            && $model->validate()
            && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->renderpartial('preset', [
            'model' => $model,
        ]);

    }

    public function actionCf($id)
    {
        $model = User::findOne($id);
        return
        $this->render('update', [
            'model' => $model,
        ]);
    }


public function actionTest(){

$ids = [1, 10,
15,
19,
27,
33,
34,
35,
38,
44];

foreach ($ids as $id) {
    $url = Yii::$app->urlManager->createAbsoluteUrl(['site/generate-pdf', 'id' => $id]);
    $this->generatePdfFromUrl
    ($id);
}



}

public function  generatePdfFromUrl($id)
{
    // Trova il modello utilizzando l'ID passato come parametro
    $model = User::findOne($id);

    // Rendering della vista per l'ID specifico
    $content = $this->renderPartial('/user/view', ['model' => $model]);

    // Crea un'istanza di Kartik MPDF
    $pdf = new Pdf([
        'format'      => Pdf::FORMAT_A4,
        'orientation' => Pdf::ORIENT_LANDSCAPE,
        'destination' => Pdf::DEST_FILE, // Per salvare il PDF in un file
        'filename' => Yii::getAlias('@webroot') . '/uploads/pdf/' . $id . 'file_' . time() . '.pdf',
    ]);

    // Imposta l'header e il footer se necessario
    $pdf->getApi()->SetHeader('Header personalizzato', 'center');
    $pdf->getApi()->SetFooter('{PAGENO}');

    // Aggiungi il contenuto alla pagina utilizzando writeHtml()
    $pdf->getApi()->writeHTML($content);

    // Genera il PDF e salvalo nel file specificato
   if ($pdf->render()) {
    echo "Il PDF è stato generato e salvato con successo.<br>";
} else {
 //   echo "Errore durante la generazione del PDF: " . $pdf->output().'<br>';
}


    //echo "Il PDF è stato generato e salvato con successo.";
}



private function generatePdfFromUrl3($url,$id)
  {
    // Istanzia un nuovo oggetto Pdf
    $client = new Client();
    $response = $client->get($url)->send();

    if ($response->isOk) {
        $pdf = new Pdf([// "enable-local-file-access"=>''
           // 'debug' => true, // Opzioni aggiuntive per il debug, se necessario
        'tmpdir' => yii::getAlias('@webroot') . '/uploads/tmp/',
        'disable-local-file-access' => true,
]);
     
$pdf->setOptions([
    'tmpdir'                    => Yii::getAlias('@webroot') . '/uploads/tmp/',
    'disable-local-file-access' => true,
]);

        // Imposta il percorso del file eseguibile di wkhtmltopdf
        $pdf->binary = 'C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe';

        // Opzioni specifiche per il PDF corrente, se necessario
        $pdf->setOptions([
            'viewport-size' => '1280x1024',
        ]);

        // Genera il PDF dal URL
        $pdf->addPage($url);

        // Salva il PDF in un file o invialo in output al browser
        $filePath = Yii::getAlias('@webroot') . '/uploads/pdf/' . $id . 'file_' . time() . '.pdf';
        var_dump($filePath);
        $result=($pdf->saveAs($filePath));
        if (false === $result) {
    // Se c'è stato un errore, stampa il messaggio di errore
    echo "Errore durante il salvataggio del PDF: " . $pdf->getError() . "<br>";
} else {
    // Altrimenti, il salvataggio è riuscito
    echo "Il PDF è stato generato e salvato con successo: $filePath<br>";
}

        echo "generazione del $id PDF per l'URL: $filePath<br>";

    } else {
        // Gestisci l'errore in caso di richiesta fallita
        echo "Errore durante la generazione del $id PDF per l'URL: $url<br> $pdf->binary <br>";
    }
  }


private function generatePdfFromUrl2($url,$id)
    {
        $client = new Client();
        $response = $client->get($url)->send();

        if ($response->isOk) {
            // Salva il contenuto del PDF nel file
            $pdfContent = $response->content;
$t=Yii::getAlias('@webroot');

            $filePath =//Yii::getAlias($t.'/upload/pdf/')
            Yii::getAlias('@webroot') . '/uploads/pdf/'

            
            .$id .'file_' . time() . '.pdf';
            file_put_contents($filePath, $pdfContent);
        } else {
            // Gestisci l'errore in caso di richiesta fallita
            echo "Errore durante la generazione del $id PDF per l'URL: $url<br>";
        }
    }

}
