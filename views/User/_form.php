<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */

/*
'default'
'primary'
'secondary'
'info'
'danger'
'warning'
'success'
'light'
'dark'
*/
$grid=[];//array('id'=>'default','name'=>'default');
$grid[]=array('id'=>'default','Name'=>'Trasparente');
$grid[]=array('id'=>'primary','Name'=>'Blu');
$grid[]=array('id'=>'secondary','Name'=>'Grigio');
$grid[]=array('id'=>'info','Name'=>'Azzurro');
$grid[] = array('id' => 'danger', 'Name' => 'Rosso');
$grid[]=array('id'=>'warning','Name'=>'Giallo');
$grid[] = array('id' => 'success', 'Name' => 'Verde');
$grid[] = array('id' => 'light', 'Name' => 'Bianco');
$grid[] = array('id' => 'dark', 'Name' => 'Nero');




$grid2=ArrayHelper::map($grid, 'id', 'Name');
$grid3 = []; //array('id'=>'default','name'=>'default');
$grid3[] = array('id' => 'default', 'Name' => 'Trasparente');
$grid3[] = array('id' => 'primary', 'Name' => 'Blu');
$grid3[] = array('id' => 'secondary', 'Name' => 'Grigio');
$grid3[] = array('id' => 'info', 'Name' => 'Azzurro');
$grid3[] = array('id' => 'danger', 'Name' => 'Rosso');
$grid3[] = array('id' => 'warning', 'Name' => 'Giallo');
$grid3[] = array('id' => 'success', 'Name' => 'Verde');
$grid3[] = array('id' => 'light', 'Name' => 'Bianco');
$grid3[] = array('id' => 'gray', 'Name' => 'Grigio');
$grid3[] = array('id' => 'gray-dark', 'Name' => 'Grigio/nero');
$grid3[] = array('id' => 'black', 'Name' => 'Nero');

$grid3 = ArrayHelper::map($grid3, 'id', 'Name');


?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>
<?= $form->field($model,'grid_color')->widget(Select2::classname(), [
    'data' => $grid2,
    'size' => 'lg',
    'options' => ['placeholder' => 'seleziona locazione ...'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
 ?>
 <?= $form->field($model,'sidebar_color')->widget(Select2::classname(), [
    'data' => $grid3,
    'size' => 'lg',
    'options' => ['placeholder' => 'seleziona locazione ...'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
 ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
