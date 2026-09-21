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
        ->select(['Cd_DOSottoCommessa as id', 'Descrizione'])
        ->orderBy('cd_DoSottoCommessa')->asArray()->all(), 'id', 'Descrizione'

);
$art=ArrayHelper::map(
Ar::find()
->select(['Cd_AR','Descrizione'])
  ->orderBy('Cd_AR')->asArray()->all(), 'Cd_AR', 'Descrizione'

);
$clif=ArrayHelper::map(
Anacli::find()
->select(['cd_cli','Desk'])
  ->orderBy('cd_cli')->asArray()->all(), 'cd_cli', 'Desk'

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
      <?= $form->field($model, 'userid')->textInput(['readonly' => true,'hidden'=>true
       'value' =>Yii::$app->user->identity->id]) ?>
  <?php echo $form->field($model, 'cd_cli')
  
   ->widget(Select2::classname(), [
    'data' => $clif,
    'options' => ['placeholder' => 'Seleziona Cliente'],
    'pluginOptions' => [
        'allowClear' => true,
      
    ],
])    ->label('Cliente') 
  ?>
 <td width="50%">
    <?php  echo $form->field($model, 'commessa')
    //textInput(['maxlength' => true])
     ->widget(Select2::classname(), [
    'data' => $sottoc,
    'options' => ['placeholder' => 'Seleziona Commessa'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])    ->label('Commessa') 
     ?>
      
         <?= $form->field($model, 'data')->widget(DatePicker::classname(), [
    'options' => ['placeholder' => 'Inserisci Data Intervento'],
    'pluginOptions' => [
        'autoclose' => true,
    ],
]);
 ?>

 <?= $form->field($model, 'ora_in')->textInput(['type'=>'time']) ?>

    <?= $form->field($model, 'ora_out')->textInput(['type'=>'time']) ?>
 <?php echo 
 $form->field($model, 'cd_art')
 //textInput(['maxlength' => true]) 
  ->widget(Select2::classname(), [
    'data' => $art,
    'options' => ['placeholder' => 'Seleziona Articolo'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])    ->label('Articolo') 
 ?>
 <?= $form->field($model, 'qta')->textInput() ?>
     <?= $form->field($model, 'note')->textarea(['rows' => 6,'required'=>false])
    ->label('Nota') ?>
 
    <div class="form-group">
        <?php echo  Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
</tbody></table>
    <?php ActiveForm::end(); ?>

</div>

</div>
