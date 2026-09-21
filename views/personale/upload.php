<?php

 use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\SubEntitaLookup;
/* @var $this yii\web\View */
/* @var $model app\models\Veicoli */
/* @var $fileModel app\models\AllFiles */

// Rimuoviamo $this->title e il CSS esterno per pulizia, 
// lo stile lo gestiamo internamente alla modale
$this->registerCss("
    .upload-box { 
        border: 2px dashed #007bff; 
        background: #f8fbff; 
        padding: 30px; 
        border-radius: 10px; 
        text-align: center; 
        transition: all 0.3s;
    }
    .upload-box:hover { background: #eef6ff; border-color: #0056b3; }
    .upload-box i { font-size: 2.5rem; color: #007bff; margin-bottom: 10px; }
    .file-name-display { font-weight: bold; color: #28a745; margin-top: 10px; display: block; }
");
// Recuperiamo i dati con pgreq
 

?>

<div class="veicoli-upload">

    <div class="alert alert-info py-2">
        <small><i class="fa fa-info-circle"></i> Carica PDF, immagini o Word per 
        il veicolo <strong><?= Html::encode($model->nome.' '.$model->cognome) ?></strong>.</small>
    </div>
<?php


// Recuperiamo i dati con pgreq
$lookupData = SubEntitaLookup::find()
    ->where(['riferimento' => 'PERSONALE'])
    ->orderBy('descrizione')->all();

$items = ArrayHelper::map($lookupData, 'codice', 'descrizione');
$optionsAttributes = [];
foreach ($lookupData as $item) {
    $optionsAttributes[$item->codice] = ['data-pgreq' => (int)$item->pgreq];
}
?>

<div class="veicoli-upload">
    <?php $form = ActiveForm::begin([
        'id' => 'upload-form-ajax',
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($fileModel, 'sub_entita')->dropDownList($items, [
                'prompt' => 'Seleziona tipologia documento...',
                'class' => 'form-select',
                'id' => 'dropdown-sub-entita',
                'options' => $optionsAttributes
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($fileModel, 'data_inizio')->textInput(['type' => 'date']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($fileModel, 'data_fine')->textInput(['type' => 'date']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($fileModel, 'importo')->textInput([
                'type' => 'number', 
                'step' => '0.01', 
                'id' => 'input-importo',
                'readonly' => true,
                'style' => 'background-color: #e9ecef;',
                'placeholder' => 'N/A'
            ]) ?>
        </div>
    </div>

    <div class="upload-box mb-3">
        <i class="fa fa-cloud-upload-alt"></i>
        <?= $form->field($fileModel, 'f_content')->fileInput(['class' => 'form-control',
         'id' => 'input-file-modal','required' => true, // Aggiunge l'obbligatorietà HTML5
         ])->label(false) ?>
        <p class="text-muted small mt-2" id="help-text">Trascina qui il file o clicca per selezionarlo</p>
        <span id="file-chosen" class="file-name-display"></span>
    </div>

    <?= $form->field($fileModel, 'nota')->textarea(['rows' => 2]) ?>

    <div class="modal-footer px-0 pb-0 pt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <?= Html::submitButton('<i class="fa fa-save"></i> Salva Documento', ['class' => 'btn btn-success shadow-sm']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs("
    // Gestione abilitazione IMPORTO basata su pgreq
    $('#dropdown-sub-entita').on('change', function() {
        var pgreq = $(this).find('option:selected').data('pgreq');
        var input = $('#input-importo');
        if (pgreq == 1) {
            input.prop('readonly', false).css('background-color', '#fff').attr('placeholder', '0.00');
        } else {
            input.prop('readonly', true).css('background-color', '#e9ecef').val('').attr('placeholder', 'N/A');
        }
    });

    // Visualizzazione nome file
    $('#input-file-modal').on('change', function() {
        var fileName = $(this).val().split('\\\\').pop();
        if (fileName) {
            $('#file-chosen').text('File pronto: ' + fileName);
            $('#help-text').hide();
        }
    });
    $('#upload-form-ajax').on('beforeSubmit', function (e) {
        var fileInput = $('#input-file-modal');
        if (fileInput.get(0).files.length === 0) {
            alert('Per favore, seleziona un file prima di salvare.');
            return false; // Blocca l'invio
        }
        return true; // Prosegue con l'invio
    });
");
?>