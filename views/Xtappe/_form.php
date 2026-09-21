<style>
    .xdivclass {
        width: 150% !important;
        /* Increased from 80% to 95% */
        margin: auto;
        overflow-x: auto;
        padding: 10px;
        /* Add some padding */
    }

    .xdivclass table {
        min-width: 1200px;
        /* Reduced from 1500px to 1200px */
        width: 100%;
    }

    .dataTables_wrapper {
        width: 100%;
        overflow-x: auto;
        /* Ensure horizontal scroll */
    }

    /* Improve horizontal scrolling */
    .dataTables_scrollX {
        overflow-x: auto !important;
    }

    /* Fissa l'ultima colonna */
    .fixed-actions-column {
        position: sticky !important;
        right: 0 !important;
        background-color: white !important;
        border-left: 2px solid #dee2e6 !important;
        z-index: 10 !important;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1) !important;
        min-width: 120px !important;
        /* Ensure minimum width */
    }

    /* Assicura che l'header sia anche fisso */
    .dataTables_scrollHead th.fixed-actions-column {
        position: sticky !important;
        right: 0 !important;
        background-color: #f8f9fa !important;
        border-left: 2px solid #dee2e6 !important;
        z-index: 11 !important;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1) !important;
        min-width: 120px !important;
    }

    /* Stile per i bottoni nella colonna fissa */
    .fixed-actions-column .btn {
        margin: 1px 2px;
        font-size: 11px;
        padding: 3px 6px;
    }

    /* Ensure other columns have proper width */
    .dataTables_wrapper table td,
    .dataTables_wrapper table th {
        white-space: nowrap;
        min-width: 80px;
    }
</style>


<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Xvenue;

use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
use yii\bootstrap4\Modal;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\db\Expression;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Xtappe */
/* @var $form yii\widgets\ActiveForm */

$listavdata = Xvenue::find()
    ->select([
        'id',
        new Expression("venue + '   ' + citta AS desk")
    ])
    ->asArray()
    ->All();


$listavoptions = ArrayHelper::map($listavdata, 'id', 'desk');




?>

<div class="xtappe-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_tappa')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'th_id')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'data')->widget(DateTimePicker::classname(), [
        'size' => 'lg',
        'options' => ['placeholder' => 'Seleziona Data di Inizio ...'],
        'pluginOptions' => [
            'autoclose' => true,
            'format' => 'dd/mm/yyyy'
        ]
    ]);
    ?>

    <?=   $form->field($model, 'evaso')->checkbox() ?>
    <?= $form->field($model, 'citta')->widget(\kartik\select2\Select2::class, [
        'data' => $listavoptions,
        'options' => [
            'placeholder' => 'Seleziona Venue...',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 0,
        ],
    ])
    ?>

    <div class="form-group form-check">
        <label>
            <?= Html::checkbox('propaga', false, ['id' => 'propaga', 'value' => 1]) ?>
            Propaga alle righe della prenotazione
        </label>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php
$script = <<<JS
$('form').on('beforeSubmit', function(e) {
    var form = $(this);
    var propagaChecked = $('#propaga').is(':checked'); // recupera il checkbox
    
    if (propagaChecked) {
        var conferma = confirm("Hai flaggato 'Propaga'. Vuoi veramente procedere?");
        if (!conferma) {
            return false; // blocca il submit
        }
    }

    // Se arriva qui, submit procede normalmente
    return true;
});
JS;

$this->registerJs($script);
?>