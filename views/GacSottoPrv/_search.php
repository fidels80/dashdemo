<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GacsottoprvSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gacsottoprv-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id_sub_prv') ?>

    <?= $form->field($model, 'descrizione') ?>

    <?= $form->field($model, 'id_prv') ?>

    <?= $form->field($model, 'note') ?>

    <?= $form->field($model, 'tipologia') ?>

    <?php // echo $form->field($model, 'sottocommessa') ?>

    <?php // echo $form->field($model, 'datacreazione') ?>

    <?php // echo $form->field($model, 'inizioval') ?>

    <?php // echo $form->field($model, 'fineval') ?>

    <?php // echo $form->field($model, 'probacq') ?>

    <?php // echo $form->field($model, 'provvigione') ?>

    <?php // echo $form->field($model, 'apertura') ?>

    <?php // echo $form->field($model, 'chiusura') ?>

    <?php // echo $form->field($model, 'apertura_pianificata') ?>

    <?php // echo $form->field($model, 'chiusura_pianificata') ?>

    <?php // echo $form->field($model, 'stato') ?>

    <?php // echo $form->field($model, 'datastato') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
