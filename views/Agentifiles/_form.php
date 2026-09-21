<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Agentifiles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agentifiles-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cd_agente')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nota')->textInput() ?>

    <?= $form->field($model, 'cartella')->textInput() ?>

    <?= $form->field($model, 'cartella_padre')->textInput() ?>

    <?= $form->field($model, 'f_content')->textInput() ?>

    <?= $form->field($model, 'nome_file')->textInput() ?>

    <?= $form->field($model, 'estenzione')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uplfile')->textInput() ?>

    <?= $form->field($model, 'file')->textInput() ?>

    <?= $form->field($model, 'kiave_arch')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
