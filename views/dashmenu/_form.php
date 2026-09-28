<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\DashMenu */
/* @var $genitori array */
/* @var $icone array */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="dashmenu-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-5"><?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4">
            <?= $form->field($model, 'icona')->widget(Select2::class, [
                'data' => $icone,
                'options' => ['placeholder' => 'Cerca e seleziona un\'icona...'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function (data) { if (!data.id) { return data.text; } return \'<i class="fas fa-\' + data.id + \' mr-1"></i> \' + data.id; }'),
                    'templateSelection' => new JsExpression('function (data) { if (!data.id) { return data.text; } return \'<i class="fas fa-\' + data.id + \' mr-1"></i> \' + data.id; }'),
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'genitore_id')->dropDownList($genitori, ['prompt' => '(voce principale - nessun genitore)']) ?>
        </div>
        <div class="col-md-6"><?= $form->field($model, 'url')->textInput(['maxlength' => true, 'placeholder' => 'es. mgdocumento/index']) ?></div>
    </div>

    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'livello_min')->textInput(['type' => 'number']) ?></div>
        <div class="col-md-3"><?= $form->field($model, 'ordine')->textInput(['type' => 'number']) ?></div>
        <div class="col-md-3"><?= $form->field($model, 'per_tutti')->checkbox() ?></div>
        <div class="col-md-3"><?= $form->field($model, 'attivo')->checkbox() ?></div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-lightbulb"></i>
        <strong>Icona:</strong> cerca e seleziona un'icona dall'elenco (Font Awesome 6.4.2, es. <code>calendar-days</code>, <code>file-invoice</code>, <code>shield-halved</code>).
        <br>
        <strong>Sottovoci:</strong> seleziona una <em>voce genitore</em> per creare una sottovoce.
        <br>
        <strong>Visibilità:</strong> "per tutti" mostra la voce a ogni utente; altrimenti va assegnata da "Assegna agli utenti".
        Il livello minimo confronta il campo <code>level</code> dell'utente.
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
