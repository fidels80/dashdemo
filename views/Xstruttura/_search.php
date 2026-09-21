<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\XstrutturaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="xstruttura-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'Struttura') ?>

    <?= $form->field($model, 'Descrizione') ?>

    <?= $form->field($model, 'Citta') ?>

    <?= $form->field($model, 'Cd_cf') ?>

    <?php // echo $form->field($model, 'Partitaiva') ?>

    <?php // echo $form->field($model, 'xcheck') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
