<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\models\MgMetodoPagamento;
use app\components\FatturaElettronica;

/* @var $this yii\web\View */
/* @var $model app\models\MgMetodoPagamento */
/* @var $tipi array */
/* @var $rate app\models\MgMetodoPagamentoRata[] */
/* @var $form yii\bootstrap4\ActiveForm */

$partenze = MgMetodoPagamento::opzioniPartenza();
?>
<div class="mgmetodopagamento-form">

    <?php $form = ActiveForm::begin(['id' => 'mgmetodopagamento-form']); ?>

    <div class="card mb-3">
        <div class="card-header">Dati metodo di pagamento</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-5"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-3"><?= $form->field($model, 'id_tipo_pagamento')->dropDownList($tipi, ['prompt' => 'Seleziona tipologia...']) ?></div>
                <div class="col-md-1"><?= $form->field($model, 'attivo')->checkbox() ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><?= $form->field($model, 'partenza')->dropDownList($partenze) ?></div>
                <div class="col-md-3"><?= $form->field($model, 'giorni_partenza')->textInput(['type' => 'number']) ?></div>
                <div class="col-md-5">
                    <?= $form->field($model, 'fe_modalita_pagamento')->dropDownList(
                        FatturaElettronica::opzioniModalitaPagamento(),
                        ['prompt' => '— Nessuna —']
                    ) ?>
                </div>
            </div>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i>
                La partenza determina da quando decorrono le rate. Ogni rata è poi spostata dei <strong>giorni</strong>
                indicati in tabella rispetto alla data di partenza.
                <br>
                La <strong>modalità di pagamento (SDI)</strong> è usata nella fattura elettronica per i documenti che
                usano questo metodo (es. <em>MP05</em> bonifico, <em>MP12</em> RIBA).
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Rate</span>
            <button type="button" class="btn btn-sm btn-primary" id="btn-add-rata">
                <i class="fas fa-plus"></i> Aggiungi rata
            </button>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0" id="rate-table">
                <thead>
                <tr>
                    <th style="width:10%">Rata</th>
                    <th style="width:45%">Giorni dalla partenza</th>
                    <th style="width:35%">% importo</th>
                    <th style="width:10%"></th>
                </tr>
                </thead>
                <tbody id="rate-body">
                <?php foreach ($rate as $i => $r): ?>
                    <?= $this->render('_rata', ['index' => $i, 'model' => $r]) ?>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th class="text-right">Totale %</th>
                    <th colspan="2" id="rate-totale">0,00</th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="form-group">
        <?php if ($model->hasErrors('n_rate')): ?>
            <div class="alert alert-danger">
                <?= implode('<br>', array_map(['\yii\helpers\Html', 'encode'], $model->getErrors('n_rate'))) ?>
            </div>
        <?php endif; ?>
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success', 'id' => 'btn-salva-metodo']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <table style="display:none;">
        <tbody>
        <?= $this->render('_rata', ['index' => '__INDEX__', 'model' => null]) ?>
        </tbody>
    </table>
</div>

<script>
(function () {
    function rinumera() {
        var i = 0;
        $('#rate-body .rata-row').each(function () {
            i++;
            $(this).find('.rata-num').text(i);
        });
    }

    function totale() {
        var tot = 0;
        $('#rate-body .rata-pct').each(function () {
            tot += parseFloat($(this).val()) || 0;
        });
        var $tot = $('#rate-totale');
        $tot.text(tot.toFixed(2).replace('.', ','));
        var oltre = tot > 100.0001;
        $tot.toggleClass('text-danger', oltre);
        $('#btn-salva-metodo').prop('disabled', oltre);
        if (oltre) {
            $tot.attr('title', 'Il totale non può superare il 100%');
        } else {
            $tot.removeAttr('title');
        }
    }

    $('#btn-add-rata').on('click', function () {
        var tpl = $('table[style="display:none;"] tbody').html();
        var idx = $('#rate-body .rata-row').length;
        $('#rate-body').append(tpl.replace(/__INDEX__/g, idx));
        rinumera();
        totale();
    });

    $(document).on('click', '.rata-remove', function () {
        $(this).closest('.rata-row').remove();
        rinumera();
        totale();
    });

    $(document).on('input', '.rata-pct', totale);

    rinumera();
    totale();
})();
</script>
