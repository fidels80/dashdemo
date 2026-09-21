<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TipoPresenze */

$this->title = 'Update Tipo Presenze: ' . $model->codice;
$this->params['breadcrumbs'][] = ['label' => 'Tipo Presenzes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->codice]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tipo-presenze-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
