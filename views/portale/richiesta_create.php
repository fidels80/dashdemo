<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgCrmTicket */
/* @var $tipi array */
/* @var $anagrafiche array */
/* @var $articoli array */
/* @var $matricole array */
/* @var $documenti array */

$this->title = 'Nuova richiesta';
?>
<div class="portale-richiesta-create">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Apri una nuova richiesta</h4>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Indietro', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <div class="card">
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'id_anagrafica')->dropDownList($anagrafiche, ['prompt' => 'Seleziona azienda...']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'id_tipo')->dropDownList($tipi, ['prompt' => 'Seleziona tipo...']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'id_documento')->dropDownList($documenti, ['prompt' => 'Nessun documento']) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'id_articolo')->dropDownList($articoli, ['prompt' => 'Nessun articolo']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'id_matricola')->dropDownList($matricole, ['prompt' => 'Nessuna matricola']) ?>
                </div>
            </div>

            <?= $form->field($model, 'oggetto')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'descrizione')->textarea(['rows' => 5, 'maxlength' => true, 'placeholder' => 'Descrivi la tua richiesta...']) ?>

            <div class="form-group">
                <label for="files">Allegati</label>
                <input type="file" id="files" name="files[]" class="form-control-file" multiple>
                <small class="text-muted">Puoi allegare più file (foto, PDF, documenti...).</small>
            </div>

            <div class="form-group mb-0">
                <?= Html::submitButton('<i class="fas fa-paper-plane"></i> Invia richiesta', ['class' => 'btn btn-success']) ?>
                <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
