<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Sottocommessa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sottocommessa-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Cd_DOCommessa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cd_DOSottoCommessa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Descrizione')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DescrizioneBreve')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cd_CF')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cd_DOCommessaStato')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DataInizio')->textInput() ?>

    <?= $form->field($model, 'DataFinePresunta')->textInput() ?>

    <?= $form->field($model, 'DataFineReale')->textInput() ?>

    <?= $form->field($model, 'NoteDoSottoCommessa')->textInput() ?>

    <?= $form->field($model, 'UserIns')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'UserUpd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'TimeIns')->textInput() ?>

    <?= $form->field($model, 'TimeUpd')->textInput() ?>

    <?= $form->field($model, 'Ts')->textInput() ?>

    <?= $form->field($model, 'NoteXML')->textInput() ?>

    <?= $form->field($model, 'Attributi')->textInput() ?>

    <?= $form->field($model, 'Sconto')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Provvigione')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
