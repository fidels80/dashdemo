<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\gacrisorse;
use app\models\Gactatt;

$id = Yii::$app->request->get('id');
$model['id_sub_prv']=$id;
$model['sequenza']=$id;


$risorse = gacrisorse::find()
    //->select(['id','descrizione' ])
    ->asArray()
    ->all();

    
$listrisorse = ArrayHelper::map($risorse, 'id', 'descrizione');

$attiv= Gactatt::find()
->select(['id','id as descrizione'])
->asArray()
->all();
$listratty= ArrayHelper::map($attiv, 'id', 'descrizione');


/* @var $this yii\web\View */
/* @var $model app\models\Gacattivita */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gacattivita-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'id_sub_prv')->
    textInput(['readonly'=>true,'hidden'=>true])->label('') ?>
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'sequenza')->textInput() ?>
</div>
 <div class="col-sm">
      
<?= $form->field($model, 'attivita')->widget(Select2::classname(),
    ['data'         => $listratty,
        'options'       => ['placeholder' => 'Seleziona Risolrsa ...',
            'id'                              => 'attivita'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>
</div>
    <div class="col-sm">
    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>
</div>
</div>
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'um')->textInput(['maxlength' => true]) ?>
</div>
    <div class="col-sm">

    <?= $form->field($model, 'tempo')->textInput() ?>
</div>
    <?php
    // $form->field($model, 'ore')->textInput();
     ?>
    <div class="col-sm">

    <?= $form->field($model, 'risorsa')->widget(Select2::classname(),
    ['data'         => $listrisorse,
        'options'       => ['placeholder' => 'Seleziona Risolrsa ...',
            'id'                              => 'risorsa'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>

</div>
</div>
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'costo')->textInput() ?>
</div>
<div class="col-sm">
        
<?= $form->field($model, 'sconto')->textInput() ?>
</div>
<div class="col-sm">
    
    <?= $form->field($model, 'costo_scontato')->textInput() ?>
</div>
<div class="col-sm">
    
    <?= $form->field($model, 'ricarico')->textInput() ?>
</div>
<div class="col-sm">
    
    <?= $form->field($model, 'costo_ricarico')->textInput() ?>
</div>
</div>
<div class="row">

<div class="col-sm">
    
    <?= $form->field($model, 'sconto_vendita')->textInput() ?>
</div>
<div class="col-sm">
    
    <?= $form->field($model, 'valore_costounitario')->textInput()->label('Val.Unit') ?>

</div>
<div class="col-sm">
    
<?= $form->field($model, 'valore_costotot')->textInput() ?>
</div>
<div class="col-sm">
    
    <?= $form->field($model, 'margine')->textInput() ?>
</div>
<div class="col-sm">
    
    <?= $form->field($model, 'margine_perc')->textInput() ?>
</div>
</div>
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
</div>
</div>
<div class="row">
<div class="col-sm">
    
    <?= $form->field($model, 'data_apertura')->widget(DatePicker::classname(), [
    'options'       => ['placeholder' => 'data'],
    'removeButton'  => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd/mm/yyyy'],
    // yii::warning(date('d/m/y', (strtotime($model->data))));
    //            return date('d/m/y', (strtotime($model->data)));
    //}
]) ?>
    </div>
<div class="col-sm">
    
    <?= $form->field($model, 'data_chiusura')->widget(DatePicker::classname(), [
    'options'       => ['placeholder' => 'data'],
    'removeButton'  => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd/mm/yyyy'],
    // yii::warning(date('d/m/y', (strtotime($model->data))));
    //            return date('d/m/y', (strtotime($model->data)));
    //}
]) ?>
    </div>
</div>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
