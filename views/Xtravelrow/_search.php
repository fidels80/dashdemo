<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\XtravelrowSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="xtravelrow-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'tr_id') ?>

    <?= $form->field($model, 'th_id') ?>

    <?= $form->field($model, 'sottocommessa') ?>

    <?= $form->field($model, 'cd_Ar') ?>

    <?= $form->field($model, 'descrizione') ?>

    <?php // echo $form->field($model, 'qta') ?>

    <?php // echo $form->field($model, 'prezzo') ?>

    <?php // echo $form->field($model, 'stato') ?>

    <?php // echo $form->field($model, 'guest') ?>

    <?php // echo $form->field($model, 'ruolo') ?>

    <?php // echo $form->field($model, 'cd_cf_ft') ?>

    <?php // echo $form->field($model, 'descli') ?>

    <?php // echo $form->field($model, 'citta') ?>

    <?php // echo $form->field($model, 'fornitore') ?>

    <?php // echo $form->field($model, 'desfor') ?>

    <?php // echo $form->field($model, 'struttura') ?>

    <?php // echo $form->field($model, 'check_in') ?>

    <?php // echo $form->field($model, 'check_out') ?>

    <?php // echo $form->field($model, 'citta_da') ?>

    <?php // echo $form->field($model, 'citta_a') ?>

    <?php // echo $form->field($model, 'orario') ?>

    <?php // echo $form->field($model, 'pnr') ?>

    <?php // echo $form->field($model, 'nr_biglietto') ?>

    <?php // echo $form->field($model, 'data_pg') ?>

    <?php // echo $form->field($model, 'cd_pg') ?>

    <?php // echo $form->field($model, 'contabile') ?>

    <?php // echo $form->field($model, 'totale') ?>

    <?php // echo $form->field($model, 'tax') ?>

    <?php // echo $form->field($model, 'fee') ?>

    <?php // echo $form->field($model, 'fee_perc') ?>

    <?php // echo $form->field($model, 'imponibile') ?>

    <?php // echo $form->field($model, 'iva') ?>

    <?php // echo $form->field($model, 'Totalegenerale') ?>

    <?php // echo $form->field($model, 'evadi_A') ?>

    <?php // echo $form->field($model, 'evadi_p') ?>

    <?php // echo $form->field($model, 'tax_unit') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'descontab') ?>

    <?php // echo $form->field($model, 'totfattura') ?>

    <?php // echo $form->field($model, 'codiva') ?>

    <?php // echo $form->field($model, 'pagato') ?>

    <?php // echo $form->field($model, 'xid') ?>

    <?php // echo $form->field($model, 'timeins') ?>

    <?php // echo $form->field($model, 'numero') ?>

    <?php // echo $form->field($model, 'datah') ?>

    <?php // echo $form->field($model, 'x_scdesc') ?>

    <?php // echo $form->field($model, 'x_pagato') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
