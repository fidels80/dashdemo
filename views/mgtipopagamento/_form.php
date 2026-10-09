<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\components\FatturaElettronica;

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoPagamento */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="mgtipopagamento-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-2"><?= $form->field($model, 'attivo')->checkbox() ?></div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'fe_condizioni_pagamento')->dropDownList(
                FatturaElettronica::opzioniCondizioniPagamento(),
                ['prompt' => '— Nessuna —']
            ) ?>
        </div>
    </div>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Condizioni di pagamento usate nella <strong>fattura elettronica</strong> per i documenti che usano un metodo
        di questo tipo: <em>TP01</em> a rate, <em>TP02</em> pagamento completo, <em>TP03</em> anticipo.
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
