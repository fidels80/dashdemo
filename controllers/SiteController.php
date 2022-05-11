<?php

namespace app\controllers;
 
use yii\helpers\Url;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\SignupForm;
use app\models\UploadImageForm;
use app\models\User;
use app\models\Xmenu;
use app\models\Xsubmenu;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UploadedFile;
use kartik\grid\GridView;
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
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
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
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
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
        //echo 'asdasdada';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            yii::warning(Yii::$app->request->post());
            return $this->goBack();
        }
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

    public function actionAddAdmin()
    {
        $model = User::find()->where(['username' => 'admin'])->one();
        //var_dump($model);
        if (empty($model)) {
            $user = new User();
            $user->username = 'admin';
            $user->email = 'm.cardinale@tecneo.it';
            $user->setPassword('missori');
            $user->generateAuthKey();
            if ($user->save()) {
                echo 'good';
            }
        }
    }

    public function actionSignup()
    {
     
           $user = new User();

        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {

          // return (var_dump($model));
            if ($user = $model->signup()) {
                if ($user->save()) {
    echo 'good';
}

                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
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
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
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

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
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
        // ['label' => 'Gii',  'icon' => 'file-code', 'url' => ['/gii'], 'target' => '_blank'],
                   
        $items = [];

        $model = User::find()->where(['username' => \Yii::$app->user->identity->username])->
        AsArray()->one();
        $menu = xmenu::find()->where(
//'<=','level',$model['level']
            'level<=:level',
            [':level' => $model['level']]
        )
        ->andwhere(
            'level<>:id',
            [':id' => 100]
        )
            ->AsArray()->all();





        foreach ($menu as $value) {
            # code...
     

            $xsm = Xsubmenu::find()->where(
                //'<=','level',$model['level']
                'level<=:level',
                [':level' => $model['level']]
            )
                ->andwhere(
                    'id_menu=:id',
                    [':id' => $value['id']]
                )
                ->AsArray()->all();
            $sb=[];
            if (count($xsm)>0) {
                foreach ($xsm as $Svalue) {
                    # code...
                    $items3=array(
                            'label'=>$Svalue['voce'],
                            'url'=>  Url::toRoute($Svalue['url']),
                  //          'target' => 'self_',
                       
                         ) ;
                        
                    $sb[]=$items3;
                }
            }



            $items2=array(
                    'label'=>$value['voce'],
                     'url'=>Url::toRoute($value['url']),
                //    'target' => 'self_',
                     'items' => $sb
                
                
                );


            $items[]=$items2;
        }
      //  yii::warning(array_values(array_filter(array_unique($items, SORT_REGULAR))));
        return (array_values(array_filter(array_unique($items, SORT_REGULAR))));
    }
    public function actionContatti()
    {

        return $this->render('contatti', ['model' => $model]);

    }


    public function actionGetexp()
    {
        $defaultExportConfig = [
        GridView::HTML => [
            'label' => Yii::t('kvgrid', 'HTML'),
            'icon' => $isFa ? 'file-text' : 'floppy-saved',
            'iconOptions' => ['class' => 'text-info'],
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            'filename' => Yii::t('kvgrid', 'grid-export'),
            'alertMsg' => Yii::t('kvgrid', 'The HTML export file will be generated for download.'),
            'options' => ['title' => Yii::t('kvgrid', 'Hyper Text Markup Language')],
            'mime' => 'text/html',
            'config' => [
                'cssFile' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css'
            ]
        ],
        GridView::CSV => [
            'label' => Yii::t('kvgrid', 'CSV'),
            'icon' => $isFa ? 'file-code-o' : 'floppy-open',
            'iconOptions' => ['class' => 'text-primary'],
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            'filename' => Yii::t('kvgrid', 'grid-export'),
            'alertMsg' => Yii::t('kvgrid', 'The CSV export file will be generated for download.'),
            'options' => ['title' => Yii::t('kvgrid', 'Comma Separated Values')],
            'mime' => 'application/csv',
            'config' => [
                'colDelimiter' => ",",
                'rowDelimiter' => "\r\n",
            ]
        ],
        GridView::TEXT => [
            'label' => Yii::t('kvgrid', 'Text'),
            'icon' => $isFa ? 'file-text-o' : 'floppy-save',
            'iconOptions' => ['class' => 'text-muted'],
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            'filename' => Yii::t('kvgrid', 'grid-export'),
            'alertMsg' => Yii::t('kvgrid', 'The TEXT export file will be generated for download.'),
            'options' => ['title' => Yii::t('kvgrid', 'Tab Delimited Text')],
            'mime' => 'text/plain',
            'config' => [
                'colDelimiter' => "\t",
                'rowDelimiter' => "\r\n",
            ]
        ],
        GridView::EXCEL => [
            'label' => Yii::t('kvgrid', 'Excel'),
            'icon' => $isFa ? 'file-excel-o' : 'floppy-remove',
            'iconOptions' => ['class' => 'text-success'],
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            'filename' => Yii::t('kvgrid', 'grid-export'),
            'alertMsg' => Yii::t('kvgrid', 'The EXCEL export file will be generated for download.'),
            'options' => ['title' => Yii::t('kvgrid', 'Microsoft Excel 95+')],
            'mime' => 'application/vnd.ms-excel',
            'config' => [
                'worksheet' => Yii::t('kvgrid', 'ExportWorksheet'),
                'cssFile' => ''
            ]
        ],
        GridView::PDF => [
            'label' => Yii::t('kvgrid', 'PDF'),
            'icon' => $isFa ? 'file-pdf-o' : 'floppy-disk',
            'iconOptions' => ['class' => 'text-danger'],
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            'filename' => Yii::t('kvgrid', 'grid-export'),
            'alertMsg' => Yii::t('kvgrid', 'The PDF export file will be generated for download.'),
            'options' => ['title' => Yii::t('kvgrid', 'Portable Document Format')],
            'mime' => 'application/pdf',
            'config' => [
                'mode' => 'c',
                'format' => 'A4-L',
                'destination' => 'D',
                'marginTop' => 20,
                'marginBottom' => 20,
                'cssInline' => '.kv-wrap{padding:20px;}' .
                    '.kv-align-center{text-align:center;}' .
                    '.kv-align-left{text-align:left;}' .
                    '.kv-align-right{text-align:right;}' .
                    '.kv-align-top{vertical-align:top!important;}' .
                    '.kv-align-bottom{vertical-align:bottom!important;}' .
                    '.kv-align-middle{vertical-align:middle!important;}' .
                    '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                    '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                    '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}',
                'methods' => [
                    'SetHeader' => [
                        ['odd' => $pdfHeader, 'even' => $pdfHeader]
                    ],
                    'SetFooter' => [
                        ['odd' => $pdfFooter, 'even' => $pdfFooter]
                    ],
                ],
                'options' => [
                    'title' => $title,
                    'subject' => Yii::t('kvgrid', 'PDF export generated by kartik-v/yii2-grid extension'),
                    'keywords' => Yii::t('kvgrid', 'krajee, grid, export, yii2-grid, pdf')
                ],
                'contentBefore'=>'',
                'contentAfter'=>''
            ]
        ],
        GridView::JSON => [
            'label' => Yii::t('kvgrid', 'JSON'),
            'icon' => $isFa ? 'file-code-o' : 'floppy-open',
            'iconOptions' => ['class' => 'text-warning'],
            'showHeader' => true,
            'showPageSummary' => true,
            'showFooter' => true,
            'showCaption' => true,
            'filename' => Yii::t('kvgrid', 'grid-export'),
            'alertMsg' => Yii::t('kvgrid', 'The JSON export file will be generated for download.'),
            'options' => ['title' => Yii::t('kvgrid', 'JavaScript Object Notation')],
            'mime' => 'application/json',
            'config' => [
                'colHeads' => [],
                'slugColHeads' => false,
                'jsonReplacer' => null,
                'indentSpace' => 4
            ]
        ],
    ];
return $defaultExportConfig;    
}
    public function actionReport()
    {
        $report = new \app\reports\MyReport;
        $report->run();
        return $this->render('report',array(
            "report"=>$report
        ));
        
    }
}
