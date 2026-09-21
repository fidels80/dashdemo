<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\XtappeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="xtappe-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id_tappa') ?>

    <?= $form->field($model, 'th_id') ?>

    <?= $form->field($model, 'data') ?>

    <?= $form->field($model, 'citta') ?>

    <?= $form->field($model, 'evaso')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
