<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GacspeseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gacspese-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_sub_prv') ?>

    <?= $form->field($model, 'spesa') ?>

    <?= $form->field($model, 'descrizione') ?>

    <?= $form->field($model, 'qta') ?>

    <?php // echo $form->field($model, 'um') ?>

    <?php // echo $form->field($model, 'costounitario') ?>

    <?php // echo $form->field($model, 'sconto') ?>

    <?php // echo $form->field($model, 'costonetto') ?>

    <?php // echo $form->field($model, 'ricarico') ?>

    <?php // echo $form->field($model, 'costoricaricato') ?>

    <?php // echo $form->field($model, 'scontovendita') ?>

    <?php // echo $form->field($model, 'valorenettounitario') ?>

    <?php // echo $form->field($model, 'valorenetto') ?>

    <?php // echo $form->field($model, 'margine') ?>

    <?php // echo $form->field($model, 'margineperc') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'cd_ar') ?>

    <?php // echo $form->field($model, 'descrizionear') ?>

    <?php // echo $form->field($model, 'prezzoar') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
