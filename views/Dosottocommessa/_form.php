<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use app\models\cf;
use app\models\Cf as ModelsCf;
use app\models\DOCommessaStato;
use app\models\Docommessa;

/* @var $this yii\web\View */
/* @var $model app\models\Dosottocommessa */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .content {
        width: 95%;
    }
</style>
<div class="dosottocommessa-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?php if ($model->isNewRecord): ?>
                <?= $form->field($model, 'Cd_DOCommessa')->widget(Select2::classname(), [
                    'data' => ArrayHelper::map(DOCommessa::find()->asArray()->all(), 'Cd_DOCommessa', 'Descrizione'),
                    'options' => ['placeholder' => 'Seleziona Commessa Padre...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label('Commessa di Riferimento'); ?>
            <?php else: ?>
                <?= $form->field($model, 'Cd_DOCommessa')->textInput([
                    'readonly' => true,
                    'style' => 'background-color: #eee;' // Grigio per far capire che è bloccato
                ]) ?>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'Cd_DOSottoCommessa')->textInput([
                'maxlength' => true,
                'readonly' => !$model->isNewRecord
            ])->label('Sotto Commessa') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'Descrizione')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'DescrizioneBreve')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'Cd_CF')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Cf::find()->all(), 'Cd_CF', 'Descrizione'),
                'options' => ['placeholder' => 'Seleziona Cliente...'],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Cliente'); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'Cd_DOCommessaStato')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(DOCommessaStato::find()->all(), 'Cd_DOCommessaStato', 'Descrizione'),
                'options' => ['placeholder' => 'Stato...'],
                'pluginOptions' => ['allowClear' => true],
            ])->label('Stato Commessa'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'DataInizio')->widget(DatePicker::classname(), [
                'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd', 'todayHighlight' => true]
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'DataFinePresunta')->widget(DatePicker::classname(), [
                'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd', 'todayHighlight' => true]
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'DataFineReale')->widget(DatePicker::classname(), [
                'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd', 'todayHighlight' => true]
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'NoteDoSottoCommessa')->textarea(['rows' => 4])->label('Note') ?>

    <hr>
    <div class="form-group text-right">
        <?= Html::submitButton($model->isNewRecord ? 'Crea Sottocommessa' : 'Salva Modifiche', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

