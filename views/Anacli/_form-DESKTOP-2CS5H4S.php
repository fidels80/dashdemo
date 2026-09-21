<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\checkbox\CheckboxX;
/* @var $this yii\web\View */
/* @var $model app\models\Anacli */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="anacli-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cd_cli')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Desk')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'localita')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cap')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cd_nazione')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'PartitaIva')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CodiceFiscale')->textInput(['maxlength' => true]) ?>


<?= $form->field($model, 'ccemail')->textInput(['maxlength' => true]) ?>
    
<?= $form->field($model, 'showprices')->widget(CheckboxX::classname(), [
    'autoLabel'=>true
  
])->label(false); ?>
 <?= $form->field($model,  'show_ins_nrgaz')->widget(CheckboxX::classname(), [
    'autoLabel'=>true
  
])->label(false); ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
