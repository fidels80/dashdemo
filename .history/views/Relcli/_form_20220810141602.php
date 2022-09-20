<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\anacli;



/* @var $this yii\web\View */
/* @var $model app\models\Relcli */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="relcli-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cd_cli')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'altcli')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
