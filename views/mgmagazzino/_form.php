<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\models\MgAnagrafica;

/* @var $this yii\web\View */
/* @var $model app\models\MgMagazzino */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="mgmagazzino-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-5"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3"><?= $form->field($model, 'id_anagrafica')->dropDownList(
            \yii\helpers\ArrayHelper::map(MgAnagrafica::find()->orderBy(['ragione_sociale' => SORT_ASC])->all(), 'id', 'ragione_sociale'),
            ['prompt' => '— Nessuna —']
        ) ?></div>
        <div class="col-md-1"><?= $form->field($model, 'attivo')->checkbox() ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
