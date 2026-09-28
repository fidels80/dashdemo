<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgAliquotaIva */

$this->title = 'Modifica aliquota IVA: ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Aliquote IVA', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgaliquotaiva-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
