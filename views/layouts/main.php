<?php
use app\models\User;
use app\models\LoginForm;
/* @var $this \yii\web\View */
/* @var $content string */
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
//use app\models\User;

\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
\hail812\adminlte3\assets\AdminLteAsset::register($this);
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');

$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

$publishedRes = Yii::$app->assetManager->publish('@vendor/hail812/yii2-adminlte3/src/web/js');
$this->registerJsFile($publishedRes[1].'/control_sidebar.js', ['depends' => '\hail812\adminlte3\assets\AdminLteAsset']);
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
</head>
<body class="hold-transition sidebar-mini">
<?php $this->beginBody() ?>

<?php if (Yii::$app->user->isGuest     ): ?>
    <div class="wrapper">
    <table width="80%">
<tbody>
<tr>
<td style="width: 30%;">&nbsp;</td>
<td style="width: 60%;">
    <?php 
    
  echo  $this->render('/site/login',['model'=>new LoginForm(),])//,[$model=>['email'=>null,'password'=>null]]
;
  //, [$model=>new User(), 'assetDir' => $assetDir]) ?>
 &nbsp;</td>
<td style="width: 30%;">&nbsp;</td>
</tr>
</tbody>
</table>
    <?php //$this->render('/site/login',[$model=>[new LoginForm()]]) ?>

</div>
    <?php else: ?>
<div class="wrapper">
    <!-- Navbar -->
    <?php echo  $this->render('navbar', ['assetDir' => $assetDir]) ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php  echo $this->render('sidebar', ['assetDir' => $assetDir]) ?>

    <!-- Content Wrapper. Contains page content -->
    <?= $this->render('content', ['content' => $content, 'assetDir' => $assetDir]) ?>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <?= $this->render('control-sidebar') ?>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <?= $this->render('footer') ?>
</div>
<?php  endif;?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
