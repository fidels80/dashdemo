<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\ActiveForm;
use app\models\MgAnagrafica;
use app\models\MgArticolo;
use app\models\MgSottocommessa;

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */
/* @var $form yii\bootstrap4\ActiveForm */

$clienti = ArrayHelper::map(
    MgAnagrafica::find()->select(['codice', 'ragione_sociale'])->orderBy(['ragione_sociale' => SORT_ASC])->all(),
    'codice',
    function ($a) {
        return $a->codice . ' - ' . $a->ragione_sociale;
    }
);
$commesse = ArrayHelper::map(
    MgSottocommessa::find()->select(['codice', 'descrizione'])->orderBy(['codice' => SORT_ASC])->all(),
    'codice',
    function ($c) {
        return $c->codice . ' - ' . $c->descrizione;
    }
);
$articoli = ArrayHelper::map(
    MgArticolo::find()->select(['codice', 'descrizione'])->orderBy(['codice' => SORT_ASC])->all(),
    'codice',
    function ($a) {
        return $a->codice . ' - ' . $a->descrizione;
    }
);
?>

<div class="rapportini-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-row">
        <div class="col-md-6"><?= $form->field($model, 'cd_cli')->dropDownList($clienti, ['prompt' => 'Seleziona cliente...']) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'commessa')->dropDownList($commesse, ['prompt' => 'Seleziona sottocommessa...']) ?></div>
    </div>

    <div class="form-row">
        <div class="col-md-3"><?= $form->field($model, 'data')->input('date')->label('Data intervento') ?></div>
        <div class="col-md-3"><?= $form->field($model, 'ora_in')->input('time')->label('Ora inizio') ?></div>
        <div class="col-md-3"><?= $form->field($model, 'ora_out')->input('time')->label('Ora fine') ?></div>
        <div class="col-md-3"><?= $form->field($model, 'qta')->input('number', ['step' => 'any'])->label('Quantità') ?></div>
    </div>

    <div class="form-row">
        <div class="col-md-6"><?= $form->field($model, 'cd_art')->dropDownList($articoli, ['prompt' => 'Seleziona articolo...']) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'note')->textarea(['rows' => 4]) ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
