<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Xruoli */
/* @var $form yii\widgets\ActiveForm */
?>
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
<div class="xdivclass">
    <div class="xruoli-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cd_party')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>