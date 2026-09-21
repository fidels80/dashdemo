<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DashPermesso */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="dashpermesso-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'codice')->textInput(['maxlength' => true, 'placeholder' => 'es. mgdocumento']) ?></div>
        <div class="col-md-5"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-2"><?= $form->field($model, 'gruppo')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-2"><?= $form->field($model, 'ordine')->textInput(['type' => 'number']) ?></div>
    </div>

    <?= $form->field($model, 'attivo')->checkbox() ?>

    <div class="alert alert-info">
        <i class="fas fa-lightbulb"></i> Il <strong>codice</strong> deve coincidere con l'id del controller Yii
        (es. <code>mgdocumento</code> per <code>MgdocumentoController</code>).
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
