<?php
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Ar;
use app\models\Listini;

$ar=ar::find()
->select(['Cd_AR as id','Descrizione'])
->asArray()
->all();
$listar = ArrayHelper::map($ar, 'id', 'Descrizione');

$id                  = Yii::$app->request->get('id');
$model['id_sub_prv'] = $id;

$xls= Listini::find()
->select(['Cd_LS as id','Descrizione'])
->asArray()
->all();
$listls=ArrayHelper::map($xls, 'id', 'Descrizione');




//$model['sequenza']   = $id;

/* @var $this yii\web\View */
/* @var $model app\models\Gacmateriali */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gacmateriali-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_sub_prv')->textInput(['readonly'=>true,'hidden'=>true])->label('') ?>
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'listino')->widget(Select2::classname(),
    ['data'         => $listls,
        'options'       => ['placeholder' => 'Seleziona Listino ...',
            'id'                              => 'listino'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>
</div>
    <div class="col-sm">

    <?= $form->field($model, 'cd_ar')->widget(Select2::classname(),
    ['data'         => $listar,
        'options'       => ['placeholder' => 'Seleziona Art ...',
            'id'                              => 'cd_ar'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>
</div>
</div> 
<div class="row">   
<div class="col-sm">
    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>
</div>
</div>
<div class="row">   
<div class="col-sm">

    <?= $form->field($model, 'qta')->textInput() ?>
</div>
<div class="col-sm">

    <?= $form->field($model, 'um')->textInput(['maxlength' => true]) ?>
</div>
</div>
<div class="row">   
<div class="col-sm">
    <?= $form->field($model, 'costounitario')->textInput()->label('Costo Unitario') ?>
</div>
<div class="col-sm">

    <?= $form->field($model, 'scontoacq')->textInput()->label('Sconto su acq.') ?>
</div>
<div class="col-sm">

    <?= $form->field($model, 'costounitscontato')->textInput()->label('Costo Netto') ?>
</div>
</div>
<div class="row">   
<div class="col-sm">
    <?= $form->field($model, 'ricarico')->textInput()->label('% Ricarico') ?>
</div>
<div class="col-sm">
    <?= $form->field($model, 'costounitarioric')->textInput()->label('Prezzo Rica.') ?>
</div>
<div class="col-sm">
    <?= $form->field($model, 'sconto_vendita')->textInput()->label('Sconto Ven.') ?>
</div>
<div class="col-sm">
    <?= $form->field($model, 'valvendita')->textInput()->label('Vendita Net.') ?>
</div>
</div>
<div class="row">   
<div class="col-sm">
    <?= $form->field($model, 'margine')->textInput()->label('Margine') ?>
</div>
<div class="col-sm">

<?= $form->field($model, 'margineperc')->textInput()->label('Margine %') ?>
</div>
<div class="col-sm">
    
<?= $form->field($model, 'prezzounitarionetto')->textInput()->label('Prezzo Unit.Net.') ?>
</div>
</div>
<div class="row">   

<div class="col-sm">

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>
</div>
</div>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
