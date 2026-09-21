<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\Docommessa */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="docommessa-form">

    <?php $form = ActiveForm::begin([
        'id' => 'form-docommessa-master', // ID fondamentale per il JS della Modal
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'label' => 'col-sm-4',
                'offset' => 'col-sm-offset-4',
                'wrapper' => 'col-sm-8',
            ],
        ],
    ]); ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-dark text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-project-diagram"></i> Dati Principali Commessa Master</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'Cd_DOCommessa')->textInput([
                        'maxlength' => true, 
                        'style' => 'text-transform: uppercase;',
                        'readonly' => !$model->isNewRecord // In update il codice non si cambia
                    ])->label('Codice Commessa') ?>

                    <?= $form->field($model, 'Descrizione')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'DescrizioneBreve')->textInput(['maxlength' => true]) ?>
                    
<?= $form->field($model, 'Cd_CF')->widget(Select2::classname(), [
    'data' => ArrayHelper::map(\app\models\CF::find()->all(), 'Cd_CF', 'Descrizione'),
    'options' => ['placeholder' => 'Seleziona Cliente...'],
    'pluginOptions' => [
        'allowClear' => true,
        // AGGIUNGI QUESTA RIGA:
        'dropdownParent' => new yii\web\JsExpression('$("#modal-master")') 
    ],
])->label('Cliente') ?>
                </div>

                <div class="col-md-6">
<?= $form->field($model, 'Cd_DOCommessaStato')->widget(Select2::classname(), [
    'data' => ArrayHelper::map(\app\models\DOCommessaStato::find()->all(), 'Cd_DOCommessaStato', 'Descrizione'),
    'options' => ['placeholder' => 'Stato...'],
    'pluginOptions' => [
        'allowClear' => true,
        // AGGIUNGI QUESTA RIGA:
        'dropdownParent' => new yii\web\JsExpression('$("#modal-master")')
    ],
])->label('Stato') ?>

                    <div class="row">
                        <div class="col-6"><?= $form->field($model, 'Sconto')->textInput(['maxlength' => true]) ?></div>
                        <div class="col-6"><?= $form->field($model, 'Provvigione')->textInput(['maxlength' => true]) ?></div>
                    </div>

                    <?= $form->field($model, 'NoteDoCommessa')->textarea(['rows' => 3]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 d-flex">
            <div class="card shadow mb-4 border-left-primary w-100">
                <div class="card-header py-2">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-day"></i> Periodo Commessa</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'DataInizio')->widget(DatePicker::classname(), [
                        'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd', 'todayHighlight' => true]
                    ]) ?>

                    <?= $form->field($model, 'DataFinePresunta')->widget(DatePicker::classname(), [
                        'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd', 'todayHighlight' => true]
                    ]) ?>

                    <?= $form->field($model, 'DataFineReale')->widget(DatePicker::classname(), [
                        'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd', 'todayHighlight' => true]
                    ]) ?>
                </div>
            </div>
        </div>

</div>
    <div style="display:none">
        <?= $form->field($model, 'UserIns')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'TimeIns')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'Ts')->hiddenInput()->label(false) ?>
    </div>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva Commessa Master', ['class' => 'btn btn-primary btn-lg shadow-sm px-5']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>