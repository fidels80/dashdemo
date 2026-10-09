<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgConfigurazione */

$this->title = 'Modifica parametro: ' . $model->codice;
$this->params['breadcrumbs'][] = ['label' => 'Configurazione', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgconfigurazione-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
