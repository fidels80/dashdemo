<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgAnagrafica */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="mganagrafica-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'ragione_sociale')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3">
            <?= $form->field($model, 'is_cliente')->checkbox() ?>
            <?= $form->field($model, 'is_fornitore')->checkbox() ?>
            <?= $form->field($model, 'is_agente')->checkbox() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'partita_iva')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3"><?= $form->field($model, 'codice_fiscale')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'indirizzo')->textInput(['maxlength' => true]) ?></div>
    </div>
    <div class="row">
        <div class="col-md-2"><?= $form->field($model, 'cap')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'citta')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-2"><?= $form->field($model, 'provincia')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?></div>
    </div>
    <div class="row">
        <div class="col-md-6"><?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-2"><?= $form->field($model, 'attivo')->checkbox() ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
