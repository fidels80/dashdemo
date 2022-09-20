<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\xsubmenu;

use kartik\select2\Select2;
use app\models\xmenu;

/* @var $this yii\web\View */
/* @var $model app\models\Usrgrp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usrgrp-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
