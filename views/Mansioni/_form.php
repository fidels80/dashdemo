<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Mansioni */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mansioni-form card shadow-sm p-4">
    
    <div class="border-bottom mb-4 pb-2">
        <h3 class="text-primary m-0">
            <i class="fas fa-briefcase"></i> 
            <?= $model->isNewRecord ? 'Nuova Mansione' : 'Modifica Mansione: ' . Html::encode($model->codice) ?>
        </h3>
        <p class="text-muted small">Specifica il codice e la mansione ricoperta dal personale.</p>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'mansioni-active-form',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'form-label fw-bold'],
            'inputOptions' => ['class' => 'form-control'],
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'codice')->textInput([
                'maxlength' => true, 
                'placeholder' => 'Es: AUT, MECC, OP...',
                'style' => 'text-transform: uppercase;',
                'id' => 'mansioni-codice'
            ]) ?>
        </div>

        <div class="col-md-8">
            <?= $form->field($model, 'descrizione')->textInput([
                'maxlength' => true, 
                'placeholder' => 'Inserisci la descrizione della mansione...'
            ]) ?>
        </div>
    </div>

    <hr class="my-4">

    <div class="form-group d-flex justify-content-end gap-2">
        <?= Html::a('<i class="fas fa-times"></i> Annulla', ['index'], ['class' => 'btn btn-outline-secondary px-4']) ?>
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva Mansione', ['class' => 'btn btn-success px-5']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// Script per il maiuscolo automatico sul codice
$this->registerJs("
    $('#mansioni-codice').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
");
?>