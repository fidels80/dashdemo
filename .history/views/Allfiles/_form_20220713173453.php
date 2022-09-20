<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/* @var $this yii\web\View */
/* @var $model app\models\Allfiles */
/* @var $form yii\widgets\ActiveForm */
$request = Yii::$app->request;
$get = $request->get();
if(!empty($get)){
    yii::error($get);
}
/*
if (array_key_exists('idagenda', $get)) {
$fid=$get['idagenda'];
}else{
$fid=null;
*/
//}
yii::warning($get);
?>

<div class="allfiles-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_padre')->textInput(['value' => $get['xid_testa']]) ?>

    <?php //$form->field($model, 'f_content')->textInput() ?>
    <?php $form->field($model, 'origine')->textInput(['hidden'=>true ]) ->label('')?>

    <?php echo $form->field($model, 'entita')->textInput(['maxlength' => true,
    'value' =>$get['tab'],'hidden'=>true ])->label('') ?>

    <?php //$form->field($model, 'nomefile')->textInput(['maxlength' => true]) ?>

    <?PHP //$form->field($model, 'estensione')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'f_content')->fileInput()->label('segli File')?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
