<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GacattivitaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gacattivita-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id_attivita') ?>

    <?= $form->field($model, 'id_sub_prv') ?>

    <?= $form->field($model, 'sequenza') ?>

    <?= $form->field($model, 'attivita') ?>

    <?= $form->field($model, 'descrizione') ?>

    <?php // echo $form->field($model, 'um') ?>

    <?php // echo $form->field($model, 'tempo') ?>

    <?php // echo $form->field($model, 'ore') ?>

    <?php // echo $form->field($model, 'risorsa') ?>

    <?php // echo $form->field($model, 'costo') ?>

    <?php // echo $form->field($model, 'sconto') ?>

    <?php // echo $form->field($model, 'costo_scontato') ?>

    <?php // echo $form->field($model, 'ricarico') ?>

    <?php // echo $form->field($model, 'costo_ricarico') ?>

    <?php // echo $form->field($model, 'sconto_vendita') ?>

    <?php // echo $form->field($model, 'valore_costounitario') ?>

    <?php // echo $form->field($model, 'valore_costotot') ?>

    <?php // echo $form->field($model, 'margine') ?>

    <?php // echo $form->field($model, 'margine_perc') ?>

    <?php // echo $form->field($model, 'note') ?>

    <?php // echo $form->field($model, 'data_apertura') ?>

    <?php // echo $form->field($model, 'data_chiusura') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
