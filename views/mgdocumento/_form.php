<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use app\models\MgArticolo;

/* @var $this yii\web\View */
/* @var $model app\models\MgDocumento */
/* @var $tipi array */
/* @var $tipiCreaScadenze array */
/* @var $tipiDestinazione array */
/* @var $anagrafiche array */
/* @var $anagraficheMetodi array */
/* @var $anagraficheIva array */
/* @var $metodi array */
/* @var $aliquote array */
/* @var $unita array */
/* @var $sottocommesse array */
/* @var $magazzini array */
/* @var $tipiMagazzini array */
/* @var $tipiMostraVarianti array */
/* @var $tipiPrelevaRapportini array */
/* @var $tipiCreaArticoli array */
/* @var $tipiCreaAnagrafiche array */
/* @var $tipiMostraMatrice array */
/* @var $modelliMatrice array */
/* @var $righe app\models\MgDocumentoRiga[] */

$articoliModels = MgArticolo::find()->with(['ivaVendita', 'ivaAcquisto', 'unitaMisura.unitaMisura', 'taglia', 'colore'])->orderBy(['descrizione' => SORT_ASC])->all();
$stati = ['bozza' => 'Bozza', 'confermato' => 'Confermato', 'chiuso' => 'Chiuso', 'annullato' => 'Annullato'];
$isNew = $model->isNewRecord;
$tipoBloccato = !$isNew || !empty($model->id_tipo);

$anaOptions = [];
foreach ((array) $anagrafiche as $aid => $alabel) {
    $anaOptions[$aid] = [
        'data-metodo' => isset($anagraficheMetodi[$aid]) ? (string) $anagraficheMetodi[$aid] : '',
        'data-iva' => isset($anagraficheIva[$aid]) && $anagraficheIva[$aid] !== null ? (string) $anagraficheIva[$aid] : '',
    ];
}

$tipoOptions = [];
foreach ((array) $tipi as $tid => $tlabel) {
    $tipoOptions[$tid] = [
        'data-crea-scadenze' => isset($tipiCreaScadenze[$tid]) ? (int) $tipiCreaScadenze[$tid] : 0,
        'data-destinazione' => isset($tipiDestinazione[$tid]) ? $tipiDestinazione[$tid] : 'cliente',
        'data-mostra-varianti' => isset($tipiMostraVarianti[$tid]) ? (int) $tipiMostraVarianti[$tid] : 0,
        'data-preleva-rapportini' => isset($tipiPrelevaRapportini[$tid]) ? (int) $tipiPrelevaRapportini[$tid] : 0,
        'data-crea-articoli' => isset($tipiCreaArticoli[$tid]) ? (int) $tipiCreaArticoli[$tid] : 0,
        'data-crea-anagrafiche' => isset($tipiCreaAnagrafiche[$tid]) ? (int) $tipiCreaAnagrafiche[$tid] : 0,
        'data-mostra-matrice' => isset($tipiMostraMatrice[$tid]) ? (int) $tipiMostraMatrice[$tid] : 0,
        'data-magazzino-partenza' => !empty($tipiMagazzini[$tid]['partenza']) ? (int) $tipiMagazzini[$tid]['partenza'] : '',
        'data-magazzino-arrivo' => !empty($tipiMagazzini[$tid]['arrivo']) ? (int) $tipiMagazzini[$tid]['arrivo'] : '',
    ];
}

$aliquoteHtml = '';
foreach ((array) $aliquote as $alid => $alabel) {
    $aliquoteHtml .= Html::tag('option', Html::encode($alabel), ['value' => $alid]);
}

$unitaHtml = Html::tag('option', 'Nessuna', ['value' => '']);
foreach ((array) $unita as $uid => $ulabel) {
    $unitaHtml .= Html::tag('option', Html::encode($ulabel), ['value' => $uid]);
}

