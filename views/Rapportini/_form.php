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

if ($model->altcli !== null) {
    $model->altcli = trim((string) $model->altcli);
}
?>

<div class="rapportini-form">

    <?php $form = ActiveForm::begin(); ?>

    <h5 class="mb-3"><i class="fas fa-user"></i> Cliente e commessa</h5>
    <div class="row">
        <div class="col-md-6"><?= $form->field($model, 'cd_cli')->dropDownList($clienti, ['prompt' => 'Seleziona cliente...']) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'altcli')->dropDownList($clienti, ['prompt' => 'Nessun cliente alternativo...'])->label('Cliente alternativo') ?></div>
    </div>
    <div class="row">
        <div class="col-md-12"><?= $form->field($model, 'commessa')->dropDownList($commesse, ['prompt' => 'Seleziona sottocommessa...']) ?></div>
    </div>

    <hr>
    <h5 class="mb-3"><i class="fas fa-clock"></i> Tempi e quantità</h5>
    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'data')->input('date', [
            'value' => $model->data ? date('Y-m-d', strtotime((string) $model->data)) : '',
        ])->label('Data intervento') ?></div>
        <div class="col-md-2"><?= $form->field($model, 'ora_in')->input('time', [
            'value' => $model->ora_in ? substr((string) $model->ora_in, 0, 5) : '',
        ])->label('Ora inizio') ?></div>
        <div class="col-md-2"><?= $form->field($model, 'ora_out')->input('time', [
            'value' => $model->ora_out ? substr((string) $model->ora_out, 0, 5) : '',
        ])->label('Ora fine') ?></div>
        <div class="col-md-2"><?= $form->field($model, 'pausa_in')->input('time', [
            'value' => $model->pausa_in ? substr((string) $model->pausa_in, 0, 5) : '',
        ])->label('Pausa inizio') ?></div>
        <div class="col-md-2"><?= $form->field($model, 'pausa_out')->input('time', [
            'value' => $model->pausa_out ? substr((string) $model->pausa_out, 0, 5) : '',
        ])->label('Pausa fine') ?></div>
        <div class="col-md-1"><?= $form->field($model, 'qta')->input('number', ['step' => 'any'])->label('Q.tà') ?></div>
    </div>

    <hr>
    <h5 class="mb-3"><i class="fas fa-box"></i> Dettaglio attività</h5>
    <div class="row">
        <div class="col-md-6"><?= $form->field($model, 'cd_art')->dropDownList($articoli, ['prompt' => 'Seleziona articolo...']) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'note')->textarea(['rows' => 5]) ?></div>
    </div>

    <div class="form-group mb-0">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', $model->isNewRecord ? ['index'] : ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
