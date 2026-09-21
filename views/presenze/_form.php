<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Personale;
use app\models\Tipologiapresenza; // Assicurati di aver creato il modello per la nuova tabella
use app\models\Sottocommessa; // Aggiungi questo import per la tabella Sottocommessa
/* @var $this yii\web\View */
/* @var $model app\models\Presenze */
/* @var $form yii\widgets\ActiveForm */

// Registriamo Select2 via CDN
$this->registerCssFile('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerCss("
    .presenze-form label { font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; color: #444; }
    .presenze-form .form-control, .select2-container--bootstrap-5 .select2-selection { 
        height: auto; padding: 10px; font-size: 1.15rem; border-radius: 8px; 
    }
    .btn-custom { padding: 12px 35px; font-size: 1.2rem; font-weight: bold; border-radius: 10px; }
");
?>
<?php
// Questo script "attiva" la barra di ricerca sulle tendine normali usando Select2
$js = <<<JS
$(document).ready(function() {
    
    // Assicurati che Select2 sia applicato alla causale
    $('#select-tipo-assenza').select2({
        placeholder: "Digita o seleziona la causale...",
        allowClear: true,
        width: '100%'
    });

    // Se per caso non c'era già, lo applichiamo esplicitamente anche al personale
    $('#select-personale').select2({
        placeholder: "Digita il nome o seleziona...",
        allowClear: true,
        width: '100%'
    });
    
});
JS;

// Registra lo script nella vista
$this->registerJs($js);
?>
<div class="presenze-form card p-4 shadow-sm border-0">

    <?php $form = ActiveForm::begin(['id' => 'presenze-active-form']); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'personale_id')->dropDownList(
                ArrayHelper::map(Personale::find()->where(['stato_attivo' => 1])->orderBy('cognome')->all(), 'id', function ($model) {
                    return $model->cognome . ' ' . $model->nome;
                }),
                ['prompt' => 'Digita il nome o seleziona...', 'id' => 'select-personale']
            ) ?>
       <?= $form->field($model, 'cd_dosottocommessa')->dropDownList(
            ArrayHelper::map(Sottocommessa::find()->orderBy('Descrizione')->all(), 'Cd_DOSottoCommessa', function ($sc) {
                return $sc->Cd_DOSottoCommessa . ' - ' . $sc->Descrizione;
            }),
            ['prompt' => 'Seleziona cantiere/sottocommessa...', 'id' => 'select-sottocommessa']
        ) ?>    
<?= $form->field($model, 'data_presenza')->textInput([
                'type' => 'date',
                'value' => $model->data_presenza ? $model->data_presenza : date('Y-m-d')
            ]) ?>
<?= $form->field($model, 'tipo_assenza')->dropDownList(
  ArrayHelper::map(Tipologiapresenza::find()->orderBy('descrizione')->all(), 'codice', function ($model) {
        return $model->codice . ' - ' . $model->descrizione;
    }),
    [
        'prompt' => 'Digita o seleziona la causale...', 
        'id' => 'select-tipo-assenza',
           ]
)?>

        </div>

        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                <?= $form->field($model, 'ora_ingresso')->widget(\yii\widgets\MaskedInput::class, [
        'mask' => '29:59', // Forza il formato: prima cifra 0-2, seconda 0-9, terza 0-5, quarta 0-9
        'definitions' => [
            '2' => [
                'validator' => "[0-2]",
                'cardinality' => 1,
            ],
            '9' => [
                'validator' => "[0-9]",
                'cardinality' => 1,
            ],
            '5' => [
                'validator' => "[0-5]",
                'cardinality' => 1,
            ],
        ],
        'options' => [
            'class' => 'form-control form-control-lg',
            'placeholder' => 'HH:mm',
            'type' => 'text', // Fondamentale per Firefox Dev per ignorare AM/PM
        ],
        'clientOptions' => [
            'clearIncomplete' => true, // Cancella se l'utente scrive solo "12:"
            'showMaskOnHover' => false,
            'greedy' => false,
        ]
    ]) ?>   </div>
<div class="col-md-6">
    <?= $form->field($model, 'ora_uscita')->widget(\yii\widgets\MaskedInput::class, [
        'mask' => '29:59', // Forza il formato: prima cifra 0-2, seconda 0-9, terza 0-5, quarta 0-9
        'definitions' => [
            '2' => [
                'validator' => "[0-2]",
                'cardinality' => 1,
            ],
            '9' => [
                'validator' => "[0-9]",
                'cardinality' => 1,
            ],
            '5' => [
                'validator' => "[0-5]",
                'cardinality' => 1,
            ],
        ],
        'options' => [
            'class' => 'form-control form-control-lg',
            'placeholder' => 'HH:mm',
            'type' => 'text', // Fondamentale per Firefox Dev per ignorare AM/PM
        ],
        'clientOptions' => [
            'clearIncomplete' => true, // Cancella se l'utente scrive solo "12:"
            'showMaskOnHover' => false,
            'greedy' => false,
        ]
    ]) ?>
</div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'ore_lavorate')->textInput([
                        'type' => 'number',
                        'step' => '0.5',
                        'class' => 'form-control form-control-lg',
                        'placeholder' => 'Es: 8.0'
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'prz_ora')->textInput([
                        'type' => 'number',
                        'step' => '0.000001', // Coerente con numeric(18,6)
                        'class' => 'form-control form-control-lg',
                        'placeholder' => '0.000000'
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'ritardo_minuti')->textInput([
                        'type' => 'number',
                        'class' => 'form-control form-control-lg',
                        'placeholder' => '0'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12">
            <?= $form->field($model, 'note')->textarea(['rows' => 3, 'class' => 'form-control', 'placeholder' => 'Eventuali annotazioni...']) ?>
        </div>
    </div>

    <hr class="my-4">

    <div class="d-flex justify-content-end">
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-outline-secondary btn-custom me-3']) ?>
        <?= Html::submitButton('<i class="fa fa-save"></i> Salva Registrazione', ['class' => 'btn btn-success btn-custom shadow-sm']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
    $('#presenze-ora_ingresso, #presenze-ora_uscita').on('blur keyup', function() {
        var start = $('#presenze-ora_ingresso').val();
        var end = $('#presenze-ora_uscita').val();
        
        // Puliamo la maschera per vedere se i valori sono completi
        var cleanStart = start.replace(/[^0-9:]/g, '');
        var cleanEnd = end.replace(/[^0-9:]/g, '');

        if (cleanStart.length === 5 && cleanEnd.length === 5) {
            var s = cleanStart.split(':');
            var e = cleanEnd.split(':');
            
            var startMin = parseInt(s[0]) * 60 + parseInt(s[1]);
            var endMin = parseInt(e[0]) * 60 + parseInt(e[1]);
            
            var diff = endMin - startMin;
            if (diff < 0) diff += 1440; // Gestione turno notturno
            
            var hours = (diff / 60).toFixed(2);
            $('#presenze-ore_lavorate').val(hours);
        }
    });
");
?>