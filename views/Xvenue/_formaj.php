<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Xvenue */
/* @var $form yii\widgets\ActiveForm */

?>

<style>
    /* Fix per Select2 nelle modali Bootstrap */
    .modal {
        overflow: visible !important;
    }
    
    .modal-dialog {
        overflow: visible !important;
    }
    
    .modal-content {
        overflow: visible !important;
    }
    
    .modal-body {
        overflow: visible !important;
    }
    
    /* Z-index alto per il dropdown Select2 */
    .select2-container--open {
        z-index: 9999 !important;
    }
    
    .select2-dropdown {
        z-index: 9999 !important;
    }
    
    /* Assicura che il container Select2 sia visibile */
    .select2-container {
        z-index: 999 !important;
    }
</style>

<div class="xdivclass">
    <div class="xvenue-form" width="100%">

        <?php $form = ActiveForm::begin(  ['id' => 'xvenue-form',
        'action' => ['xvenue/createaj','èajax'=>1],  
        'enableAjaxValidation' => false]); ?>

        <?= $form->field($model, 'id')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'venue')->textInput(['maxlength' => true]) ?>
        
        <?= $form->field($model, 'citta')->widget(Select2::class, [
            'options' => [
                'placeholder' => 'Seleziona una città...',
                'id' => 'venue-citta-select' // ID specifico per debugging
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'dropdownParent' => new \yii\web\JsExpression('$("#venueModal")'),
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['xvenue/xcaricacitta']),
                    'dataType' => 'json',
                    'delay' => 250,
                    'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }'),
                    'processResults' => new \yii\web\JsExpression('function(data) { return {results:data.items}; }'),
                ],
                'minimumInputLength' => 2,
                'width' => '100%'
            ],
        ]); ?>

        <?= $form->field($model, 'indirizzo')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'cap')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'provincia')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'tipologia')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'capienza')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'sito_web')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'mappa')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'note')->textarea(['maxlength' => true]) ?>
        <?= $form->field($model, 'pos')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<script>
$(document).ready(function() {
    // Fix per Select2 nelle modali Bootstrap
    
    // Rimuovi l'attributo tabindex dalla modale per permettere il focus sui campi Select2
    $('#venueModal').removeAttr('tabindex');
    
    // Override del metodo enforceFocus per permettere il focus sui dropdown Select2
    $.fn.modal.Constructor.prototype.enforceFocus = function() {
        var that = this;
        $(document).on('focusin.modal', function(e) {
            if (that.$element[0] !== e.target && 
                !that.$element.has(e.target).length &&
                // Allow select2 dropdown to be focusable
                !$(e.target).closest('.select2-container--open').length &&
                !$(e.target).closest('.select2-search--dropdown').length) {
                that.$element.focus();
            }
        });
    };
    
    // Reinizializza Select2 quando la modale viene mostrata
    $('#venueModal').on('shown.bs.modal', function() {
        // Distruggi e ricrea Select2 per assicurarsi che funzioni correttamente
        $('#venue-citta-select').select2('destroy').select2({
            placeholder: 'Seleziona una città...',
            allowClear: true,
            dropdownParent: $('#venueModal'),
            ajax: {
                url: '<?= \yii\helpers\Url::to(['xvenue/xcaricacitta']) ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) { 
                    return {q: params.term}; 
                },
                processResults: function(data) { 
                    return {results: data.items}; 
                }
            },
            minimumInputLength: 2,
            width: '100%'
        });
    });
    
    // Pulisci Select2 quando la modale viene nascosta
    $('#venueModal').on('hidden.bs.modal', function() {
        $('#venue-citta-select').select2('destroy');
    });
    
});
</script>