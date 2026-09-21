<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Ar;
use app\models\Listini;
/* @var $this yii\web\View */
/* @var $model app\models\Gacspese */
/* @var $form yii\widgets\ActiveForm */
$ar = ar::find()
    ->select(['Cd_AR as id', 'Descrizione'])
    ->asArray()
    ->all();
$listar = ArrayHelper::map($ar, 'id', 'Descrizione');

$id = Yii::$app->request->get('id');
$idspesa = Yii::$app->request->get('idspesa');
if(isset($idspesa)){
$model['id_sub_prv'] = $idspesa;


}else{
$model['id_sub_prv'] = $id;
}
$spese =
    [['id'        => 1,
    'Descrizione' => 'Manutenzione'],
    ['id' => 2, 'Descrizione' => 'Bolli'],
    ['id' => 3, 'Descrizione' => 'Certificati'],
    ['id' => 4, 'Descrizione' => 'Extra'],
    ['id' => 5, 'Descrizione' => 'Spedizione/Consegna'],
    ['id' => 3, 'Descrizione' => 'Trasporto'],

];
$listspese = ArrayHelper::map($spese, 'id', 'Descrizione');
//yii::warning($listtipo);

?>
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success">
        <?php echo Yii::$app->session->getFlash('success'); ?>
    </div>
<?php endif;?>
<div class="gacspese-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_sub_prv')->textInput(['readonly'=>true,
    //'hidden'=>true
    ])->label('') ?>
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'spesa')->widget(Select2::classname(),
    ['data'         => $listspese,
        'options'       => ['placeholder' => 'Seleziona spesa ...',
            'id'                              => 'spesa'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])?>
</div>
<div class="col-sm">
        
<?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>
</div>
<div class="col-sm">
   
    <?= $form->field($model, 'um')->textInput(['maxlength' => true]) ?>
</div>
<div class="col-sm">

    <?= $form->field($model, 'qta')->textInput() ?>
</div>
</div>
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'costounitario')->textInput() ?>
</div>
    <div class="col-sm">
    <?= $form->field($model, 'sconto')->textInput() ?>
</div>
<div class="col-sm">
    <?= $form->field($model, 'costonetto')->textInput() ?>
</div>
<div class="col-sm">
    <?= $form->field($model, 'ricarico')->textInput() ?>
</div>
<div class="col-sm">
    <?= $form->field($model, 'costoricaricato')->textInput() ?>
</div>
<div class="col-sm">
    <?= $form->field($model, 'scontovendita')->textInput() ?>
</div>
</div>
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'valorenettounitario')->textInput() ?>
</div>
    <div class="col-sm">

    <?= $form->field($model, 'valorenetto')->textInput() ?>
</div>
    <div class="col-sm">

<?= $form->field($model, 'margine')->textInput() ?>
</div>
    <div class="col-sm">
    <?= $form->field($model, 'margineperc')->textInput() ?>
</div>
</div>   
<div class="row">
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
    <div class="col-sm">

    <?= $form->field($model, 'descrizionear')->textInput(['maxlength' => true])->label('Descrizione') ?>
</div>
    <div class="col-sm">

    <?= $form->field($model, 'prezzoar')->textInput() ?>
</div>
</div>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