$mostraVarianti = !empty($model->id_tipo) && !empty($tipiMostraVarianti[$model->id_tipo]);
$puoPrelevare = $model->puoPrelevare();
$idDocumento = $model->isNewRecord ? null : (int) $model->id;
?>
<div class="mgdocumento-form">

    <?php $form = ActiveForm::begin(['id' => 'mgdocumento-form']); ?>

    <div class="card mb-3">
        <div class="card-header">Dati documento</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'id_tipo')->dropDownList($tipi, [
                        'prompt' => 'Seleziona tipo...',
                        'options' => $tipoOptions,
                        'disabled' => $tipoBloccato,
                        'title' => $tipoBloccato ? 'Il tipo documento non è modificabile dopo la prima selezione' : null,
                    ]) ?>
                    <?= Html::hiddenInput(Html::getInputName($model, 'id_tipo'), $model->id_tipo, ['id' => 'mgdocumento-id_tipo-hidden']) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'data')->textInput(['type' => 'date']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'anno')->textInput(['type' => 'number']) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'numero')->textInput(['type' => 'number']) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'suffisso')->textInput(['placeholder' => 'es. bis']) ?>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="btn-proponi"
                            title="Proponi il primo numero libero">
                        <i class="fas fa-magic"></i>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <?= $form->field($model, 'id_anagrafica')->dropDownList($anagrafiche, [
                        'prompt' => 'Seleziona cliente/fornitore...',
                        'options' => $anaOptions,
                    ]) ?>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-outline-success btn-sm mt-3" id="btn-nuovo-fornitore"
                            title="Crea nuovo cliente/fornitore">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'stato')->dropDownList($stati) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'id_metodo_pagamento')->dropDownList($metodi, ['prompt' => 'Nessun metodo...']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <?= $form->field($model, 'id_sottocommessa')->dropDownList($sottocommesse, ['prompt' => 'Nessuna sottocommessa...']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 d-flex align-items-center">
                    <div class="text-muted small mt-3" id="scadenze-info">
                        <i class="fas fa-info-circle"></i>
                        <span id="scadenze-info-text">Il tipo documento selezionato non prevede la creazione automatica delle scadenze.</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'note')->textarea(['rows' => 2]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Righe documento</span>
            <div>
                <button type="button" class="btn btn-sm btn-outline-info" id="btn-matrice-taglie">
                    <i class="fas fa-table"></i> Matrice taglie
                </button>
                <?php if ($puoPrelevare): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-preleva-rapportini">
                        <i class="fas fa-clipboard-check"></i> Preleva rapportini
                    </button>
                <?php endif; ?>
                <button type="button" class="btn btn-sm btn-outline-success" id="btn-nuovo-articolo">
                    <i class="fas fa-box"></i> Nuovo articolo
                </button>
                <button type="button" class="btn btn-sm btn-primary" id="btn-add-riga">
                    <i class="fas fa-plus"></i> Aggiungi riga
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="px-2 py-2 border-bottom d-flex align-items-center flex-wrap">
                <div class="input-group input-group-sm" style="max-width:340px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" id="righe-search" class="form-control" placeholder="Cerca righe per codice, descrizione, sottocommessa, taglia, colore...">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-outline-secondary" id="righe-search-clear" title="Azzera ricerca"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <span class="ml-2 text-muted small" id="righe-search-info"></span>
                <span class="ml-auto text-muted small"><i class="fas fa-sort"></i> Clicca sull'intestazione per ordinare</span>
            </div>
            <table class="table table-sm mb-0" id="righe-table">
                <thead>
                <tr>
                    <th class="sortable" data-sort="articolo" style="width:16%">Articolo <i class="fas fa-sort sort-ind"></i></th>
                    <th class="sortable" data-sort="codice" style="width:9%">Codice <i class="fas fa-sort sort-ind"></i></th>
                    <th class="sortable" data-sort="descrizione">Descrizione <i class="fas fa-sort sort-ind"></i></th>
                    <th class="sortable" data-sort="sottocommessa" style="width:11%">Sottocommessa <i class="fas fa-sort sort-ind"></i></th>
                    <th class="col-mag-partenza" style="width:11%">Mag. partenza</th>
                    <th class="col-mag-arrivo" style="width:11%">Mag. arrivo</th>
                    <th class="sortable col-varianti" data-sort="taglia" style="width:7%">Taglia <i class="fas fa-sort sort-ind"></i></th>
                    <th class="sortable col-varianti" data-sort="colore" style="width:7%">Colore <i class="fas fa-sort sort-ind"></i></th>
                    <th class="sortable col-varianti" data-sort="tessuto" style="width:7%">Tessuto <i class="fas fa-sort sort-ind"></i></th>
                    <th class="sortable" data-sort="um" style="width:10%">U.M. <i class="fas fa-sort sort-ind"></i></th>
                    <th class="sortable" data-sort="qta" style="width:7%">Q.tà <i class="fas fa-sort sort-ind"></i></th>
                    <th class="sortable" data-sort="prezzo" style="width:8%">Prezzo <i class="fas fa-sort sort-ind"></i></th>
                    <th class="sortable" data-sort="sconto" style="width:6%">Sc. % <i class="fas fa-sort sort-ind"></i></th>
                    <th class="sortable" data-sort="iva" style="width:6%">IVA % <i class="fas fa-sort sort-ind"></i></th>
                    <th class="sortable" data-sort="totale" style="width:8%">Totale <i class="fas fa-sort sort-ind"></i></th>
                    <th style="width:8%"></th>
                </tr>
                </thead>
                <tbody id="righe-body">
                <?php foreach ($righe as $i => $r): ?>
                    <?= $this->render('_riga', ['index' => $i, 'model' => $r, 'articoliModels' => $articoliModels, 'sottocommesse' => $sottocommesse, 'magazzini' => $magazzini]) ?>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th id="righe-totale-label" colspan="12" class="text-right">Totale documento</th>
                    <th class="text-right" id="totale-documento">0,00</th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card mb-3" id="card-scadenze" style="display:none;">
        <div class="card-header">Scadenze generate dal metodo di pagamento</div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                <tr>
                    <th style="width:12%">Rata</th>
                    <th>Data scadenza</th>
                    <th class="text-right" style="width:20%">%</th>
                    <th class="text-right" style="width:25%">Importo</th>
                </tr>
                </thead>
                <tbody id="scadenze-body"></tbody>
            </table>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <!-- template riga nascosta -->
    <table style="display:none;">
        <tbody>
        <?= $this->render('_riga', ['index' => '__INDEX__', 'model' => null, 'articoliModels' => $articoliModels, 'sottocommesse' => $sottocommesse, 'magazzini' => $magazzini]) ?>
        </tbody>
    </table>

    <!-- Modale nuovo articolo -->
    <div class="modal fade" id="modal-nuovo-articolo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white"><i class="fas fa-box"></i> Nuovo articolo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Codice *</label>
                        <input type="text" id="na-codice" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Descrizione *</label>
                        <input type="text" id="na-descrizione" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>U.M.</label>
                                <div class="input-group">
                                    <select id="na-um" class="form-control"><?= $unitaHtml ?></select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-success" id="btn-na-nuova-um" title="Nuova unità di misura"><i class="fas fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"><div class="form-group"><label>Prezzo</label><input type="number" step="any" id="na-prezzo" class="form-control" value="0"></div></div>
                    </div>
                    <div class="row" id="na-nuova-um-box" style="display:none;">
                        <div class="col-md-4"><input type="text" id="na-um-codice" class="form-control form-control-sm" maxlength="10" placeholder="Codice (es. PLT)"></div>
                        <div class="col-md-5"><input type="text" id="na-um-descrizione" class="form-control form-control-sm" placeholder="Descrizione (es. Pallet)"></div>
                        <div class="col-md-3"><button type="button" class="btn btn-sm btn-success btn-block" id="btn-na-crea-um">Crea unità</button></div>
                        <div class="col-md-12"><div id="na-um-error" class="text-danger small"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>IVA vendita</label>
                            <select id="na-iva-vendita" class="form-control"><?= $aliquoteHtml ?></select>
                        </div></div>
                        <div class="col-md-6"><div class="form-group"><label>IVA acquisto</label>
                            <select id="na-iva-acquisto" class="form-control"><?= $aliquoteHtml ?></select>
                        </div></div>
                    </div>
                    <div id="na-error" class="text-danger small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-success" id="btn-salva-articolo"><i class="fas fa-save"></i> Crea articolo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modale matrice taglie -->
    <div class="modal fade" id="modal-matrice" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white"><i class="fas fa-table"></i> Matrice taglie</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Modello</label>
                        <select id="mat-modello" class="form-control">
                            <option value="">Seleziona modello...</option>
                            <?php foreach ((array) $modelliMatrice as $mid => $mlabel): ?>
                                <option value="<?= (int) $mid ?>"><?= Html::encode($mlabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="mat-loading" class="text-muted small" style="display:none;">Caricamento...</div>
                    <div id="mat-error" class="text-danger small"></div>
                    <div id="mat-container" class="table-responsive" style="max-height:55vh;overflow:auto;"></div>
                </div>
                <div class="modal-footer">
                    <span class="mr-auto text-muted small" id="mat-info"></span>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-info" id="btn-mat-genera"><i class="fas fa-plus"></i> Genera righe</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modale preleva rapportini -->
    <div class="modal fade" id="modal-preleva-rapportini" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-secondary">
                    <h5 class="modal-title text-white"><i class="fas fa-clipboard-check"></i> Preleva rapportini</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center flex-wrap mb-2">
                        <div class="input-group input-group-sm" style="max-width:340px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" id="rap-search" class="form-control" placeholder="Cerca per numero, cliente, commessa, articolo...">
                        </div>
                        <span class="ml-2 text-muted small" id="rap-search-info"></span>
                    </div>
                    <div class="table-responsive" style="max-height:55vh;overflow:auto;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                            <tr>
                                <th class="text-center" style="width:36px;">
                                    <input type="checkbox" id="rap-check-all" title="Seleziona tutti">
                                </th>
                                <th style="width:70px;">N.</th>
                                <th style="width:90px;">Data</th>
                                <th>Cliente</th>
                                <th>Sottocommessa</th>
                                <th>Articolo</th>
                                <th class="text-right" style="width:70px;">Q.tà</th>
                                <th style="width:110px;">Ore</th>
                            </tr>
                            </thead>
                            <tbody id="rap-list-body">
                            <tr><td colspan="8" class="text-muted">Caricamento...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="rap-error" class="text-danger small mt-2"></div>
                </div>
                <div class="modal-footer">
                    <span class="mr-auto text-muted small" id="rap-info">Seleziona i rapportini da prelevare</span>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-secondary" id="btn-rap-genera"><i class="fas fa-plus"></i> Genera righe</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modale nuovo cliente/fornitore -->
    <div class="modal fade" id="modal-nuovo-fornitore" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white"><i class="fas fa-users"></i> Nuovo cliente/fornitore</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Codice *</label><input type="text" id="nf-codice" class="form-control"></div></div>
                        <div class="col-md-8"><div class="form-group"><label>Ragione sociale *</label><input type="text" id="nf-ragione" class="form-control"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>Partita IVA</label><input type="text" id="nf-piva" class="form-control"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Città</label><input type="text" id="nf-citta" class="form-control"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Ruoli</label>
                            <div class="form-check"><input type="checkbox" class="form-check-input" id="nf-cliente"><label class="form-check-label" for="nf-cliente">Cliente</label></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" id="nf-fornitore" checked><label class="form-check-label" for="nf-fornitore">Fornitore</label></div>
                            <div class="form-check"><input type="checkbox" class="form-check-input" id="nf-agente"><label class="form-check-label" for="nf-agente">Agente</label></div>
                        </div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Telefono</label><input type="text" id="nf-telefono" class="form-control"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Email</label><input type="text" id="nf-email" class="form-control"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12"><div class="form-group"><label>Aliquota IVA</label>
                            <select id="nf-iva" class="form-control"><option value="">Nessuna (usa quella articolo)</option><?= $aliquoteHtml ?></select>
                        </div></div>
                    </div>
                    <div id="nf-error" class="text-danger small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-success" id="btn-salva-fornitore"><i class="fas fa-save"></i> Crea</button>
                </div>
            </div>
        </div>
    </div>

    <?= $this->render('_rapportino_modal') ?>
</div>

<script>
(function () {
    var isNew = <?= $isNew ? 'true' : 'false' ?>;
    var puoPrelevare = <?= $puoPrelevare ? 'true' : 'false' ?>;
    var proponiUrl = '<?= \yii\helpers\Url::to(['mgdocumento/proponi-numero']) ?>';

    function formatTotale(v) {
        return v.toFixed(2).replace('.', ',');
    }

    function ricalcolaRiga($row) {
        var qta = parseFloat($row.find('.riga-qta').val()) || 0;
        var prezzo = parseFloat($row.find('.riga-prezzo').val()) || 0;
        var sconto = parseFloat($row.find('.riga-sconto').val()) || 0;
        var tot = qta * prezzo * (1 - sconto / 100);
        $row.find('.riga-totale').val(formatTotale(tot));
        ricalcolaTotale();
    }

    function ricalcolaTotale() {
        var tot = 0;
        $('#righe-body .riga-row').each(function () {
            var v = parseFloat($(this).find('.riga-totale').val().replace('.', '').replace(',', '.')) || 0;
            tot += v;
        });
        $('#totale-documento').text(formatTotale(tot));
        aggiornaScadenze();
    }

    function aggiornaIndici() {
        $('#righe-body .riga-row').each(function (i) {
            $(this).find('select, input').each(function () {
                var name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/righe\[[^\]]*\]/, 'righe[' + i + ']'));
                }
            });
        });
    }

    // --- SOTTOCOMMESSA: PROPOSTA DALLA TESTATA ALLE RIGHE ---
    function sottocommessaTestata() {
        return $('#mgdocumento-id_sottocommessa').val() || '';
    }

    function applicaSottocommessaRiga($row) {
        $row.find('.riga-sottocommessa').val(sottocommessaTestata());
    }

    function aggiornaSottocommessaRighe() {
        var $testata = $('#mgdocumento-id_sottocommessa');
        var nuovo = $testata.val() || '';
        var precedente = $testata.data('precedente') || '';
        $('#righe-body .riga-row').each(function () {
            var $sel = $(this).find('.riga-sottocommessa');
            if (($sel.val() || '') === precedente) {
                $sel.val(nuovo);
            }
        });
        $testata.data('precedente', nuovo);
    }

    // --- VISIBILITA' COLONNE TAGLIA/COLORE E MAGAZZINI ---
    function tipoOption() {
        return $('#mgdocumento-id_tipo option:selected');
    }

    function magazzinoTipo(campo) {
        var v = tipoOption().attr('data-magazzino-' + campo);
        return (v === undefined || v === null || v === '') ? '' : String(v);
    }

    function aggiornaColonneDinamiche() {
        $('.col-varianti').toggle(String(tipoOption().attr('data-mostra-varianti')) === '1');
        $('.col-mag-partenza').toggle(!!magazzinoTipo('partenza'));
        $('.col-mag-arrivo').toggle(!!magazzinoTipo('arrivo'));
        var visibili = $('#righe-table thead th:visible').length;
        if (visibili > 0) {
            $('#righe-totale-label').attr('colspan', visibili - 2);
        }
    }

    // --- MAGAZZINI DI DEFAULT SULLE RIGHE ---
    function applicaMagazziniRiga($row) {
        var partenza = magazzinoTipo('partenza');
        var arrivo = magazzinoTipo('arrivo');
        if (partenza && !$row.find('.riga-mag-partenza').val()) {
            $row.find('.riga-mag-partenza').val(partenza);
        }
        if (arrivo && !$row.find('.riga-mag-arrivo').val()) {
            $row.find('.riga-mag-arrivo').val(arrivo);
        }
    }

    function applicaMagazziniRigheVuote() {
        $('#righe-body .riga-row').each(function () {
            applicaMagazziniRiga($(this));
        });
    }

    // --- VISIBILITA' PULSANTI IN BASE AL TIPO DOCUMENTO ---
    function tipoFlag(nome) {
        var v = $('#mgdocumento-id_tipo option:selected').attr('data-' + nome);
        return String(v) === '1';
    }

    function aggiornaPulsantiTipo() {
        $('#btn-nuovo-articolo').toggle(tipoFlag('crea-articoli'));
        $('#btn-nuovo-fornitore').toggle(tipoFlag('crea-anagrafiche'));
        $('#btn-matrice-taglie').toggle(tipoFlag('mostra-matrice'));
        if (puoPrelevare) {
            $('#btn-preleva-rapportini').toggle(tipoFlag('preleva-rapportini'));
        }
    }

    // --- RICERCA RIGHE ---
    function filtraRighe() {
        var q = ($('#righe-search').val() || '').toLowerCase().trim();
        var vis = 0, tot = 0;
        $('#righe-body .riga-row').each(function () {
            var $r = $(this);
            tot++;
            var testo = [
                $r.find('.riga-articolo option:selected').text(),
                $r.find('.riga-codice').val(),
                $r.find('.riga-desc').val(),
                $r.find('.riga-sottocommessa option:selected').text(),
                $r.find('.riga-mag-partenza option:selected').text(),
                $r.find('.riga-mag-arrivo option:selected').text(),
                $r.find('.riga-taglia').val(),
                $r.find('.riga-colore').val(),
                $r.find('.riga-tessuto').val(),
                $r.find('.riga-um option:selected').text()
            ].join(' ').toLowerCase();
            var ok = (q === '' || testo.indexOf(q) !== -1);
            $r.toggle(ok);
            if (ok) { vis++; }
        });
        $('#righe-search-info').text(q === '' ? '' : (vis + ' di ' + tot + ' righe'));
    }

    // --- ORDINAMENTO COLONNE ---
    var sortKey = null, sortDir = 1;
    function valoreSort($row, key) {
        switch (key) {
            case 'articolo': return ($row.find('.riga-articolo option:selected').text() || '').trim().toLowerCase();
            case 'codice': return ($row.find('.riga-codice').val() || '').toLowerCase();
            case 'descrizione': return ($row.find('.riga-desc').val() || '').toLowerCase();
            case 'sottocommessa': return ($row.find('.riga-sottocommessa option:selected').text() || '').trim().toLowerCase();
            case 'taglia': return ($row.find('.riga-taglia').val() || '').toLowerCase();
            case 'colore': return ($row.find('.riga-colore').val() || '').toLowerCase();
            case 'tessuto': return ($row.find('.riga-tessuto').val() || '').toLowerCase();
            case 'um': return ($row.find('.riga-um option:selected').text() || '').toLowerCase();
            case 'qta': return parseFloat($row.find('.riga-qta').val()) || 0;
            case 'prezzo': return parseFloat($row.find('.riga-prezzo').val()) || 0;
            case 'sconto': return parseFloat($row.find('.riga-sconto').val()) || 0;
            case 'iva': return parseFloat($row.find('.riga-iva').val()) || 0;
            case 'totale': return parseFloat(($row.find('.riga-totale').val() || '0').replace(/\./g, '').replace(',', '.')) || 0;
        }
        return '';
    }
    function aggiornaIndicatoriSort(key) {
        $('#righe-table thead th.sortable').each(function () {
            var $i = $(this).find('.sort-ind');
            if ($(this).data('sort') === key) {
                $i.removeClass('fa-sort fa-sort-up fa-sort-down')
                    .addClass(sortDir === 1 ? 'fa-sort-up' : 'fa-sort-down').css('opacity', 1);
            } else {
                $i.removeClass('fa-sort-up fa-sort-down').addClass('fa-sort').css('opacity', .35);
            }
        });
    }
    function ordinaRighe(key) {
        if (sortKey === key) { sortDir = -sortDir; } else { sortKey = key; sortDir = 1; }
        var rows = $('#righe-body .riga-row').get();
        rows.sort(function (a, b) {
            var va = valoreSort($(a), key), vb = valoreSort($(b), key);
            if (va < vb) { return -sortDir; }
            if (va > vb) { return sortDir; }
            return 0;
        });
        var $body = $('#righe-body');
        $.each(rows, function (i, r) { $body.append(r); });
        aggiornaIndici();
        aggiornaIndicatoriSort(key);
        filtraRighe();
    }
    $('#righe-table').on('click', 'th.sortable', function () {
        ordinaRighe($(this).data('sort'));
    });
    $('#righe-search').on('input', filtraRighe);
    $('#righe-search-clear').on('click', function () {
        $('#righe-search').val('');
        filtraRighe();
    });

    $(document).on('input change', '.riga-qta, .riga-prezzo, .riga-sconto', function () {
        ricalcolaRiga($(this).closest('.riga-row'));
    });

    function destinazioneTipo() {
        return $('#mgdocumento-id_tipo option:selected').attr('data-destinazione') || 'cliente';
    }

    function aliquotaSoggetto() {
        var v = $('#mgdocumento-id_anagrafica option:selected').attr('data-iva');
        return (v === undefined || v === null || v === '') ? null : v;
    }

    function ivaArticolo($opt) {
        var dest = destinazioneTipo();
        var v = (dest === 'fornitore') ? $opt.attr('data-iva-acquisto') : $opt.attr('data-iva-vendita');
        if (v === undefined || v === '') { v = $opt.attr('data-iva'); }
        return v;
    }

    function applicaAliquoteRighe() {
        var ivaSogg = aliquotaSoggetto();
        $('#righe-body .riga-row').each(function () {
            var $row = $(this);
            var $opt = $row.find('.riga-articolo option:selected');
            if (!$opt.val()) { return; }
            var val = (ivaSogg !== null) ? ivaSogg : ivaArticolo($opt);
            if (val !== undefined && val !== '') {
                $row.find('.riga-iva').val(val);
            }
            ricalcolaRiga($row);
        });
    }

    function parseUm(opt) {
        var raw = opt.attr('data-um');
        if (!raw) { return []; }
        try { return JSON.parse(raw); } catch (e) { return []; }
    }

    function aggiornaFattore($row) {
        var $um = $row.find('.riga-um option:selected');
        var fattore = parseFloat($um.attr('data-fattore'));
        if (isNaN(fattore) || fattore <= 0) { fattore = 1; }
        $row.find('.riga-fattore').val(fattore);
        $row.find('.riga-um-codice').val($um.attr('data-codice') || '');
        return fattore;
    }

    function populateUm($row, selectedId) {
        var opt = $row.find('.riga-articolo option:selected');
        var list = parseUm(opt);
        var $sel = $row.find('.riga-um').empty();
        var found = false;
        $.each(list, function (i, u) {
            $sel.append($('<option>').attr('value', u.id)
                .attr('data-codice', u.codice)
                .attr('data-fattore', u.fattore)
                .attr('data-predefinita', u.predefinita)
                .text(u.etichetta));
            if (selectedId !== null && selectedId !== undefined && String(selectedId) === String(u.id)) {
                found = true;
            }
        });
        if (found) {
            $sel.val(String(selectedId));
        } else {
            var $def = $sel.find('option[data-predefinita="1"]');
            if ($def.length) { $sel.val($def.first().val()); }
            else if ($sel.find('option').length) { $sel.val($sel.find('option').first().val()); }
        }
        return aggiornaFattore($row);
    }

    function applicaArticoloARiga($row) {
        var opt = $row.find('.riga-articolo option:selected');
        if (opt.val()) {
            $row.find('.riga-codice').val(opt.data('codice') || '');
            $row.find('.riga-desc').val(opt.data('descrizione') || '');
            $row.find('.riga-taglia').val(opt.attr('data-taglia') || '');
            $row.find('.riga-colore').val(opt.attr('data-colore') || '');
            $row.find('.riga-tessuto').val(opt.attr('data-tessuto') || '');
            var base = parseFloat(opt.data('prezzo')) || 0;
            $row.find('.riga-prezzo-base').val(base);
            var fattore = populateUm($row, null);
            $row.find('.riga-prezzo').val(base * fattore);
            var ivaSogg = aliquotaSoggetto();
            $row.find('.riga-iva').val(ivaSogg !== null ? ivaSogg : ivaArticolo(opt));
            if (!$row.find('.riga-qta').val()) {
                $row.find('.riga-qta').val(1);
            }
        } else {
            $row.find('.riga-um').empty();
            $row.find('.riga-fattore').val(1);
            $row.find('.riga-um-codice').val('');
        }
        ricalcolaRiga($row);
    }

    $(document).on('change', '.riga-articolo', function () {
        applicaArticoloARiga($(this).closest('.riga-row'));
    });

    $(document).on('change', '.riga-um', function () {
        var $row = $(this).closest('.riga-row');
        var fattore = aggiornaFattore($row);
        var base = parseFloat($row.find('.riga-prezzo-base').val()) || 0;
        $row.find('.riga-prezzo').val(base * fattore);
        ricalcolaRiga($row);
    });

    function initRigheUm() {
        $('#righe-body .riga-row').each(function () {
            var $row = $(this);
            var opt = $row.find('.riga-articolo option:selected');
            if (!opt.val()) { return; }
            var storedUm = $row.attr('data-um');
            var fattore = populateUm($row, storedUm || null);
            var prezzo = parseFloat($row.find('.riga-prezzo').val()) || 0;
            var base = storedUm ? (fattore > 0 ? prezzo / fattore : prezzo) : prezzo;
            $row.find('.riga-prezzo-base').val(base);
        });
    }

    $('#btn-add-riga').on('click', function () {
        aggiungiRiga();
        filtraRighe();
    });

    $(document).on('click', '.riga-duplica', function () {
        var $clone = $(this).closest('.riga-row').clone();
        $clone.find('.riga-id-rap').val('');
        $clone.find('.riga-rap-dettaglio').attr('data-id-rap', '').hide();
        $('#righe-body').append($clone);
        aggiornaIndici();
        filtraRighe();
        ricalcolaTotale();
    });

    $(document).on('click', '.riga-remove', function () {
        $(this).closest('.riga-row').remove();
        aggiornaIndici();
        filtraRighe();
        ricalcolaTotale();
    });

    function proponiNumero() {
        var idTipo = $('#mgdocumento-id_tipo').val();
        var anno = $('#mgdocumento-anno').val();
        var data = $('#mgdocumento-data').val();
        if (!idTipo) { return; }
        $.getJSON(proponiUrl, { id_tipo: idTipo, anno: anno, data: data }, function (res) {
            if (res && res.success && res.numero) {
                $('#mgdocumento-numero').val(res.numero);
            }
        });
    }

    $('#btn-proponi').on('click', proponiNumero);

    // --- DESTINAZIONE / ANAGRAFICHE / SCADENZE ---
    var anaUrl = '<?= Url::to(['mgdocumento/anagrafiche']) ?>';
    var scadUrl = '<?= Url::to(['mgdocumento/anteprima-scadenze']) ?>';

    function rebuildAnagrafiche(list) {
        var $sel = $('#mgdocumento-id_anagrafica');
        var current = $sel.val();
        $sel.empty().append($('<option>').attr('value', '').text('Seleziona cliente/fornitore...'));
        var found = false;
        $.each(list, function (i, a) {
            $sel.append($('<option>')
                .attr('value', a.id)
                .attr('data-metodo', a.id_metodo_pagamento || '')
                .attr('data-iva', (a.iva_perc === null || a.iva_perc === undefined) ? '' : a.iva_perc)
                .text(a.ragione_sociale));
            if (String(current) === String(a.id)) { found = true; }
        });
        $sel.val(found ? current : '');
        applicaMetodoAnagrafica();
    }

    function loadAnagrafiche() {
        var idTipo = $('#mgdocumento-id_tipo').val();
        if (!idTipo) { return; }
        $.getJSON(anaUrl, { id_tipo: idTipo }, function (res) {
            if (res && res.success) { rebuildAnagrafiche(res.anagrafiche); }
        });
    }

    function applicaMetodoAnagrafica() {
        var metodo = $('#mgdocumento-id_anagrafica option:selected').attr('data-metodo');
        if (metodo) {
            $('#mgdocumento-id_metodo_pagamento').val(metodo);
        }
        applicaAliquoteRighe();
        aggiornaScadenze();
    }

    function tipoCreaScadenze() {
        var v = $('#mgdocumento-id_tipo option:selected').attr('data-crea-scadenze');
        return String(v) === '1';
    }

    function aggiornaInfoScadenze() {
        var crea = tipoCreaScadenze();
        var idMetodo = $('#mgdocumento-id_metodo_pagamento').val();
        var msg;
        if (!crea) {
            msg = 'Il tipo documento selezionato non prevede la creazione automatica delle scadenze.';
        } else if (!idMetodo) {
            msg = 'Seleziona un metodo di pagamento per generare le scadenze.';
        } else {
            msg = 'Le scadenze verranno create automaticamente alla data del documento in base al metodo di pagamento.';
        }
        $('#scadenze-info-text').text(msg);
    }

    var sopprimiScadenze = false;
    function aggiornaScadenze() {
        if (sopprimiScadenze) { return; }
        aggiornaInfoScadenze();
        var crea = tipoCreaScadenze();
        var idMetodo = $('#mgdocumento-id_metodo_pagamento').val();
        var $card = $('#card-scadenze');
        if (!crea || !idMetodo) {
            $card.hide();
            $('#scadenze-body').empty();
            return;
        }
        var data = $('#mgdocumento-data').val();
        var totale = parseFloat($('#totale-documento').text().replace(/\./g, '').replace(',', '.')) || 0;
        $.getJSON(scadUrl, { id_metodo: idMetodo, data: data, totale: totale }, function (res) {
            if (!res || !res.success) { return; }
            var $b = $('#scadenze-body').empty();
            $.each(res.scadenze, function (i, s) {
                $b.append('<tr><td>' + s.progressivo + '</td><td>' + s.data_label +
                    '</td><td class="text-right">' + formatTotale(s.percentuale) +
                    '</td><td class="text-right">' + formatTotale(s.importo) + '</td></tr>');
            });
            $card.toggle(res.scadenze.length > 0);
        });
    }

    $('#mgdocumento-id_tipo').on('change', function () {
        var val = $(this).val();
        $('#mgdocumento-id_tipo-hidden').val(val);
        if (!val) { return; }
        if (isNew) { proponiNumero(); }
        loadAnagrafiche();
        applicaAliquoteRighe();
        aggiornaColonneDinamiche();
        aggiornaPulsantiTipo();
        aggiornaScadenze();
        applicaMagazziniRigheVuote();
        $(this).prop('disabled', true);
    });

    // --- MATRICE TAGLIE ---
    var matriceUrl = '<?= Url::to(['mgdocumento/matrice-taglie']) ?>';

    function caricaMatrice() {
        var idModello = $('#mat-modello').val();
        $('#mat-container').empty();
        $('#mat-error').text('');
        $('#mat-info').text('');
        if (!idModello) { return; }
        $('#mat-loading').show();
        $.getJSON(matriceUrl, { id_modello: idModello }, function (res) {
            $('#mat-loading').hide();
            if (!res || !res.success) {
                $('#mat-error').text(res && res.error ? res.error : 'Errore di caricamento.');
                return;
            }
            renderMatrice(res);
        }).fail(function () {
            $('#mat-loading').hide();
            $('#mat-error').text('Errore di rete.');
        });
    }

    function escAttr(v) {
        return String(v === null || v === undefined ? '' : v)
            .replace(/&/g, '&amp;').replace(/"/g, '&quot;');
    }

    function renderMatrice(res) {
        if (!res.righe.length || !res.taglie.length) {
            $('#mat-container').html('<div class="text-muted">Nessuna variante trovata per questo modello. Genera prima gli articoli con il Wizard prodotti.</div>');
            return;
        }
        var html = '<table class="table table-sm table-bordered mb-0"><thead><tr>'
            + '<th style="min-width:180px">Tessuto / Colore</th>';
        $.each(res.taglie, function (i, t) {
            html += '<th class="text-center">' + $('<div>').text(t.label).html() + '</th>';
        });
        html += '</tr></thead><tbody>';
        $.each(res.righe, function (i, r) {
            var nome = [r.tessuto, r.colore].filter(function (x) { return x; }).join(' / ') || '—';
            html += '<tr><th class="font-weight-normal">' + $('<div>').text(nome).html() + '</th>';
            $.each(res.taglie, function (j, t) {
                var c = r.celle[t.key];
                if (c) {
                    html += '<td class="p-1"><input type="number" min="0" step="any"'
                        + ' class="form-control form-control-sm text-right mat-qta"'
                        + ' data-id-articolo="' + c.id_articolo + '"'
                        + ' data-codice="' + escAttr(c.codice) + '"'
                        + ' data-descrizione="' + escAttr(c.descrizione) + '"'
                        + ' data-prezzo="' + escAttr(c.prezzo) + '"'
                        + ' data-iva="' + escAttr(c.iva) + '"'
                        + ' data-um="' + escAttr(c.um) + '"'
                        + ' data-taglia="' + escAttr(c.taglia) + '"'
                        + ' data-colore="' + escAttr(c.colore) + '"'
                        + ' data-tessuto="' + escAttr(c.tessuto) + '"'
                        + ' value=""></td>';
                } else {
                    html += '<td class="bg-light text-center text-muted">-</td>';
                }
            });
            html += '</tr>';
        });
        html += '</tbody></table>';
        $('#mat-container').html(html);
        aggiornaInfoMatrice();
    }

    function aggiornaInfoMatrice() {
        var n = 0;
        $('#mat-container .mat-qta').each(function () {
            if ((parseFloat($(this).val()) || 0) > 0) { n++; }
        });
        $('#mat-info').text(n > 0 ? (n + ' varianti con quantità') : 'Inserisci le quantità nelle celle');
    }

    $('#mat-modello').on('change', caricaMatrice);
    $(document).on('input', '#mat-container .mat-qta', aggiornaInfoMatrice);

    $('#btn-matrice-taglie').on('click', function () {
        $('#mat-error, #mat-info').text('');
        $('#mat-container').empty();
        $('#modal-matrice').modal('show');
    });

    $('#btn-mat-genera').on('click', function () {
        var creati = 0;
        sopprimiScadenze = true;
        $('#mat-container .mat-qta').each(function () {
            var $inp = $(this);
            var qta = parseFloat($inp.val()) || 0;
            if (qta <= 0) { return; }

            var $row = aggiungiRiga(true);
            var $sel = $row.find('.riga-articolo');
            $sel.val(String($inp.data('id-articolo')));
            if (!$sel.val()) { $row.remove(); return; }

            // Popola unità di misura e valori di base dal listino.
            applicaArticoloARiga($row);

            // Sovrascrive con i dati certi della matrice (codice, descrizione, U.M., prezzo, IVA).
            $row.find('.riga-codice').val($inp.attr('data-codice') || '');
            $row.find('.riga-desc').val($inp.attr('data-descrizione') || '');
            $row.find('.riga-taglia').val($inp.attr('data-taglia') || '');
            $row.find('.riga-colore').val($inp.attr('data-colore') || '');
            $row.find('.riga-tessuto').val($inp.attr('data-tessuto') || '');
            if (!$row.find('.riga-um-codice').val()) {
                $row.find('.riga-um-codice').val($inp.attr('data-um') || '');
            }
            var prezzo = parseFloat($inp.attr('data-prezzo')) || 0;
            $row.find('.riga-prezzo-base').val(prezzo);
            $row.find('.riga-prezzo').val(prezzo);
            var iva = $inp.attr('data-iva');
            if (iva !== undefined && iva !== null && iva !== '') {
                $row.find('.riga-iva').val(iva);
            }
            $row.find('.riga-qta').val(qta);
            ricalcolaRiga($row);
            creati++;
        });
        sopprimiScadenze = false;

        if (creati > 0) {
            aggiornaIndici();
            filtraRighe();
            ricalcolaTotale();
            $('#modal-matrice').modal('hide');
        } else {
            $('#mat-error').text('Inserisci almeno una quantità.');
        }
    });

    $('#mgdocumento-data').on('change', function () {
        if (isNew) { proponiNumero(); }
        aggiornaScadenze();
    });
    $('#mgdocumento-id_anagrafica').on('change', applicaMetodoAnagrafica);
    $('#mgdocumento-id_metodo_pagamento').on('change', aggiornaScadenze);
    $('#mgdocumento-id_sottocommessa').on('change', aggiornaSottocommessaRighe);

    // --- NUOVO ARTICOLO IN LINEA ---
    var lastRiga = null;
    $(document).on('focus click', '.riga-articolo', function () {
        lastRiga = $(this).closest('.riga-row');
    });

    function aggiungiRiga(silent) {
        var tpl = $('#righe-table').closest('.mgdocumento-form').find('table[style="display:none;"] tbody').html();
        var idx = $('#righe-body .riga-row').length;
        $('#righe-body').append(tpl.replace(/__INDEX__/g, idx));
        var $row = $('#righe-body .riga-row').last();
        applicaSottocommessaRiga($row);
        applicaMagazziniRiga($row);
        if (!silent) { ricalcolaTotale(); }
        return $row;
    }

    $('#btn-nuovo-articolo').on('click', function () {
        $('#na-error').text('');
        $('#na-codice, #na-descrizione').val('');
        $('#na-um').val('');
        $('#na-um-codice, #na-um-descrizione').val('');
        $('#na-um-error').text('');
        $('#na-nuova-um-box').hide();
        $('#na-prezzo').val(0);
        $('#na-iva-vendita, #na-iva-acquisto').val('');
        $('#modal-nuovo-articolo').modal('show');
    });

    // Creazione inline unità di misura dalla modale articolo
    $('#btn-na-nuova-um').on('click', function () {
        $('#na-nuova-um-box').toggle();
        $('#na-um-error').text('');
    });

    $('#btn-na-crea-um').on('click', function () {
        var codice = $('#na-um-codice').val().trim();
        var descrizione = $('#na-um-descrizione').val().trim();
        if (!codice || !descrizione) {
            $('#na-um-error').text('Codice e Descrizione sono obbligatori.');
            return;
        }
        var btn = $(this).prop('disabled', true);
        $.post('index.php?r=mgunitamisura/create-ajax', {
            codice: codice,
            descrizione: descrizione,
            _csrf: (typeof yii !== 'undefined' ? yii.getCsrfToken() : '')
        }, function (res) {
            btn.prop('disabled', false);
            if (res && res.success) {
                var u = res.unita;
                $('#na-um').append($('<option>').attr('value', u.id).text(u.etichetta)).val(String(u.id));
                $('#na-um-codice, #na-um-descrizione').val('');
                $('#na-nuova-um-box').hide();
            } else {
                $('#na-um-error').text(res && res.errors ? JSON.stringify(res.errors) : (res.error || 'Errore di creazione.'));
            }
        }, 'json').fail(function () {
            btn.prop('disabled', false);
            $('#na-um-error').text('Errore di rete.');
        });
    });

    $('#btn-salva-articolo').on('click', function () {
        var codice = $('#na-codice').val().trim();
        var descrizione = $('#na-descrizione').val().trim();
        if (!codice || !descrizione) {
            $('#na-error').text('Codice e Descrizione sono obbligatori.');
            return;
        }
        var data = {
            codice: codice,
            descrizione: descrizione,
            id_unita_misura: $('#na-um').val(),
            prezzo: $('#na-prezzo').val(),
            id_iva_vendita: $('#na-iva-vendita').val(),
            id_iva_acquisto: $('#na-iva-acquisto').val(),
            _csrf: (typeof yii !== 'undefined' ? yii.getCsrfToken() : '')
        };
        var btn = $(this).prop('disabled', true);
        $.post('index.php?r=mgarticolo/create-ajax', data, function (res) {
            btn.prop('disabled', false);
            if (res && res.success) {
                var a = res.articolo;
                var opt = $('<option>')
                    .attr('value', a.id)
                    .attr('data-codice', a.codice)
                    .attr('data-prezzo', a.prezzo)
                    .attr('data-iva', a.iva_vendita_perc)
                    .attr('data-iva-vendita', a.iva_vendita_perc)
                    .attr('data-iva-acquisto', a.iva_acquisto_perc)
                    .attr('data-descrizione', a.descrizione)
                    .attr('data-um', JSON.stringify(a.unita || []))
                    .text(a.codice + ' - ' + a.descrizione);
                $('.riga-articolo').append(opt);

                var $row = (lastRiga && $.contains(document, lastRiga[0])) ? lastRiga : aggiungiRiga();
                $row.find('.riga-articolo').val(String(a.id)).trigger('change');
                $('#modal-nuovo-articolo').modal('hide');
            } else {
                $('#na-error').text(res && res.errors ? JSON.stringify(res.errors) : (res.error || 'Errore di creazione.'));
            }
        }, 'json').fail(function () {
            btn.prop('disabled', false);
            $('#na-error').text('Errore di rete.');
        });
    });

    // --- NUOVO CLIENTE/FORNITORE IN LINEA ---
    $('#btn-nuovo-fornitore').on('click', function () {
        $('#nf-error').text('');
        $('#nf-codice, #nf-ragione, #nf-piva, #nf-citta, #nf-telefono, #nf-email').val('');
        $('#nf-iva').val('');
        $('#nf-cliente, #nf-agente').prop('checked', false);
        $('#nf-fornitore').prop('checked', true);
        $('#modal-nuovo-fornitore').modal('show');
    });

    $('#btn-salva-fornitore').on('click', function () {
        var codice = $('#nf-codice').val().trim();
        var ragione = $('#nf-ragione').val().trim();
        if (!codice || !ragione) {
            $('#nf-error').text('Codice e Ragione sociale sono obbligatori.');
            return;
        }
        var data = {
            codice: codice,
            ragione_sociale: ragione,
            partita_iva: $('#nf-piva').val(),
            citta: $('#nf-citta').val(),
            is_cliente: $('#nf-cliente').is(':checked') ? 1 : 0,
            is_fornitore: $('#nf-fornitore').is(':checked') ? 1 : 0,
            is_agente: $('#nf-agente').is(':checked') ? 1 : 0,
            telefono: $('#nf-telefono').val(),
            email: $('#nf-email').val(),
            id_aliquota_iva: $('#nf-iva').val(),
            _csrf: (typeof yii !== 'undefined' ? yii.getCsrfToken() : '')
        };
        var btn = $(this).prop('disabled', true);
        $.post('index.php?r=mganagrafica/create-ajax', data, function (res) {
            btn.prop('disabled', false);
            if (res && res.success) {
                var a = res.anagrafica;
                var opt = $('<option>')
                    .attr('value', a.id)
                    .attr('data-metodo', '')
                    .attr('data-iva', (a.iva_perc === null || a.iva_perc === undefined) ? '' : a.iva_perc)
                    .text(a.ragione_sociale);
                $('#mgdocumento-id_anagrafica').append(opt).val(String(a.id));
                applicaAliquoteRighe();
                $('#modal-nuovo-fornitore').modal('hide');
            } else {
                $('#nf-error').text(res && res.errors ? JSON.stringify(res.errors) : (res.error || 'Errore di creazione.'));
            }
        }, 'json').fail(function () {
            btn.prop('disabled', false);
            $('#nf-error').text('Errore di rete.');
        });
    });

    // --- PRELIEVO RAPPORTINI ---
    var rapDisponibiliUrl = '<?= Url::to(['mgdocumento/rapportini-disponibili']) ?>';
    var idDocumento = <?= $idDocumento === null ? 'null' : (int) $idDocumento ?>;

    function aggiornaInfoRapportini() {
        var n = $('#rap-list-body .rap-check:checked').length;
        $('#rap-info').text(n > 0 ? (n + ' rapportini selezionati') : 'Seleziona i rapportini da prelevare');
    }

    function filtraRapportini() {
        var q = ($('#rap-search').val() || '').toLowerCase().trim();
        var vis = 0, tot = 0;
        $('#rap-list-body tr').each(function () {
            var $r = $(this);
            if (!$r.data('rap')) { return; }
            tot++;
            var ok = (q === '' || $r.text().toLowerCase().indexOf(q) !== -1);
            $r.toggle(ok);
            if (ok) { vis++; }
        });
        $('#rap-search-info').text(q === '' ? '' : (vis + ' di ' + tot));
    }

    function renderRapportini(list) {
        var $b = $('#rap-list-body').empty();
        if (!list.length) {
            $b.append('<tr><td colspan="8" class="text-muted">Nessun rapportino disponibile.</td></tr>');
            $('#rap-info').text('Nessun rapportino da prelevare');
            return;
        }
        $.each(list, function (i, rap) {
            var $tr = $('<tr>').data('rap', rap);
            $tr.append($('<td class="text-center">').append($('<input type="checkbox" class="rap-check">').val(rap.id)));
            $tr.append($('<td>').text(rap.numero));
            $tr.append($('<td>').text(rap.data));
            $tr.append($('<td>').text(rap.cliente || ''));
            $tr.append($('<td>').text(rap.commessa_desc || ''));
            $tr.append($('<td>').text(((rap.cd_art || '') + ' ' + (rap.des_art || '')).trim()));
            $tr.append($('<td class="text-right">').text(rap.qta));
            $tr.append($('<td>').text((rap.ora_in || '') + (rap.ora_out ? ' - ' + rap.ora_out : '')));
            $b.append($tr);
        });
        filtraRapportini();
        aggiornaInfoRapportini();
    }

    function caricaRapportini() {
        $('#rap-error').text('');
        $('#rap-list-body').html('<tr><td colspan="8" class="text-muted">Caricamento...</td></tr>');
        $.getJSON(rapDisponibiliUrl, { id_documento: idDocumento, q: $('#rap-search').val() || '' }, function (res) {
            if (!res || !res.success) {
                $('#rap-list-body').html('<tr><td colspan="8" class="text-danger">Errore di caricamento.</td></tr>');
                return;
            }
            renderRapportini(res.rapportini);
        }).fail(function () {
            $('#rap-list-body').html('<tr><td colspan="8" class="text-danger">Errore di rete.</td></tr>');
        });
    }

    function applicaRapportinoARiga($row, rap) {
        var $sel = $row.find('.riga-articolo');
        if (rap.id_articolo) {
            $sel.val(String(rap.id_articolo));
            if ($sel.val()) {
                applicaArticoloARiga($row);
            }
        }
        $row.find('.riga-codice').val(rap.cd_art || '');
        $row.find('.riga-desc').val(rap.des_art || '');
        $row.find('.riga-qta').val(rap.qta || 0);
        if (!rap.id_articolo || !$sel.val()) {
            $row.find('.riga-prezzo-base').val(rap.prezzo || 0);
            $row.find('.riga-prezzo').val(rap.prezzo || 0);
            $row.find('.riga-iva').val(rap.iva || 0);
            if (rap.um) { $row.find('.riga-um-codice').val(rap.um); }
        }
        $row.find('.riga-id-rap').val(rap.id);
        $row.find('.riga-rap-dettaglio').attr('data-id-rap', rap.id).show();
        ricalcolaRiga($row);
    }

    $('#btn-preleva-rapportini').on('click', function () {
        $('#rap-search').val('');
        $('#rap-check-all').prop('checked', false);
        $('#modal-preleva-rapportini').modal('show');
        caricaRapportini();
    });

    $('#rap-search').on('input', filtraRapportini);
    $('#rap-check-all').on('change', function () {
        var c = $(this).is(':checked');
        $('#rap-list-body .rap-check').filter(':visible').prop('checked', c);
        aggiornaInfoRapportini();
    });
    $(document).on('change', '.rap-check', aggiornaInfoRapportini);

    $('#btn-rap-genera').on('click', function () {
        var creati = 0;
        sopprimiScadenze = true;
        $('#rap-list-body .rap-check:checked').each(function () {
            var rap = $(this).closest('tr').data('rap');
            if (!rap) { return; }
            var $row = aggiungiRiga(true);
            applicaRapportinoARiga($row, rap);
            creati++;
        });
        sopprimiScadenze = false;
        if (creati > 0) {
            aggiornaIndici();
            filtraRighe();
            ricalcolaTotale();
            $('#modal-preleva-rapportini').modal('hide');
        } else {
            $('#rap-error').text('Seleziona almeno un rapportino.');
        }
    });

    $('#mgdocumento-id_tipo-hidden').val($('#mgdocumento-id_tipo').val() || '');
    aggiornaColonneDinamiche();
    aggiornaPulsantiTipo();
    initRigheUm();
    $('#mgdocumento-id_sottocommessa').data('precedente', sottocommessaTestata());
    filtraRighe();
    ricalcolaTotale();
})();
</script>
