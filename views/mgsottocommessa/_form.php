<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgSottocommessa */
/* @var $commesse array */
/* @var $anagrafiche array */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="mgsottocommessa-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3"><?= $form->field($model, 'attivo')->checkbox() ?></div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'id_commessa')->dropDownList($commesse, ['prompt' => 'Seleziona commessa...']) ?>
        </div>
        <div class="col-md-3"><?= $form->field($model, 'data_inizio')->input('date') ?></div>
        <div class="col-md-3"><?= $form->field($model, 'data_fine')->input('date') ?></div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'id_anagrafica')->dropDownList($anagrafiche, ['prompt' => 'Nessuna (facoltativa)']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
