<?php

namespace app\controllers;

use app\models\ContactForm;
use app\models\Elemail;
use app\models\LoginForm;
use app\models\ResetPasswordForm;
use app\models\SignupForm;
use app\models\UploadImageForm;
use app\models\User;
use app\models\Xmenu;
use app\models\Xsubmenu;
use kartik\grid\GridView;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;
use TCPDF;
use mikehaertl\wkhtmlto\Pdf;
use yii\web\HttpException;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs'  => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error'   => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class'           => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        // Se c'e' una verifica 2FA in sospeso, vai direttamente alla verifica
        if (Yii::$app->session->has('pending_2fa_user_id')) {
            return $this->redirect(['two-factor/verify']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            $result = $model->login();

            if ($result === '2fa') {
                Yii::$app->session->set('pending_2fa_user_id', $model->pendingUserId);
                Yii::$app->session->set('pending_2fa_remember', $model->rememberMe);
                return $this->redirect(['two-factor/verify']);
            }

            if ($result) {
                // --- MODIFICA QUI ---
                // Invece di return $this->goBack();
                return $this->redirect(['planning/index']);
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
  $query2 = yii::$app->db
    ->createCommand("
update doc_head set is_locked=null,locked_by=null where locked_by=:usr "
    )->bindValues([ ':usr' => Yii::$app->user->identity->username]);
$query2->execute();
$query2 = yii::$app->db
    ->createCommand("
update gac_sottoprv set is_locked=null,locked_by=null where locked_by=:usr "
    )->bindValues([':usr' => Yii::$app->user->identity->username]);
$query2->execute();

        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionResetadmin()
    {
        $model = User::find()->where(['username' => 'admin'])->one();
        //var_dump($model);
        // if (empty($model)) {
        //  $user = new User();
        // $user->username = 'admin';
        $model->email = 'temp.mail@ilvbc.it';
        $model->setPassword('lineaverde');
        //$user->generateAuthKey();
        if ($model->save(false)) {
            die('good');
        } else {
            die('fail');

        }
        //}
    }

    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {

            // Se la registrazione va a buon fine
            if ($user = $model->signup()) {

                Yii::$app->session->setFlash('success', 
                'Registrazione completata con successo!');

                // Logga l'utente e mandalo alla home
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            } else {
                // SE FALLISCE: Recuperiamo gli errori dal modello e creiamo il Flash
                $errors = \yii\helpers\ArrayHelper::flatten($model->getErrors());
                if (!empty($errors)) {
                    Yii::$app->session->setFlash('errore', "Attenzione: <br><ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
                } else {
                    Yii::$app->session->setFlash('errore', "Errore generico durante la registrazione.");
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {

/*
$model = new PasswordResetRequestForm();
if ($model->load(Yii::$app->request->post()) && $model->validate()) {

if ($model->sendEmail()) {
Yii::$app->session->setFlash('success',
'Check your email for further instructions.');

return $this->goHome();

} else {
Yii::$app->session->setFlash('error',
'Sorry, we are unable to reset password for email provided.');
}
}

//        return $this->render('requestPasswordResetToken', [
//          'model' => $model,
//    ]);
return $this->goHome();

var_dump ('giampaolo.schiappoli@programma2000.com');

//      return $model;*/
//return $this->goHome();
        return $this->renderajax('site/requestPasswordResetToken');
//, [
        //          'model' => $model,
        //    ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()
            && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('RequestPasswordReset', [
            'model' => $model,
        ]);
    }

    public function actionUploadImage()
    {
        $model = new UploadImageForm();
        if (Yii::$app->request->isPost) {
            $model->image = UploadedFile::getInstance($model, 'image');
            if ($model->upload()) {
                // file is uploaded successfully
                echo "File successfully uploaded";
                return;
            }
        }
        return $this->render('upload', ['model' => $model]);
    }

    public function actionBleft()
    {
        $userId = (int) Yii::$app->user->id;
        $identity = Yii::$app->user->identity;
        $level = ($identity && $identity->hasAttribute('level')) ? (int) $identity->level : 0;

        // Menu dinamico definito in dash_menu (con sottovoci e assegnazioni per utente).
        // Il livello 100 vede sempre tutte le voci.
        $email = ($identity && !empty($identity->email)) ? $identity->email : null;
        $items = \app\models\DashMenu::buildMenuForUser($userId, $level, $email);

        $items[] = [
            'label' => 'Utente',
            'url'   => Url::toRoute(['/user/update', 'id' => $userId]),
            'icon'  => 'user',
        ];

        $ris = (new \yii\db\Query())
            ->select(['lastlogin'])
            ->from('user')
            ->where(['id' => $userId])
            ->one();

        if (!empty($ris['lastlogin'])) {
            $items[] = [
                'label' => 'Ultimo accesso: ' . date_format(date_create($ris['lastlogin']), 'd/m/Y H:i'),
                'icon'  => 'clock',
            ];
        }

        return $items;
    }

    public function actionContatti($render = null, $id = null,$periodo=null)
    {

        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post())) {

            $model->files = UploadedFile::getInstances($model, 'files');
            $path         = Yii::getAlias('@webroot') . '/uploads/mail2/';
            $t            = '';
            $atc          = [];
            $atc2         = $model->files;

            foreach ($model->files as $file) {
                $file->saveAs(
                    // $t=$t.
                    ($path . $file->baseName . '.' . $file->extension));
                //.'<br>';
                //$model->path = $path . $model->files->baseName . '.' . $model->files->extension;

                $atc[] = ($path . $file->baseName . '.' . $file->extension);
            }
$request = Yii::$app->request;
if ($request->get('periodo') !== null) {
    $model->contact(Yii::$app->params['adminEmail'], $atc, $atc2, 1);
} else {
    $model->contact(Yii::$app->params['adminEmail'], $atc, $atc2, 0);
}

//die("inviata");

            $elemail = new Elemail();
            $elemail->setAttribute('nome', $model->name);
            $elemail->setAttribute('email', $model->email);
            $elemail->setAttribute('Soggetto', $model->subject);
            $elemail->setAttribute('Corpo', $model->body);

            $elemail->setAttribute('allegati', implode("|", $atc));
            $elemail->save(false);

            if (is_null($render) == false) {
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);

            } else {
                Yii::$app->session->setFlash('contactFormSubmitted');

                return $this->refresh();
            }

        }

        if (is_null($render) == false) {
            return $this->renderajax('contatti', [
                'model' => $model,
            ]);

        } else {
            return $this->render('contatti', [
                'model' => $model,
            ]);
        }

        //return $this->render('contatti', ['model' => $model]);

    }

    public function actionGetexp()
    {

        $isFa      = 'default value';
        $isFa      = $isFa ?? 'default value';
        $pdfHeader = '';
        $pdfFooter = '';
        $title     = null;

        $defaultExportConfig = [
            GridView::HTML  => [
                'label'           => Yii::t('kvgrid', 'HTML'),
                'icon'            => $isFa ? 'file-text' : 'floppy-saved',
                'iconOptions'     => ['class' => 'text-info'],
                'showHeader'      => true,
                'showPageSummary' => true,
                'showFooter'      => true,
                'showCaption'     => true,
                'filename'        => Yii::t('kvgrid', 'grid-export'),
                'alertMsg'        => Yii::t('kvgrid', 'The HTML export file will be generated for download.'),
                'options'         => ['title' => Yii::t('kvgrid', 'Hyper Text Markup Language')],
                'mime'            => 'text/html',
                'config'          => [
                    'cssFile' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
                ],
            ],
            GridView::CSV   => [
                'label'           => Yii::t('kvgrid', 'CSV'),
                'icon'            => $isFa ? 'file-code-o' : 'floppy-open',
                'iconOptions'     => ['class' => 'text-primary'],
                'showHeader'      => true,
                'showPageSummary' => true,
                'showFooter'      => true,
                'showCaption'     => true,
                'filename'        => Yii::t('kvgrid', 'grid-export'),
                'alertMsg'        => Yii::t('kvgrid', 'The CSV export file will be generated for download.'),
                'options'         => ['title' => Yii::t('kvgrid', 'Comma Separated Values')],
                'mime'            => 'application/csv',
                'config'          => [
                    'colDelimiter' => ",",
                    'rowDelimiter' => "\r\n",
                ],
            ],
            GridView::TEXT  => [
                'label'           => Yii::t('kvgrid', 'Text'),
                'icon'            => $isFa ? 'file-text-o' : 'floppy-save',
                'iconOptions'     => ['class' => 'text-muted'],
                'showHeader'      => true,
                'showPageSummary' => true,
                'showFooter'      => true,
                'showCaption'     => true,
                'filename'        => Yii::t('kvgrid', 'grid-export'),
                'alertMsg'        => Yii::t('kvgrid', 'The TEXT export file will be generated for download.'),
                'options'         => ['title' => Yii::t('kvgrid', 'Tab Delimited Text')],
                'mime'            => 'text/plain',
                'config'          => [
                    'colDelimiter' => "\t",
                    'rowDelimiter' => "\r\n",
                ],
            ],
            GridView::EXCEL => [
                'label'           => Yii::t('kvgrid', 'Excel'),
                'icon'            => $isFa ? 'file-excel-o' : 'floppy-remove',
                'iconOptions'     => ['class' => 'text-success'],
                'showHeader'      => true,
                'showPageSummary' => true,
                'showFooter'      => true,
                'showCaption'     => true,
                'filename'        => Yii::t('kvgrid', 'grid-export'),
                'alertMsg'        => Yii::t('kvgrid', 'The EXCEL export file will be generated for download.'),
                'options'         => ['title' => Yii::t('kvgrid', 'Microsoft Excel 95+')],
                'mime'            => 'application/vnd.ms-excel',
                'config'          => [
                    'worksheet' => Yii::t('kvgrid', 'ExportWorksheet'),
                    'cssFile'   => '',
                ],
            ],
            GridView::PDF   => [
                'label'           => Yii::t('kvgrid', 'PDF'),
                'icon'            => $isFa ? 'file-pdf-o' : 'floppy-disk',
                'iconOptions'     => ['class' => 'text-danger'],
                'showHeader'      => true,
                'showPageSummary' => true,
                'showFooter'      => true,
                'showCaption'     => true,
                'filename'        => Yii::t('kvgrid', 'grid-export'),
                'alertMsg'        => Yii::t('kvgrid', 'The PDF export file will be generated for download.'),
                'options'         => ['title' => Yii::t('kvgrid', 'Portable Document Format')],
                'mime'            => 'application/pdf',
                'config'          => [
                    'mode'          => 'c',
                    'format'        => 'A4-L',
                    'destination'   => 'D',
                    'marginTop'     => 20,
                    'marginBottom'  => 20,
                    'cssInline'     => '.kv-wrap{padding:20px;}' .
                    '.kv-align-center{text-align:center;}' .
                    '.kv-align-left{text-align:left;}' .
                    '.kv-align-right{text-align:right;}' .
                    '.kv-align-top{vertical-align:top!important;}' .
                    '.kv-align-bottom{vertical-align:bottom!important;}' .
                    '.kv-align-middle{vertical-align:middle!important;}' .
                    '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                    '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                    '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}',
                    'methods'       => [
                        'SetHeader' => [
                            ['odd' => $pdfHeader, 'even' => $pdfHeader],
                        ],
                        'SetFooter' => [
                            ['odd' => $pdfFooter, 'even' => $pdfFooter],
                        ],
                    ],
                    'options'       => [
                        'title'    => $title,
                        'subject'  => Yii::t('kvgrid', 'PDF export generated by kartik-v/yii2-grid extension'),
                        'keywords' => Yii::t('kvgrid', 'krajee, grid, export, yii2-grid, pdf'),
                    ],
                    'contentBefore' => '',
                    'contentAfter'  => '',
                ],
            ],
            GridView::JSON  => [
                'label'           => Yii::t('kvgrid', 'JSON'),
                'icon'            => $isFa ? 'file-code-o' : 'floppy-open',
                'iconOptions'     => ['class' => 'text-warning'],
                'showHeader'      => true,
                'showPageSummary' => true,
                'showFooter'      => true,
                'showCaption'     => true,
                'filename'        => Yii::t('kvgrid', 'grid-export'),
                'alertMsg'        => Yii::t('kvgrid', 'The JSON export file will be generated for download.'),
                'options'         => ['title' => Yii::t('kvgrid', 'JavaScript Object Notation')],
                'mime'            => 'application/json',
                'config'          => [
                    'colHeads'     => [],
                    'slugColHeads' => false,
                    'jsonReplacer' => null,
                    'indentSpace'  => 4,
                ],
            ],
        ];
        return $defaultExportConfig;
    }
    public function actionReport()
    {
        $report = new \app\reports\_MyReport(array('cd_Cli' =>
            Yii::$app->user->identity->cd_cli,
        ));
        $report->run();
        return $this->render('report', array(
            "report" => $report,
        ));

    }
    public function actionSmail()
    {
        //  $t=Yii::$app->request->post();
        //  echo "ciao";
        // yii::warning($t);
        //  return serialize($_POST);

/*
public $name;
public $email;
public $subject;
public $body;
public $verifyCode;
public $files;
 */

        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post())
        ) {
            $model->files = UploadedFile::getInstance($model, 'files');
            $path         = Yii::getAlias('@webroot') . '/uploads/mail/';

            $model->files->saveAs($path . $model->files->baseName . '.' . $model->files->extension);
            $model->path = $path . $model->files->baseName . '.' . $model->files->extension;
            $model->contact(Yii::$app->params['adminEmail']);
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $model->files;
            //$this->refresh();
        }
        return $this->render('contatti', [
            'model' => $model,
        ]);

    }

    public function actionAssocia()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post())) {

            $model->files = UploadedFile::getInstances($model, 'files');
            $path         = Yii::getAlias('@webroot') . '/uploads/mail2/';
            $t            = '';
            $atc          = [];
            $atc2         = $model->files;
            foreach ($model->files as $file) {
                $file->saveAs(
                    // $t=$t.
                    ($path . $file->baseName . '.' . $file->extension));
                //.'<br>';
                //$model->path = $path . $model->files->baseName . '.' . $model->files->extension;

                $atc[] = ($path . $file->baseName . '.' . $file->extension);
            }

            $model->contact(Yii::$app->params['adminEmail'], $atc, $atc2);

            Yii::$app->session->setFlash('contactFormSubmitted');
//die("inviata");

            $elemail = new Elemail();
            $elemail->setAttribute('nome', $model->name);
            $elemail->setAttribute('email', $model->email);
            $elemail->setAttribute('Soggetto', $model->subject);
            $elemail->setAttribute('Corpo', $model->body);

            $elemail->setAttribute('allegati', implode("|", $atc));
            $elemail->save();

            return $this->refresh();
        }
        return $this->render('associa', [
            'model' => $model,
        ]);

        //return $this->render('contatti', ['model' => $model]);

    }
    public function actionCf($id)
    {
        $model = user::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
          //  yii::error($model->cd_cli);
            $model->save();
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);

            //$this->refresh();
        }
        return $this->renderajax('cf', [
            'id' => $id, 'model' => $model,
        ]);

    }

    public function actionChat()
    {
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            $message = Yii::$app->request->post('message');

            // Esegui la logica del tuo chatbot per elaborare il messaggio e generare una risposta
            $answer = 'La risposta del tuo chatbot qui';
            yii::warning($message);
            // Reindirizza l'utente alla vista "chat" con la risposta del chatbot come parametro
            return $this->render('chat', ['answer' => $answer]);
        } else {
            // Se la richiesta non è una richiesta AJAX o non contiene dati POST, reindirizza l'utente alla pagina principale
            return $this->render('chat', ['answer' => null]);
        }
    }


  public function actionGeneratePdf($id)
 {
    // Trova il modello utilizzando l'ID passato come parametro
    $model = User::findOne($id);

    // Rendering della vista per l'ID specifico
    $content = $this->renderPartial('/user/view', ['model' => $model]);

    // Istanzia una nuova istanza di TCPDF
    $pdf = new TCPDF();

    // Imposta l'orientamento della pagina (ad esempio, 'L' per orizzontale, 'P' per verticale)
    $pdf->AddPage('L');

    // Aggiungi il contenuto alla pagina
    $pdf->writeHTML($content);

    // Salva il PDF in un file o invialo in output al browser
    $pdf->Output($id.'file.pdf', 'I');
}



    /**
     * Manuale utente dell'applicazione.
     * Accessibile solo agli utenti autenticati (i guest vengono rediretti al login).
     *
     * @return string
     */
    public function actionManuale()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        return $this->render('manuale');
    }

    /**
     * Riceve una segnalazione di anomalia dalla dashboard e invia una mail
     * al supporto tecnico con tutti i dati di contesto (utente, pagina, sessione, server).
     *
     * @return array
     */
    public function actionSegnalazione()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->request->isAjax || !Yii::$app->request->isPost) {
            return ['success' => false, 'error' => 'Richiesta non valida.'];
        }

        $descrizione = Yii::$app->request->post('descrizione', '');
        $pagina = Yii::$app->request->post('pagina', 'N/A');
        $url = Yii::$app->request->post('url', Yii::$app->request->absoluteUrl);
        $browser = Yii::$app->request->post('browser', 'N/A');
        $recordId = Yii::$app->request->post('record_id', '');
        $datiExtra = Yii::$app->request->post('dati_extra', '');

        if (empty(trim($descrizione))) {
            return ['success' => false, 'error' => 'La descrizione dell\'anomalia è obbligatoria.'];
        }

        $user = Yii::$app->user->identity;
        $timestamp = date('d/m/Y H:i:s');

        // Dati utente
        $userName = 'Utente sconosciuto';
        $userEmail = '';
        $userUsername = '';
        $userLevel = '';
        $userGruppo = '';
        $userLastLogin = '';
        $userId = '';

        if ($user) {
            $userId = $user->id ?? '';
            $userUsername = $user->username ?? '';
            $userEmail = $user->email ?? '';
            $userLevel = $user->hasAttribute('level') ? ($user->level ?? '') : '';
            $userGruppo = $user->hasAttribute('gruppo') ? ($user->gruppo ?? '') : '';
            $userLastLogin = $user->hasAttribute('lastlogin') ? ($user->lastlogin ?? '') : '';
            $userName = $userUsername;
        }

        // Dati sessione e server
        $sessionId = Yii::$app->session->id ?: 'N/A';
        $ipServer = $_SERVER['SERVER_ADDR'] ?? 'N/A';
        $ipClient = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'N/A');
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';
        $phpVersion = phpversion();
        $appEnv = YII_ENV;
        $RequestMethod = Yii::$app->request->method;
        $isSecure = Yii::$app->request->isSecureConnection ? 'HTTPS' : 'HTTP';

        $emailData = [
            'descrizione' => $descrizione,
            'pagina' => $pagina,
            'url' => $url,
            'browser' => $browser,
            'record_id' => $recordId,
            'dati_extra' => $datiExtra,
            'timestamp' => $timestamp,
            // Utente
            'utente' => $userName,
            'user_id' => $userId,
            'user_username' => $userUsername,
            'user_email' => $userEmail,
            'user_level' => $userLevel,
            'user_gruppo' => $userGruppo,
            'user_lastlogin' => $userLastLogin,
            // Sessione
            'session_id' => $sessionId,
            'ip_client' => $ipClient,
            'ip_server' => $ipServer,
            // Server
            'server_software' => $serverSoftware,
            'php_version' => $phpVersion,
            'app_env' => $appEnv,
            'request_method' => $RequestMethod,
            'is_secure' => $isSecure,
        ];

        try {
            $subject = '[SEGNA] ' . $pagina . ' - ' . $userName . ' - ' . date('d/m/Y H:i');

            $message = Yii::$app->mailer->compose('site/_email_segnalazione', ['data' => $emailData])
                ->setTo(['supportoclienti@programma2000.com' => 'Supporto Clienti Programma 2000'])
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setSubject($subject);

            $message->send();

            return ['success' => true, 'message' => 'Segnalazione inviata con successo al supporto tecnico.'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Errore nell\'invio dell\'email: ' . $e->getMessage()];
        }
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;

        if ($exception !== null) {
            // Se è un HttpException (es. 404)
            $code = $exception instanceof HttpException ? $exception->statusCode : 500;
            $message = $exception->getMessage();

            // Se vuoi mostrare anche la traccia dello stack in dev
            $trace = YII_ENV_DEV ? $exception->getTraceAsString() : null;

            return $this->render('error', [
                'code' => $code,
                'message' => $message,
                'trace' => $trace,
            ]);
        }

        // Nessuna eccezione, redirect a index
        return $this->redirect(['site/index']);
    }
}
