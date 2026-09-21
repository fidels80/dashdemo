<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rapportini-form">

    <?php $form = ActiveForm::begin(); ?>



    <?= $form->field($model, 'cd_cli')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'commessa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qta')->textInput() ?>

    <?= $form->field($model, 'data')->textInput() ?>

    <?= $form->field($model, 'ora_in')->textInput() ?>

    <?= $form->field($model, 'ora_out')->textInput() ?>

    <?= $form->field($model, 'userid')->textInput() ?>

    <?= $form->field($model, 'note')->textInput() ?>

    <?= $form->field($model, 'cd_art')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'des_art')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
