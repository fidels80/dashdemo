<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgUnitaMisura */

$this->title = 'Modifica unità di misura: ' . $model->codice;
$this->params['breadcrumbs'][] = ['label' => 'Unità di misura', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgunitamisura-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
