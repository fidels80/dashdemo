<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Veicoli */
/* @var $form yii\widgets\ActiveForm */

// CSS personalizzato aggiornato per layout orizzontale compatto
$this->registerCss("
    .veicoli-form label { font-size: 1rem; font-weight: 600; margin-bottom: 4px; color: #444; }
    .veicoli-form .form-control { height: auto; padding: 8px 12px; font-size: 1.05rem; border-radius: 6px; }
    .veicoli-form .form-group { margin-bottom: 15px; } /* Spazio verticale tra le righe di campi */
    .btn-custom { padding: 10px 35px; font-size: 1.2rem; font-weight: bold; border-radius: 8px; }
    .section-subtitle { border-bottom: 2px solid #e9ecef; padding-bottom: 8px; margin-bottom: 20px; font-weight: bold; color: #0d6efd; font-size: 1.2rem; }
    .card { border: 1px solid #dee2e6; }
");
?>

<div class="veicoli-form card p-4 shadow-sm bg-white">

    <?php $form = ActiveForm::begin([
        'id' => 'veicoli-active-form',
        // Rimuoviamo le opzioni globali per gestirle localmente nelle righe
    ]); ?>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'tipologia')->dropDownList(
            \app\models\Veicoli::getTipologieList(),
            ['prompt' => 'Seleziona...', 'id' => 'select-tipologia']
        ) ?>
    </div>
    <div class="col-md-6" id="div-scadenza-contratto" style="<?= $model->tipologia === 'Proprietà' ? 'display:none' : '' ?>">
        <?= $form->field($model, 'data_scadenza_contratto')->textInput(['type' => 'date']) ?>
    </div>
</div>

<?php
// Piccolo script per mostrare/nascondere la data in base alla selezione
$this->registerJs("
    $('#select-tipologia').change(function() {
        if ($(this).val() === 'Proprietà') {
            $('#div-scadenza-contratto').hide();
        } else {
            $('#div-scadenza-contratto').show();
        }
    });
");
?>
    <div class="row">

        <div class="col-md-12 mb-4">
            <h4 class="section-subtitle"><i class="fa fa-car me-2"></i> Identificazione Mezzo</h4>

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'targa')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Es: AA000BB',
                        'style' => 'text-transform: uppercase; letter-spacing: 1px;'
                    ]) ?>
                </div>
                <div class="col-md-5">
                    <?= $form->field($model, 'marca_modello')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Es: Fiat Ducato L2H2'
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'stato_veicolo')->dropDownList([
                        'Disponibile' => 'Disponibile',
                        'In Uso' => 'In Uso',
                        'In Riparazione' => 'In Riparazione',
                        'Dismesso' => 'Dismesso',
                    ], ['class' => 'form-control']) ?>
                </div>
            </div>
        </div>

        <div class="w-100"></div>
        <div class="col-md-12 mb-4">
            <h4 class="section-subtitle"><i class="fa fa-cogs me-2"></i> Dati Tecnici e Utilizzo</h4>

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'data_immatricolazione')->textInput([
                        'type' => 'date',
                        'class' => 'form-control'
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'classe_euro')->dropDownList([
                        'Euro 4' => 'Euro 4',
                        'Euro 5' => 'Euro 5',
                        'Euro 6' => 'Euro 6',
                        'Euro 6d-Temp' => 'Euro 6d-Temp',
                        'Elettrico' => 'Elettrico',
                        'Ibrido' => 'Ibrido',
                    ], ['prompt' => 'Seleziona Classe...']) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'peso_complessivo')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Es: 35 q.li o 3500 kg'
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'ultimo_km')->textInput([
                        'type' => 'number',
                        'placeholder' => 'Chilometri attuali'
                    ])->label('Chilometraggio (km)') ?>
                </div>
            </div>
        </div>

        <div class="w-100"></div>
        <div class="col-md-12">
            <h4 class="section-subtitle"><i class="fa fa-calendar-check me-2"></i> Scadenze Burocratiche e Archivio</h4>

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'scadenza_assicurazione')->textInput([
                        'type' => 'date',
                        'class' => 'form-control'
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'scadenza_revisione')->textInput([
                        'type' => 'date',
                        'class' => 'form-control'
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'scadenza_ztl')->textInput([
                        'type' => 'date',
                        'class' => 'form-control'
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'documento_path')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Es: Armadio A, Ripiano 2'
                    ])->label('Collocazione Cartaceo') ?>
                </div>
            </div>
        </div>

    </div>

    <hr class="my-4 border-secondary opacity-25">

    <div class="form-group d-flex justify-content-end mb-0">
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-outline-secondary btn-custom me-3']) ?>
        <?= Html::submitButton('<i class="fa fa-save me-2"></i> Salva Scheda Veicolo', ['class' => 'btn btn-success btn-custom shadow-sm']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>