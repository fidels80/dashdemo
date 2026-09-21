<?php

use app\models\User;
use app\models\LoginForm;

/* @var $this \yii\web\View */
/* @var $content string */
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
//use app\models\User;
use app\models\SignupForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;


$usrid = Yii::$app->user->Id;


if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['level', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
    //->AsArray();
}





\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
\hail812\adminlte3\assets\AdminLteAsset::register($this);
\hail812\adminlte3\assets\PluginAsset::register($this)->add(['sweetalert2']);
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');
//$this->registerJsFile('https://code.jquery.com/jquery-3.6.0.min.js', ['position' => \yii\web\View::POS_HEAD]);

$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

$publishedRes = Yii::$app->assetManager->publish('@vendor/hail812/yii2-adminlte3/src/web/js');
$this->registerJsFile($publishedRes[1] . '/control_sidebar.js', ['depends' => '\hail812\adminlte3\assets\AdminLteAsset']);
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/segnalazione.js', ['depends' => [\yii\web\YiiAsset::class]]);


$this->registerJsFile('https://code.jquery.com/jquery-3.3.1.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jquery.pjax/2.0.1/jquery.pjax.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Includi gli altri asset di Yii2 e il tuo stile e script personalizzati
$this->registerAssetBundle(yii\web\YiiAsset::class);
$this->registerAssetBundle(yii\widgets\PjaxAsset::class);
$this->registerAssetBundle(yii\bootstrap4\BootstrapAsset::class);
$this->registerAssetBundle(yii\bootstrap4\BootstrapPluginAsset::class);
//$this->registerAssetBundle(yii\bootstrap4\BootstrapThemeAsset::class);


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Stile per applicare il font Poppins a tutta l'applicazione -->
    <style>
        body,
        html {
            font-family: 'Poppins', sans-serif !important;
        }

        * {
            font-family: inherit;
        }
    </style>

</head>

<body class="hold-transition sidebar-mini">


    <?php

    $request = Yii::$app->request;

    //echo $request->scriptUrl;
    $get = $request->get();

    ?>
    <?php $this->beginBody() ?>
    <?php if (Yii::$app->user->isGuest && Yii::$app->session->has('pending_2fa_user_id')): ?>
        <div class="wrapper">
            <?= $content ?>
        </div>
    <?php elseif (Yii::$app->user->isGuest): ?>
        <div class="wrapper">
            <table width="80%">
                <tbody>
                    <tr>
                        <td style="width: 30%;">&nbsp;</td>
                        <td style="width: 60%;">
                            <?php
                            if (($request->get('isnew')) !== null) {
                                echo $this->render('/site/signup', ['model' => new SignupForm()]);
                            } else {

                                if (($request->get('reset')) !== null) {
                                    //   echo $this->render('/site/requestPasswordResetToken',
                                    //  ['model'=> new PasswordResetRequestForm()]);
                                    echo $this->render(
                                        '/user/rp',
                                        ['model' => new PasswordResetRequestForm()]
                                    );
                                    //, ['model' => new LoginForm()]);

                                } elseif (($request->get('token') == null)) {
                                    echo  $this->render('/site/login', ['model' => new LoginForm(),]);
                                }
                            }




                            //, [$model=>new User(), 'assetDir' => $assetDir]) 
                            ?>
                            &nbsp;</td>
                        <td style="width: 30%;">&nbsp;</td>
                    </tr>
                </tbody>
            </table>
            <?php //$this->render('/site/login',[$model=>[new LoginForm()]]) 
            ?>

        </div>


    <?php elseif ($ris['cd_cli'] == null || $ris['level'] == null): ?>

        <div class="wrapper">
            <?php echo $this->render('navbar', ['assetDir' => $assetDir]) ?>


            <!-- /.control-sidebar -->
            <div class="card">
                <div class="card-body login-card-body">
                    <table height="450px" width='100%'>
                        <br><br><br><br><br><br><br><br>
                        <h1 align="center">A breve sarà attivata l'utenza controlla la mail <br>
                            che hai usato per registarti!</h1>
                        <br>
                    </table>
                </div>
            </div>
            <!-- Main Footer -->
            <?= $this->render('footer') ?>
        </div>


    <?php else: ?>
        <div class="wrapper">
            <!-- Navbar -->
            <?php echo  $this->render('navbar', ['assetDir' => $assetDir]) ?>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <?php echo $this->render('sidebar', ['assetDir' => $assetDir]) ?>

            <!-- Content Wrapper. Contains page content -->
            <?= $this->render('content', ['content' => $content, 'assetDir' => $assetDir]) ?>
            <!-- /.content-wrapper -->

            <!-- Control Sidebar -->
            <?= $this->render('control-sidebar') ?>
            <!-- /.control-sidebar -->

            <!-- Main Footer -->
            <?= $this->render('footer') ?>
            <?= $this->render('_chat') ?>
        </div>
    <?php endif; ?>
    <?= \bizley\cookiemonster\CookieMonster::widget([
        'content' => [
            'buttonMessage' => 'OK', // instead of default 'I understand'
            'mainMessage' => 'I Cookies utilizzzati sono vincolati solo e unicamente all\'uso interno della web app per la quale avete richiesto accesso di conseguenza l\'uso 
        degli stessi è limitato alla singola applicazione e sessione e non vengono ceduti a terze parti o utilizzati a fini statistici di qualunque tipo'
        ],
        'mode' => 'bottom'
    ]);
    ?>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>