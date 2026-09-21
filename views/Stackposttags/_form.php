<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Stackposttags */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stackposttags-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'somma')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tagname')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
