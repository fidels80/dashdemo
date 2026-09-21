<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TodomainSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="todomain-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user') ?>

    <?= $form->field($model, 'group') ?>

    <?= $form->field($model, 'cd_cli') ?>

    <?= $form->field($model, 'priorita') ?>

    <?php // echo $form->field($model, 'progresso') ?>

    <?php // echo $form->field($model, 'id_padre') ?>

    <?php // echo $form->field($model, 'descrizione') ?>

    <?php // echo $form->field($model, 'data_inizio') ?>

    <?php // echo $form->field($model, 'data_fine') ?>

    <?php // echo $form->field($model, 'data_scadenza') ?>

    <?php // echo $form->field($model, 'stato') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
