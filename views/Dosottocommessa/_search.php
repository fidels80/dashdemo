<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DosottocommessaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="dosottocommessa-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'Id_DOSottoCommessa') ?>

    <?= $form->field($model, 'Cd_DOCommessa') ?>

    <?= $form->field($model, 'Cd_DOSottoCommessa') ?>

    <?= $form->field($model, 'Descrizione') ?>

    <?= $form->field($model, 'DescrizioneBreve') ?>

    <?php // echo $form->field($model, 'Cd_CF') ?>

    <?php // echo $form->field($model, 'Cd_DOCommessaStato') ?>

    <?php // echo $form->field($model, 'DataInizio') ?>

    <?php // echo $form->field($model, 'DataFinePresunta') ?>

    <?php // echo $form->field($model, 'DataFineReale') ?>

    <?php // echo $form->field($model, 'NoteDoSottoCommessa') ?>

    <?php // echo $form->field($model, 'UserIns') ?>

    <?php // echo $form->field($model, 'UserUpd') ?>

    <?php // echo $form->field($model, 'TimeIns') ?>

    <?php // echo $form->field($model, 'TimeUpd') ?>

    <?php // echo $form->field($model, 'Ts') ?>

    <?php // echo $form->field($model, 'NoteXML') ?>

    <?php // echo $form->field($model, 'Attributi') ?>

    <?php // echo $form->field($model, 'Sconto') ?>

    <?php // echo $form->field($model, 'Provvigione') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
