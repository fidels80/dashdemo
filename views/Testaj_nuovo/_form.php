<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Testaj */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="divtestaj-form">

    <?php $form = ActiveForm::begin(['id'=>'testaj-form']); ?>

    <?= $form->field($model, 'testo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'testo2')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
