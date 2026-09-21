<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\db\Expression;

/* @var $this yii\web\View */
/* @var $model app\models\Xroomlist */
/* @var $tappeDisponibili app\models\Xtappe[] */

// --- Recupero Dati per Select2 (Invariato) ---
$partyData = (new \yii\db\Query())->select(['party as cd_party', 'party as dparty'])->from('xtravelrow')->where(['not', ['party' => null]])->distinct()->createCommand(Yii::$app->db5)->queryAll();
$ruoloData = (new \yii\db\Query())->select(['cd_ruolo as cd_ruolo', 'descrizione as druolo'])->from('xruoli')->distinct()->createCommand(Yii::$app->db5)->queryAll();
$commessaData = (new \yii\db\Query())->select(['Cd_DOSottoCommessa as cd_commessa', 'Descrizione as dcommessa'])->from('DOSottoCommessa')->distinct()->createCommand(Yii::$app->db5)->queryAll();
$listaart = (new \yii\db\Query())->select(['Cd_Ar as id', new Expression("Cd_Ar + '   ' + Descrizione AS desk")])->from('Ar')->where(['obsoleto' => 0])->createCommand(Yii::$app->db5)->queryAll();

$partyOptions = ArrayHelper::map($partyData, 'cd_party', 'dparty');
$ruoloOptions = ArrayHelper::map($ruoloData, 'cd_ruolo', 'druolo');
$commessaOptions = ArrayHelper::map($commessaData, 'cd_commessa', 'dcommessa');
$listaart2 = ArrayHelper::map($listaart, 'id', 'desk');
?>

<style>
    .xdivclass {
        width: 100%;
        padding: 10px;
    }

    .sezione-tappe-container {
        border: 2px solid #ffc107;
        border-radius: 5px;
        margin: 15px 0;
        background-color: #fffbef;
        display: none;
    }

    .tappa-table-scroll {
        max-height: 300px;
        overflow-y: auto;
    }

    .bg-original-propaga {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 10px;
        border-radius: 5px;
    }
</style>

<div class="xdivclass">
    <?php $form = ActiveForm::begin(['id' => 'roomlist-update-form']); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'id_guest')->textInput(['readonly' => true]) ?>
            <?= $form->field($model, 'nominativo')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'cd_ar')->widget(Select2::class, [
                'data' => $listaart2,
                'options' => ['placeholder' => 'Seleziona articolo...'],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'th_id')->textInput(['readonly' => true]) ?>
            <?= $form->field($model, 'ruolo')->widget(Select2::class, [
                'data' => $ruoloOptions,
                'options' => ['placeholder' => 'Seleziona ruolo...'],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
            <?= $form->field($model, 'party')->widget(Select2::class, [
                'data' => $partyOptions,
                'options' => ['placeholder' => 'Seleziona party...'],
                'pluginOptions' => ['allowClear' => true],
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'commessa')->widget(Select2::class, [
        'data' => $commessaOptions,
        'options' => ['placeholder' => 'Seleziona commessa...'],
        'pluginOptions' => ['allowClear' => true],
    ]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 2]) ?>
    <?= $form->field($model, 'evaso')->checkbox() ?>

    <hr>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="bg-original-propaga">
                <div class="custom-control custom-checkbox">
                    <?= Html::checkbox('propaga', false, ['id' => 'propaga-vecchio', 'class' => 'custom-control-input', 'value' => 1]) ?>
                    <label class="custom-control-label font-weight-bold text-secondary" for="propaga-vecchio">
                        Propaga a TUTTE le righe (Vecchio Metodo)
                    </label>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-light border p-2 rounded">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="propaga-tappe-trigger" name="propaga_tappe" value="1">
                    <label class="custom-control-label font-weight-bold text-primary" for="propaga-tappe-trigger">
                        Seleziona tappe specifiche per propagare
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="card sezione-tappe-container" id="box-tappe-specifiche">
        <div class="card-header bg-warning text-dark p-2">
            <strong>Scegli le tappe di destinazione:</strong>
        </div>
        <div class="card-body p-0 tappa-table-scroll">
            <table class="table table-sm table-striped mb-0">
                <thead>
                    <tr class="bg-light">
                        <th class="text-center"><?= Html::checkbox('selection_all', false, ['id' => 'select-all-tappe']) ?></th>
                        <th>Data</th>
                        <th>Città / Venue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tappeDisponibili)): ?>
                        <?php foreach ($tappeDisponibili as $tappa): ?>
                            <tr>
                                <td class="text-center">
                                    <?= Html::checkbox('tappe_selezionate[]', false, [
                                        'value' => $tappa->id_tappa,
                                        'class' => 'tappa-check'
                                    ]) ?>
                                </td>
                                <td><?= Yii::$app->formatter->asDate($tappa->data, 'php:d/m/Y') ?></td>
                                <td><?= $tappa->getDecodevenue() ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">Nessuna tappa trovata.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-group mt-4 text-right">
        <?= Html::submitButton('Salva Modifiche', ['class' => 'btn btn-success btn-lg px-5']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
// Gestione per la tabella specifica
$(document).on('change', '#propaga-tappe-trigger', function() {
    if($(this).is(':checked')) {
        $('#box-tappe-specifiche').stop().slideDown(300);
        // Se attivo questo, disattivo l'altro per sicurezza (opzionale)
        $('#propaga-vecchio').prop('checked', false);
    } else {
        $('#box-tappe-specifiche').stop().slideUp(300);
        $('.tappa-check').prop('checked', false);
    }
});

// Se attivo il vecchio propaga, chiudo la tabella specifica
$(document).on('change', '#propaga-vecchio', function() {
    if($(this).is(':checked')) {
        $('#propaga-tappe-trigger').prop('checked', false).trigger('change');
    }
});

$(document).on('click', '#select-all-tappe', function() {
    $('.tappa-check').prop('checked', $(this).prop('checked'));
});

// Validazione
$('#roomlist-update-form').on('beforeSubmit', function(e) {
    var vecchio = $('#propaga-vecchio').is(':checked');
    var nuovo = $('#propaga-tappe-trigger').is(':checked');

    if (vecchio) {
        return confirm("Hai selezionato la propagazione TOTALE. Confermi?");
    }
    
    if (nuovo) {
        var count = $('.tappa-check:checked').length;
        if (count === 0) {
            alert("Seleziona almeno una tappa!");
            return false;
        }
        return confirm("Confermi la propagazione su " + count + " tappe?");
    }
    
    return true;
});
JS;
$this->registerJs($js);
?>