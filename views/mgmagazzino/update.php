<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgMagazzino */

$this->title = 'Modifica magazzino: ' . $model->codice;
$this->params['breadcrumbs'][] = ['label' => 'Magazzini', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgmagazzino-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
