<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgAnagrafica */
/* @var $metodi array */
/* @var $aliquote array */
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

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'perc_provvigione')->textInput(['type' => 'number', 'step' => '0.01', 'min' => 0, 'max' => 100]) ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($model, 'id_metodo_pagamento')->dropDownList($metodi, ['prompt' => 'Seleziona metodo...']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'id_aliquota_iva')->dropDownList($aliquote, ['prompt' => 'Nessuna (usa quella articolo)']) ?>
        </div>
    </div>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        La <strong>% provvigione</strong> è usata per gli <strong>agenti</strong>. Il <strong>metodo di pagamento</strong>
        è valido per clienti, fornitori e agenti e viene proposto nei documenti.
        L'<strong>aliquota IVA</strong>, se impostata, viene proposta su tutte le righe dei documenti del soggetto;
        in alternativa si usa l'aliquota dell'articolo (vendita per i clienti, acquisto per i fornitori).
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
(function () {
    var $agente = $('#mg_anagrafica-is_agente');
    var $provv = $('#mg_anagrafica-perc_provvigione');

    function toggleProvv() {
        var on = $agente.is(':checked');
        $provv.prop('disabled', !on);
        if (!on) {
            $provv.val(0);
        }
    }

    $agente.on('change', toggleProvv);
    toggleProvv();
})();
</script>
