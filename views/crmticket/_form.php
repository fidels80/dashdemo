<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgCrmTicket */
/* @var $tipi array */
/* @var $stati array */
/* @var $priorita array */
/* @var $anagrafiche array */
/* @var $articoli array */
/* @var $matricole array */
/* @var $operatori array */
?>
<div class="crmticket-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'oggetto')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'id_tipo')->dropDownList($tipi, ['prompt' => 'Seleziona tipo...']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'id_priorita')->dropDownList($priorita, ['prompt' => '—']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'id_anagrafica')->dropDownList($anagrafiche, ['prompt' => 'Seleziona cliente...']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'id_stato')->dropDownList($stati, ['prompt' => 'Seleziona stato...']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'assegnato_a')->dropDownList($operatori, ['prompt' => 'Non assegnato']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'id_articolo')->dropDownList($articoli, ['prompt' => '—']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'id_matricola')->dropDownList($matricole, ['prompt' => '—']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'data_scadenza')->textInput(['type' => 'date']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'data_apertura')->textInput(['type' => 'date']) ?>
        </div>
    </div>

    <?= $form->field($model, 'descrizione')->textarea(['rows' => 5, 'maxlength' => true]) ?>

    <?php if ($model->isNewRecord): ?>
        <div class="form-group">
            <label for="files">Allegati</label>
            <input type="file" id="files" name="files[]" class="form-control-file" multiple>
            <small class="text-muted">Puoi selezionare più file.</small>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
