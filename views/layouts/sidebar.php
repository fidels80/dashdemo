<?php

use app\models\xm;
use app\mpdel\site;
use yii\helpers;
use yii\helpers\Url;
use yii\helpers\Html;

//$x=new  site->Bleft();
\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
\hail812\adminlte3\assets\AdminLteAsset::register($this);
$this->registerCssFile('https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback');

$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

$publishedRes = Yii::$app->assetManager->publish('@vendor/hail812/yii2-adminlte3/src/web/js');
$this->registerJsFile(
    $publishedRes[1] . '/control_sidebar.js',
    ['depends' => '\hail812\adminlte3\assets\AdminLteAsset']
);

$x = Yii::$app->runAction('site/bleft');
//yii::warning($x);
$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['sidebar_color', 'file', 'lastlogin'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrgrid = $ris['sidebar_color'];
//yii::warning($usrid);
// Sotto le altre registrazioni asset
$this->registerCssFile(Yii::getAlias('@web/css/custom-sidebar.css'), ['depends' => [\hail812\adminlte3\assets\AdminLteAsset::class]]);
?>



<?php
// Se $usrgrid è vuoto o vuoi forzare un colore fisso:
$customColorClass = "sidebar-custom-bg"; // La classe creata nel CSS sopra

        // Cambia la riga dell'aside così:
 echo   '<aside class="main-sidebar elevation-0 sidebar-light-primary">'; ?>
 
<!-- Brand Logo -->
<a href="<?= Url::home() ?>" class="brand-link">
    <img src="<?php echo Yii::getAlias('@web') . '/uploads/logo_ufficio2000.png' ?>"
        alt="Demo" class=" img-circle elevation-3" width="50" height="50" style="opacity: .8">
    <span class="brand-text font-weight-light">Ufficio2000</span>
</a>

<div class="sidebar layout-navbar-fixed ">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">


            <img src="<?php
                        if (!empty($ris['file'])) {
                            echo Yii::getAlias('@web') . '/uploads/' . $usrid . '_' . str_replace(' ', '_', $ris['file']);
                        } else {
                            echo $assetDir . '/img/user2-160x160.jpg';
                        }
                        ?>"

                alt="User Image">
        </div>
        <div class="info">
            <a href="<?= Url::toRoute(['/user/update', 'id' => Yii::$app->user->id]) ?>" class="d-block font-weight-bold">
                <?= Html::encode(Yii::$app->user->identity->username) ?>
            </a>
            <span class="text-muted" style="font-size: 0.7rem; display: block; margin-top: -2px;">
                <i class="fas fa-sign-in-alt mr-1"></i>
                <?= date_format(date_create($ris['lastlogin']), "d/m/Y H:i") ?>
            </span>
        </div>
    </div>


    <nav class="mt-2">
        <?php
        yii::warning($x);
        echo \hail812\adminlte\widgets\Menu::widget([
            'items' => $x

        ]);
        ?>

    </nav>


</div>

</aside>