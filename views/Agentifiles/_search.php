<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AgentifilesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agentifiles-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cd_agente') ?>

    <?= $form->field($model, 'descrizione') ?>

    <?= $form->field($model, 'nota') ?>

    <?= $form->field($model, 'cartella') ?>

    <?php // echo $form->field($model, 'cartella_padre') ?>

    <?php // echo $form->field($model, 'f_content') ?>

    <?php // echo $form->field($model, 'nome_file') ?>

    <?php // echo $form->field($model, 'estenzione') ?>

    <?php // echo $form->field($model, 'uplfile') ?>

    <?php // echo $form->field($model, 'file') ?>

    <?php // echo $form->field($model, 'kiave_arch') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
