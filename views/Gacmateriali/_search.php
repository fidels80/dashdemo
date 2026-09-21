<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GacmaterialiSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gacmateriali-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_sub_prv') ?>

    <?= $form->field($model, 'listino') ?>

    <?= $form->field($model, 'cd_ar') ?>

    <?= $form->field($model, 'descrizione') ?>

    <?php // echo $form->field($model, 'qta') ?>

    <?php // echo $form->field($model, 'um') ?>

    <?php // echo $form->field($model, 'costounitario') ?>

    <?php // echo $form->field($model, 'scontoacq') ?>

    <?php // echo $form->field($model, 'costounitscontato') ?>

    <?php // echo $form->field($model, 'ricarico') ?>

    <?php // echo $form->field($model, 'costounitarioric') ?>

    <?php // echo $form->field($model, 'sconto_vendita') ?>

    <?php // echo $form->field($model, 'valvendita') ?>

    <?php // echo $form->field($model, 'margine') ?>

    <?php // echo $form->field($model, 'margineperc') ?>

    <?php // echo $form->field($model, 'prezzounitarionetto') ?>

    <?php // echo $form->field($model, 'note') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
