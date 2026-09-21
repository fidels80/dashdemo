<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\XroomlistSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="xroomlist-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_guest') ?>

    <?= $form->field($model, 'nominativo') ?>

    <?= $form->field($model, 'cd_ar') ?>

    <?= $form->field($model, 'th_id') ?>

    <?= $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'evaso')->checkbox() ?>

    <?php // echo $form->field($model, 'ruolo') ?>

    <?php // echo $form->field($model, 'party') ?>

    <?php // echo $form->field($model, 'commessa') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
