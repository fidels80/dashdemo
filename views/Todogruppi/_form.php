<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Todogruppi */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="todogruppi-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'gruppo')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
