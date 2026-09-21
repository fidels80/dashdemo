<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use app\models\Cf; // Assicurati che il case (Cf) sia corretto rispetto al file

/** @var yii\web\View $this */
/** @var app\models\Xtravelhead $model */
/** @var yii\widgets\ActiveForm $form */

$this->title = 'Crea Nuova Prenotazione';

use app\models\User; // Assicurati che il percorso sia corretto


// Recupera gli utenti per la dropdown
$usersList = ArrayHelper::map(User::find()->where(['istourmanager' => 1])->orderBy('username')->all(), 'id', 'username');
// 1. RECUPERIAMO I DATI E CREIAMO IL JSON PER IL JAVASCRIPT
$anagrafica = Cf::find()->all();
$listaClienti = ArrayHelper::map($anagrafica, 'Cd_CF', 'Descrizione');
$clientiJson = json_encode($listaClienti);
?>

<style>
    .nowrap {
        white-space: nowrap;
    }

    .content {
        width: 95%;
    }

    /* ... restanti stili ... */
</style>
<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger"><?= Yii::$app->session->getFlash('error') ?></div>
<?php endif; ?>
<div class="xtravelhead-crea">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="xtravelhead-form">
        <?php $form = ActiveForm::begin(); ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'datath')->widget(DatePicker::classname(), [
                    'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd', 'todayHighlight' => true]
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'numero')->textInput([
                    'readonly' => true,
                    'maxlength' => true,
                    'id' => 'input-numero'
                ]) ?>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Anteprima Decodificatore (Auto)</label>
                    <?= Html::textInput('anteprima_dec', '', [
                        'id' => 'input-decodificatore',
                        'class' => 'form-control',
                        'readonly' => true,
                        'style' => 'background-color: #eee;'
                    ]) ?>
                </div>
            </div>
        </div>

        <?= $form->field($model, 'descrizione')->textarea([
            'id' => 'input-descrizione',
            // ID per JS,
            'rows' => 3
        ])
        ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'x_cd_cf')->widget(Select2::classname(), [
                    'data' => $listaClienti,
                    'options' => [
                        'id' => 'select-cliente', // ID forzato per il JavaScript
                        'placeholder' => 'Seleziona Cliente...'
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Cliente') ?>
            </div>
            <div class="col-md-8">
                <?= $form->field($model, 'x_cfdesk')->textInput([
                    'id' => 'descrizione-cliente',
                    'readonly' => true
                ])->label('Ragione Sociale') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'x_acconto')->textInput(['type' => 'number', 'step' => '0.01']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'x_tiposhow')->dropDownList([
                    1 => 'Città',
                    2 => 'Commessa',
                    3 => 'Festival'
                ], ['prompt' => 'Seleziona tipo...']) ?>
            </div>
            <div class="col-md-4">
                <?php
                // Se stiamo modificando, carichiamo gli ID dei manager già salvati
                if (!$model->isNewRecord && $model->manager_ids === null) {
                    $model->manager_ids = ArrayHelper::getColumn($model->tourManagers, 'id');
                }
                ?>

                <?= $form->field($model, 'manager_ids')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(User::find()->orderBy('username')->all(), 'id', 'username'),
                    'options' => [
                        'placeholder' => 'Seleziona Tour Managers...',
                        'multiple' => true, // ABILITA IL MULTI-SELECT
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ])->label('Tour Managers') ?>
            </div>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <?= Html::submitButton('Salva Prenotazione', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$script = <<< JS
    var datiClienti = $clientiJson;
    
    // Funzione per comporre il decodificatore
    function aggiornaDecodificatore() {
        var numero = $('#input-numero').val();
        var descrizione = $('#input-descrizione').val();
        var risultato = 'Num: ' + numero + ' Des: ' + descrizione;
        $('#input-decodificatore').val(risultato);
    }

    // Evento per il cambio cliente (già presente)
    $('#select-cliente').on('change', function() {
        var codiceSel = $(this).val();
        if (codiceSel && datiClienti[codiceSel]) {
            $('#descrizione-cliente').val(datiClienti[codiceSel]);
        } else {
            $('#descrizione-cliente').val('');
        }
    });

    // Eventi per aggiornare il decodificatore in tempo reale
    $('#input-numero, #input-descrizione').on('input change', function() {
        aggiornaDecodificatore();
    });

    // Eseguiamo la funzione all'avvio per valorizzare il campo con il numero preimpostato
    aggiornaDecodificatore();
JS;
$this->registerJs($script);
?>