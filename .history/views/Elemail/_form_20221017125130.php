<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Elemail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="elemail-form">




<?php 
$get = $request->get();
// equivalent to: $get = $_GET;

$id = $request->get('id');
if (isset($id)){
    echo $id;

}



?>


    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'Soggetto')->textInput() ?>

    <?= $form->field($model, 'Corpo')->textInput() ?>

    <?= $form->field($model, 'allegati')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
