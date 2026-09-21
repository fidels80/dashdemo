<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tipologiapresenza */

$this->title = 'Update Tipologiapresenza: ' . $model->codice;
$this->params['breadcrumbs'][] = ['label' => 'Tipologiapresenzas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->codice]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tipologiapresenza-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
