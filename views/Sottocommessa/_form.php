<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Dosottocommessa */
/* @var $form yii\widgets\ActiveForm */
?>
 

<div class="dosottocommessa-form">

    <?php $form = ActiveForm::begin([
        'id' => 'sottocommessa-form',
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
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold"><i class="fas fa-edit"></i> Dati Principali Sottocommessa</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label font-weight-bold">Commessa Master</label>
                        <div class="input-group">
                            <?= $form->field($model, 'Cd_DOCommessa', [
                                'options' => ['tag' => false],
                                'template' => '{input}',
                            ])->widget(Select2::classname(), [
                                'data' => ArrayHelper::map(\app\models\DOCommessa::find()->all(), 'Cd_DOCommessa', 'Cd_DOCommessa'),
                                'options' => ['placeholder' => 'Seleziona...', 'id' => 'select-commessa-master'],
                                'pluginOptions' => ['allowClear' => true],
                            ]) ?>
                            
                            <div class="input-group-append">
                                <?= Html::button('<i class="fas fa-plus"></i>', [
                                    'class' => 'btn btn-success btn-create-master',
                                    'title' => 'Crea Nuova Commessa Master',
                                    'data-url' => Url::to(['docommessa/create-ajax']),
                                ]) ?>
                                <?= Html::button('<i class="fas fa-edit"></i>', [
                                    'class' => 'btn btn-primary btn-edit-master',
                                    'title' => 'Modifica Commessa Selezionata',
                                    'data-url' => Url::to(['docommessa/update-ajax']),
                                ]) ?>
                            </div>
                        </div>
                        <?= $form->field($model, 'Cd_DOCommessa', ['template' => '{error}'])->label(false) ?>
                    </div>

                    <?= $form->field($model, 'Cd_DOSottoCommessa')->textInput([
                        'maxlength' => true, 
                        'placeholder' => 'Codice Univoco...',
                        'style' => 'text-transform: uppercase;'
                    ])->label('Codice Sottocommessa') ?>

                    <?= $form->field($model, 'Descrizione')->textInput(['maxlength' => true]) ?>
                    <?= $form->field($model, 'DescrizioneBreve')->textInput(['maxlength' => true]) ?>
                </div>
                
                <div class="col-md-6">
                    <br>
                    <?= $form->field($model, 'Cd_CF')->widget(Select2::classname(), [
    'data' => ArrayHelper::map(\app\models\CF::find()->all(), 'Cd_CF', 'Descrizione'),
    'options' => ['placeholder' => 'Seleziona Cliente...'],
    'pluginOptions' => ['allowClear' => true],
])->label('Cliente') ?>

                    <?= $form->field($model, 'Cd_DOCommessaStato')->widget(Select2::classname(), [
                        'data' => ArrayHelper::map(\app\models\DOCommessaStato::find()->all(), 'Cd_DOCommessaStato', 'Descrizione'),
                        'options' => ['placeholder' => 'Stato Attuale...'],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label('Stato Commessa') ?>

                    <div class="row">
                        <div class="col-6"><?= $form->field($model, 'Sconto')->textInput(['maxlength' => true]) ?></div>
                        <div class="col-6"><?= $form->field($model, 'Provvigione')->textInput(['maxlength' => true]) ?></div>
                    </div>
                </div>
            </div> </div> </div> <div class="row">
        <div class="col-md-6 d-flex">
            <div class="card shadow mb-4 border-left-info w-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-calendar-alt"></i> Pianificazione Temporale</h6>
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

        <div class="col-md-6 d-flex">
            <div class="card shadow mb-4 border-left-warning w-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-sticky-note"></i> Note e Dettagli Extra</h6>
                </div>
                <div class="card-body">
                    <?= $form->field($model, 'NoteDoSottoCommessa')->textarea(['rows' => 6])->label('Note Sottocommessa') ?>
                              </div>
            </div>
        </div>
    </div>

    <div class="form-group text-right">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva Sottocommessa', ['class' => 'btn btn-success btn-lg px-5 shadow']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
\yii\bootstrap4\Modal::begin([
    'title' => '<h5><i class="fas fa-project-diagram"></i> Gestione Commessa Master</h5>',
    'id' => 'modal-master',
    'size' => 'modal-xl',
]);
echo "<div id='modalContentMaster'></div>";
\yii\bootstrap4\Modal::end();

$this->registerJs("
    $('.btn-create-master').click(function(){
        var url = $(this).attr('data-url');
        $('#modal-master').find('.modal-title').html('<h5><i class=\"fas fa-plus-circle text-success\"></i> Nuova Commessa Master</h5>');
        $('#modal-master').modal('show').find('#modalContentMaster').html('<div class=\"text-center p-5\"><i class=\"fas fa-spinner fa-spin fa-2x\"></i></div>').load(url);
    });

    $('.btn-edit-master').click(function(){
        var cdCommessa = $('#select-commessa-master').val();
        if(!cdCommessa){
            alert('Seleziona prima una commessa master da modificare!');
            return;
        }
        var url = $(this).attr('data-url') + '&id=' + cdCommessa;
        $('#modal-master').find('.modal-title').html('<h5><i class=\"fas fa-edit text-primary\"></i> Modifica Commessa Master: ' + cdCommessa + '</h5>');
        $('#modal-master').modal('show').find('#modalContentMaster').html('<div class=\"text-center p-5\"><i class=\"fas fa-spinner fa-spin fa-2x\"></i></div>').load(url);
    });

    $(document).on('submit', '#form-docommessa-master', function(e) {
        e.preventDefault();
        var form = $(this);
        $.post(form.attr('action'), form.serialize())
            .done(function(res) {
                if(typeof res === 'object' && res.success) {
                    $('#modal-master').modal('hide');
                    if ($('#select-commessa-master').find(\"option[value='\" + res.id + \"']\").length == 0) {
                        var newOption = new Option(res.id, res.id, true, true);
                        $('#select-commessa-master').append(newOption).trigger('change');
                    } else {
                        $('#select-commessa-master').val(res.id).trigger('change');
                    }
                } else {
                    $('#modalContentMaster').html(res);
                }
            });
    });
");
?>