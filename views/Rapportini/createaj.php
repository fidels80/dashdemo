<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\select2\Select2; // <-- Aggiunto il widget Select2
use app\models\MgAnagrafica;
use app\models\MgArticolo;
use app\models\MgSottocommessa;

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */

$clienti = MgAnagrafica::find()
    ->select(['codice', 'ragione_sociale'])
    ->orderBy(['ragione_sociale' => SORT_ASC])
    ->all();

$commesse = MgSottocommessa::find()
    ->select(['codice', 'descrizione'])
    ->orderBy(['codice' => SORT_ASC])
    ->all();

$articoli = MgArticolo::find()
    ->select(['codice', 'descrizione'])
    ->orderBy(['codice' => SORT_ASC])
    ->all();

Yii::warning('articoli', $articoli);
?>

<div class="rapportini-create">

    <h5>Inserisci rapportino</h5>

    <?php $form = ActiveForm::begin([
        'id' => 'rapportini-form',
        'action' => ['rapportini/createaj'],
        'enableClientValidation' => true,
        'enableAjaxValidation' => false,
        'options' => ['validateOnSubmit' => true],
    ]); ?>

    <!-- Clienti (Principale e Alternativo) -->
    <div class="form-row">
        <div class="col-md-6">
            <?= $form->field($model, 'cd_cli')->widget(Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map($clienti, 'codice', function ($a) {
                    return $a->codice . ' - ' . $a->ragione_sociale;
                }),
                'options' => ['placeholder' => 'Seleziona cliente...'],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Cliente') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'altcli')->widget(Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map($clienti, 'codice', function ($a) {
                    return $a->codice . ' - ' . $a->ragione_sociale;
                }),
                'options' => ['placeholder' => 'Seleziona cliente alternativo...'],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Cliente Alternativo') ?>
        </div>
    </div>

    <!-- Sottocommessa -->
    <div class="form-row">
        <div class="col-md-12">
            <?= $form->field($model, 'commessa')->widget(Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map($commesse, 'codice', function ($c) {
                    return $c->codice . ' - ' . $c->descrizione;
                }),
                'options' => ['placeholder' => 'Seleziona sottocommessa...'],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Sottocommessa') ?>
        </div>
    </div>

    <!-- Data Intervento -->
    <div class="form-row">
        <div class="col-md-12">
            <?= $form->field($model, 'data')->input('date')->label('Data intervento') ?>
        </div>
    </div>

    <!-- Ora Inizio e Ora Fine -->
    <div class="form-row">
        <div class="col-md-6">
            <?= $form->field($model, 'ora_in')->input('time')->label('Ora inizio') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'ora_out')->input('time')->label('Ora fine') ?>
        </div>
    </div>

    <!-- Quantità -->
    <div class="form-row">
        <div class="col-md-12">
            <?= $form->field($model, 'qta')->input('number', ['step' => 'any'])->label('Quantità') ?>
        </div>
    </div>

    <!-- Articolo -->
    <div class="form-row">
        <div class="col-md-12">
            <?= $form->field($model, 'cd_art')->widget(Select2::classname(), [
                'data' => \yii\helpers\ArrayHelper::map($articoli, 'codice', function ($a) {
                    return $a->codice . ' - ' . $a->descrizione;
                }),
                'options' => ['placeholder' => 'Seleziona articolo...'],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Articolo') ?>
        </div>
    </div>

    <?= $form->field($model, 'note')->textarea(['rows' => 4])->label('Note') ?>

    <div id="rapportini-errori"></div>

    <div class="form-group mb-0">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', [
            'class' => 'btn btn-success',
            'data-no-confirm' => 1,
        ]) ?>
        <?= Html::button('Annulla', [
            'class' => 'btn btn-secondary',
            'data-dismiss' => 'modal',
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    (function() {
        var $form = $('#rapportini-form');
        if (!$form.length) {
            return;
        }

        // Salvataggio via AJAX (il codice per i filtri manuali è stato rimosso perché ci pensa Select2)
        $form.on('submit', function(e) {
            e.preventDefault();
            $('#rapportini-errori').html('');
            $.post($form.attr('action'), $form.serialize())
                .done(function(res) {
                    if (res && res.success) {
                        $('#rapportini-modal').modal('hide');
                        window.location.reload();
                        return;
                    }
                    var msgs = (res && res.errors) ? res.errors : ['Salvataggio non riuscito.'];
                    $('#rapportini-errori').html(
                        $('<div class="alert alert-danger">').append(
                            $('<ul class="mb-0 pl-3">').append(
                                $.map(msgs, function(m) {
                                    return $('<li>').text(m);
                                })
                            )
                        )
                    );
                })
                .fail(function() {
                    $('#rapportini-errori').html('<div class="alert alert-danger mb-0">Errore durante il salvataggio.</div>');
                });
        });
    })();
</script>