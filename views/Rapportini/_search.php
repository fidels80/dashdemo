<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RapportiniSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rapportini-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cf') ?>

    <?= $form->field($model, 'commessa') ?>

    <?= $form->field($model, 'qta') ?>

    <?= $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'ora_in') ?>

    <?php // echo $form->field($model, 'ora_out') ?>

    <?php // echo $form->field($model, 'numero') ?>

    <?php // echo $form->field($model, 'userid') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'cd_art') ?>

    <?php // echo $form->field($model, 'des_art') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
