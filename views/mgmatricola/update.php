<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgMatricola */

$this->title = 'Modifica matricola: ' . $model->matricola;
$this->params['breadcrumbs'][] = ['label' => 'Matricole / Numeri di serie', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->matricola, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgmatricola-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
