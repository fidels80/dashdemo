<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UecanagraficaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="uecanagrafica-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nome') ?>

    <?= $form->field($model, 'cognome') ?>

    <?= $form->field($model, 'indirizzo') ?>

    <?= $form->field($model, 'citta') ?>

    <?php // echo $form->field($model, 'provincia') ?>

    <?php // echo $form->field($model, 'nazione') ?>

    <?php // echo $form->field($model, 'codicefiscale') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
