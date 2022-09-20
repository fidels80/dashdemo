<?php

use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\models\relcli;
use app\models\anacli;


use app\models\CliDest;
$annmodel = relcli::find()
    ->select(['altcli as id', 'ana_cli.Desk as  Name'])
    ->leftjoin('ana_cli',['ana_cli.cd_cli'=>'relcli.altcli'])
    -> where(['relcli.cd_cli' => $model->cd_cli])->asArray()
    ->all();

$listdest = ArrayHelper::map($annmodel, 'id', 'Name');


/* @var $this yii\web\View */
/* @var $model app\models\Doc_head */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="doc-head-form">

    <?php $form = ActiveForm::begin();?>
    
    
 <h1>Per Favore inserire la sede</h1>
    <?php echo $form->field($model, 'id')->textInput(['hidden' => true])->label('')?>

    <?php echo $form->field($model, 'dest')->widget(Select2::classname(), [
    'data' => $listdest,
    'options' => ['placeholder' => 'Selziona sede'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])    ->label('Intestatario') ?>

 
 <?= Html::submitButton('Conferma', ['class' => 'btn btn-success'     ,   
 'data' => [
            'confirm' => 'Sei sicuro di voler Confermare la SEDE?',
            'method' => 'post',
        ]]) ?>
    <?php ActiveForm::end();?>

</div>
