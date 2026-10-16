<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
\app\assets\FontAwesomeAsset::register($this);
\hail812\adminlte3\assets\AdminLteAsset::register($this);
\app\components\Swal::registerAssets($this);

$user = Yii::$app->user->identity;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - Portale clienti</title>
    <?php $this->head() ?>
</head>
<body class="hold-transition layout-top-nav">
<?php $this->beginBody() ?>
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-dark navbar-primary">
        <div class="container">
            <a href="<?= Url::to(['/portale/index']) ?>" class="navbar-brand">
                <img src="<?= Yii::getAlias('@web') ?>/images/logo.png" alt="Logo"
                     style="width:32px;height:32px;object-fit:contain;border-radius:50%;margin-right:6px;">
                <span class="brand-text font-weight-light">Portale clienti</span>
            </a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['/portale/index']) ?>"><i class="fas fa-life-ring"></i> Le mie richieste</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['/portale/documenti']) ?>"><i class="fas fa-file-invoice"></i> Documenti</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= Url::to(['/portale/scadenze']) ?>"><i class="fas fa-calendar-check"></i> Scadenze</a>
                </li>
                <li class="nav-item">
                    <span class="nav-link text-white-50"><i class="fas fa-user"></i> <?= Html::encode($user->username ?? '') ?></span>
                </li>
                <li class="nav-item">
                    <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'd-inline']) ?>
                        <button type="submit" class="nav-link btn btn-link text-white"><i class="fas fa-sign-out-alt"></i> Esci</button>
                    <?= Html::endForm() ?>
                </li>
            </ul>
        </div>
    </nav>

    <div class="content-wrapper">
        <div class="content pt-3">
            <div class="container pb-4">
                <?= $content ?>
            </div>
        </div>
    </div>

    <footer class="main-footer">
        <div class="container">
            <span>&copy; <?= date('Y') ?> <?= Html::encode(Yii::$app->name) ?> — Area riservata ai clienti</span>
        </div>
    </footer>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
