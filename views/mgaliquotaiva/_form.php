<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\components\FatturaElettronica;

/* @var $this yii\web\View */
/* @var $model app\models\MgAliquotaIva */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="mgaliquotaiva-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-2"><?= $form->field($model, 'percentuale')->textInput(['type' => 'number', 'step' => '0.01']) ?></div>
        <div class="col-md-1"><?= $form->field($model, 'attivo')->checkbox() ?></div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'fe_natura')->dropDownList(
                FatturaElettronica::opzioniNaturaIva(),
                ['prompt' => '— Nessuna —']
            ) ?>
        </div>
    </div>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        La <strong>natura IVA (SDI)</strong> è usata nella fattura elettronica per le righe con questa aliquota
        (tipicamente aliquota 0: esente, non imponibile, reverse charge...). Per le aliquote superiori a zero
        va lasciata vuota.
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
