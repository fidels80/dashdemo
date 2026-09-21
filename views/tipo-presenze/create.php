<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TipoPresenze */

$this->title = 'Create Tipo Presenze';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Presenzes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipo-presenze-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
