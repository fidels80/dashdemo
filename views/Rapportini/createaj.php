<?php
use yii\helpers\ArrayHelper;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\Ar;
use app\models\Dosottocommessa;
use app\models\anacli;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */



$sottoc = ArrayHelper::map(
    dosottocommessa::find()
        ->select(['Cd_DOSottoCommessa as id', '(Cd_DOSottoCommessa+\'-\'+Descrizione) as Descrizione'])
        ->orderBy('cd_DoSottoCommessa')->asArray()->all(), 'id', 'Descrizione'

);
$art=ArrayHelper::map(
Ar::find()
->select(['Cd_AR','(Cd_AR+\'-\'+Descrizione) as Descrizione'])
  ->orderBy('Cd_AR')->asArray()->all(), 'Cd_AR', 'Descrizione'

);
$clif=ArrayHelper::map(
Anacli::find()
->select(['cd_cli','(cd_cli+\'-\'+Desk) as desk'])
  ->orderBy('cd_cli')->asArray()->all(), 'cd_cli', 'desk'

);



?>
<div class="rapportini-create">

    <h1> Inserisci Rapportino</h1>

    
<div class="rapportini-form">
 <?php   $form = ActiveForm::begin([ 'enableClientValidation' => true,'options' => [
        'validateOnSubmit' => true,
        'class' => 'form'
    ]]); 


     
     ?>
      <?= $form->field($model, 'userid')->textInput(['readonly' => true,
       'value' =>Yii::$app->user->identity->id])   ?>
    <div class="row">
    <div class="col">
  <?php echo $form->field($model, 'cd_cli')
  
   ->widget(Select2::classname(), [
    'data' => $clif,
    'options' => ['placeholder' => 'Seleziona Cliente'],
    'pluginOptions' => [
        'allowClear' => true,
       'dropdownParent' => '#cli3'
    ],
])    ->label('Cliente') 
  ?></div> <div class="col">

    <?php  echo $form->field($model, 'commessa')
    //textInput(['maxlength' => true])
     ->widget(Select2::classname(), [
    'data' => $sottoc,
    'options' => ['placeholder' => 'Seleziona Commessa'],
    'pluginOptions' => [
        'allowClear' => true,
         'dropdownParent' => '#cli3'
    ],
])    ->label('Commessa') 
     ?>
      </div></div>

 <div class="row">
    <div class="col">
         <?= $form->field($model, 'data')->widget(DatePicker::classname(), [
    'options' => ['placeholder' => 'Inserisci Data Intervento'],
    'pluginOptions' => [
        'autoclose' => true,
    ],
]);
 ?>
</div>
<div class="col">
 <?= $form->field($model, 'ora_in')->textInput(['type'=>'time']) ?>
</div>
    <div class="col">
    <?= $form->field($model, 'ora_out')->textInput(['type'=>'time']) ?>
</div>
</div>
<div class="row">
    <div class="col">
 <?php echo 
 $form->field($model, 'cd_art')
 //textInput(['maxlength' => true]) 
  ->widget(Select2::classname(), [
    'data' => $art,
    'options' => ['placeholder' => 'Seleziona Articolo'],
    'pluginOptions' => [
        'allowClear' => true,
         'dropdownParent' => '#cli3'
    ],
])    ->label('Articolo') 
 ?></div>
 <div class="col">
 <?= $form->field($model, 'qta')->textInput() ?>
</div></div>
     <?= $form->field($model, 'note')->textarea(['rows' => 6,'required'=>false])
    ->label('Nota') ?>
 
    <div class="form-group">
        <?php echo  Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
</tbody></table>
    <?php ActiveForm::end(); ?>

</div>

</div>
