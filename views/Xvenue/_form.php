<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Xvenue */
/* @var $form yii\widgets\ActiveForm */

$cittadata = (new \yii\db\Query())
    ->select(['cd_citta', 'descrizione'])
    ->from('x_citta')

    ->distinct()
    ->createCommand(Yii::$app->db5)
    ->queryAll();


$cittaOptions = ArrayHelper::map($cittadata, 'cd_citta', 'descrizione');


?>
<style>
    .xdivclass {
        width: 180% !important;
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
<div class="xdivclass">
    <div class="xvenue-form" width="100%">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'id')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'venue')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'citta')->widget(Select2::class, [
            'options' => ['placeholder' => 'Seleziona una città...'],
            'pluginOptions' => [
                'allowClear' => true,
              //  'dropdownParent' => new \yii\web\JsExpression('$("#venueModal")'),
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['xvenue/xcaricacitta']), // il tuo action
                    'dataType' => 'json',
                    'delay' => 250,
                    'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }'),
                    'processResults' => new \yii\web\JsExpression('function(data) { return {results:data.items}; }'),
                ],
                'minimumInputLength' => 2, // inizia a cercare dopo 2 caratteri
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
    </div>
    <?php ActiveForm::end(); ?>

</div>