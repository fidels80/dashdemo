<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = '';
$this->params['breadcrumbs'] = '';
?>
<div class="user-create">

    <h1><?= Html::encode("Creazione utente") ?></h1>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> Errore!</h5>
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>