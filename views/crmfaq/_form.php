<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgCrmFaq */
/* @var $articoli array */
?>
<div class="crmfaq-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'id_articolo')->dropDownList($articoli, ['prompt' => 'Nessun articolo']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'codice_articolo')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'attivo')->checkbox() ?>
        </div>
    </div>

    <?= $form->field($model, 'domanda')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'soluzione')->textarea(['rows' => 6, 'maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
