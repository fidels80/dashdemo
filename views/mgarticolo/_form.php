<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgArticolo */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="mgarticolo-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3"><?= $form->field($model, 'um')->textInput(['maxlength' => true]) ?></div>
    </div>
    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'prezzo')->textInput(['type' => 'number', 'step' => '0.0001']) ?></div>
        <div class="col-md-3"><?= $form->field($model, 'iva')->textInput(['type' => 'number', 'step' => '0.01']) ?></div>
        <div class="col-md-3"><?= $form->field($model, 'attivo')->checkbox() ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
