<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoDocumento */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="mgtipodocumento-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-8">
            <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'anno')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'contatore')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'usa_progressivo')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'congruita')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'attivo')->checkbox() ?>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Proposta congruità numeri:</strong> se attiva, non è possibile creare un documento con numero più alto
        per una data precedente (es. il 101 non può essere datato prima del 100). Il contatore indica l'ultimo numero usato.
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
