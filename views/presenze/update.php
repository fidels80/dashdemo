<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Presenze */

$this->title = '';
//'Aggiorna Presenza : ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Presenzes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '';
//'Update';
?>
<div class="presenze-update">

    <h1><?= Html::encode('Aggiorna Presenza : ' . $model->getNominativoPersonale()) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
