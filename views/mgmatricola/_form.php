<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\models\MgArticolo;

/* @var $this yii\web\View */
/* @var $model app\models\MgMatricola */

$articoli = ArrayHelper::map(
    MgArticolo::find()->orderBy(['descrizione' => SORT_ASC])->all(),
    'id',
    function ($a) {
        return $a->codice . ' - ' . $a->descrizione;
    }
);
?>
<div class="mgmatricola-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card mb-3">
        <div class="card-header">Dati matricola</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><?= $form->field($model, 'matricola')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-5"><?= $form->field($model, 'id_articolo')->widget(Select2::classname(), [
                    'data' => $articoli,
                    'options' => ['placeholder' => 'Cerca articolo per codice o descrizione...'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?></div>
                <div class="col-md-3"><?= $form->field($model, 'attivo')->checkbox() ?></div>
            </div>
            <div class="row">
                <div class="col-md-12"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
            </div>
            <div class="row">
                <div class="col-md-12"><?= $form->field($model, 'nota')->textarea(['rows' => 2, 'maxlength' => true]) ?></div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
