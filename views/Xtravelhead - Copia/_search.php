<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\XtravelheadSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="xtravelhead-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'th_id') ?>

    <?= $form->field($model, 'datath') ?>

    <?= $form->field($model, 'numero') ?>

    <?= $form->field($model, 'descrizione') ?>

    <?= $form->field($model, 'timeins') ?>

    <?php // echo $form->field($model, 'evaso_A') ?>

    <?php // echo $form->field($model, 'evaso_p') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
