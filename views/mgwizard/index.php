<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $stato array */
/* @var $modelli array */
/* @var $aliquote array */
/* @var $unita array */

$this->title = 'Wizard prodotti';
$this->params['breadcrumbs'][] = ['label' => 'Articoli', 'url' => ['mgarticolo/index']];
$this->params['breadcrumbs'][] = $this->title;

$defaults = $stato['defaults'] ?? [];
$idModelloSel = $stato['id_modello'] ?? null;
?>
<div class="mgwizard-index card p-3 shadow-sm">
    <?= $this->render('_steps', ['attivo' => 1]) ?>

    <h5 class="mb-3">1. Modello e dati base</h5>

    <?= Html::beginForm(['index'], 'post', ['id' => 'wizard-modello']) ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Modello esistente</label>
                <?= Html::dropDownList('id_modello', $idModelloSel, $modelli, [
                    'prompt' => 'Seleziona un modello...',
                    'class' => 'form-control',
                    'id' => 'wz-id-modello',
                ]) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>...oppure crea un nuovo modello</label>
                <div class="input-group">
                    <?= Html::textInput('modello_nuovo', '', ['class' => 'form-control', 'placeholder' => 'Descrizione nuovo modello', 'id' => 'wz-modello-nuovo']) ?>
                    <?= Html::textInput('modello_codice', '', ['class' => 'form-control', 'placeholder' => 'Codice', 'id' => 'wz-modello-codice']) ?>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <h6 class="text-muted mb-3">Dati di default applicati a tutti gli articoli generati</h6>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label>Prezzo</label>
                <?= Html::textInput('prezzo', $defaults['prezzo'] ?? '0', ['type' => 'number', 'step' => '0.0001', 'class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>IVA vendita</label>
                <?= Html::dropDownList('id_iva_vendita', $defaults['id_iva_vendita'] ?? null, $aliquote, ['prompt' => '--', 'class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>IVA acquisto</label>
                <?= Html::dropDownList('id_iva_acquisto', $defaults['id_iva_acquisto'] ?? null, $aliquote, ['prompt' => '--', 'class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Unità di misura</label>
                <?= Html::dropDownList('id_unita_misura', $defaults['id_unita_misura'] ?? null, $unita, ['prompt' => '--', 'class' => 'form-control']) ?>
            </div>
        </div>
    </div>

    <div class="form-group mb-0">
        <?= Html::submitButton('<i class="fas fa-arrow-right"></i> Avanti', ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fas fa-redo"></i> Ricomincia', ['reset'], ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('Annulla', ['mgarticolo/index'], ['class' => 'btn btn-link']) ?>
    </div>
    <?= Html::endForm() ?>
</div>

<script>
(function () {
    var $sel = $('#wz-id-modello');
    var $nuovo = $('#wz-modello-nuovo');
    var $cod = $('#wz-modello-codice');

    function sync() {
        var esiste = !!$sel.val();
        $nuovo.prop('disabled', esiste);
        $cod.prop('disabled', esiste);
    }
    $sel.on('change', sync);
    sync();
})();
</script>
