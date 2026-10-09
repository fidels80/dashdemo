<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgConfigurazione */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="mgconfigurazione-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-8"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
    </div>
    <div class="row">
        <div class="col-md-12"><?= $form->field($model, 'valore')->textInput(['maxlength' => true]) ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
