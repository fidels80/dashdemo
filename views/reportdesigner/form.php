<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

/* @var $model app\models\DashReport */
/* @var $fonti app\models\DashReportFonte[] */
/* @var $def array */
/* @var $colonne array */
/* @var $features array */

$this->title = $model->isNewRecord ? 'Nuovo report' : 'Modifica report: ' . $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Report Designer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$d = function ($section, $key, $default = '') use ($def) {
    return trim((string) ($def[$section][$key] ?? $default));
};

$tipiCol = ['testo' => 'Testo', 'numero' => 'Numero', 'valuta' => 'Valuta', 'data' => 'Data', 'intero' => 'Intero'];
$funzioni = ['SUM' => 'Somma', 'AVG' => 'Media', 'COUNT' => 'Conteggio', 'MIN' => 'Minimo', 'MAX' => 'Massimo'];
$operatori = ['=' => '=', '<>' => '<>', '>' => '>', '>=' => '>=', '<' => '<', '<=' => '<=',
    'like' => 'contiene', 'in' => 'in elenco', 'between' => 'tra', 'isnull' => 'è vuoto', 'notnull' => 'non è vuoto'];
$paramTipi = ['testo' => 'Testo', 'date' => 'Data', 'select' => 'Tendina'];

$renderSelect = function ($name, $options, $selected) {
    $html = '<select class="form-control form-control-sm" name="' . $name . '">';
    foreach ($options as $v => $l) {
        $sel = ((string) $selected === (string) $v) ? ' selected' : '';
        $html .= '<option value="' . Html::encode($v) . '"' . $sel . '>' . Html::encode($l) . '</option>';
    }
    return $html . '</select>';
};
?>

<div class="reportdesigner-form">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $msgs): ?>
        <?php foreach ((array) $msgs as $msg): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : Html::encode($type) ?>"><?= $msg ?></div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <?php $form = ActiveForm::begin(['id' => 'report-form']); ?>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-4"><?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-3"><?= $form->field($model, 'categoria')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-3"><?= $form->field($model, 'fonte_id')->dropDownList(
                    ArrayHelper::map($fonti, 'id', 'nome'),
                    ['prompt' => '- seleziona fonte -', 'id' => 'fonte_id']
                ) ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-2"><?= $form->field($model, 'tipo')->dropDownList(
                    ['tabella' => 'Tabella', 'raggruppato' => 'Raggruppato', 'pivot' => 'Pivot']
                ) ?></div>
                <div class="col-md-2"><?= $form->field($model, 'icona')->textInput([
                    'maxlength' => true, 'list' => 'icone-report', 'placeholder' => 'es. file-invoice',
                ])->hint('Icona Font Awesome') ?></div>
                <div class="col-md-2"><?= $form->field($model, 'ordine')->input('number') ?></div>
                <div class="col-md-2"><?= $form->field($model, 'attivo')->checkbox() ?></div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'licenza_feature')->textInput([
                        'maxlength' => true, 'list' => 'licenza-features',
                        'placeholder' => 'vuoto = sempre attivo',
                    ])->hint('Codice feature licenza: il report è attivo solo se abilitata.') ?>
                </div>
                <div class="col-md-4 pt-4">
                    <label>Modalità</label><br>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="modalita" id="mod-wizard" value="wizard" checked>
                        <label class="form-check-label" for="mod-wizard">Wizard</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" name="modalita" id="mod-json" value="json">
                        <label class="form-check-label" for="mod-json">JSON avanzato</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="wizard-sections">
        <ul class="nav nav-tabs" id="rep-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab-colonne">Colonne</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-parametri">Parametri</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-filtri">Filtri</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-rag">Raggruppamenti</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-agg">Aggregazioni</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-cal">Calcolati</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab-ord">Ordinamento</a></li>
        </ul>

        <div class="tab-content border border-top-0 p-3 bg-white">
            <!-- COLONNE -->
            <div class="tab-pane fade show active" id="tab-colonne">
                <p class="text-muted small">Colonne da mostrare. Nei report "Raggruppato" sono il dettaglio espandibile di ogni gruppo (vuoto = solo riepilogo).</p>
                <table class="table table-sm" id="tbl-colonne">
                    <thead><tr><th>Campo (alias.col)</th><th>Alias</th><th>Etichetta</th><th>Tipo</th><th>Formato</th><th>Totale</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach (($def['colonne'] ?? []) as $r): ?>
                        <tr>
                            <td><input class="form-control form-control-sm" name="col_campo[]" list="fonte-colonne" value="<?= Html::encode($r['campo'] ?? '') ?>"></td>
                            <td><input class="form-control form-control-sm" name="col_alias[]" value="<?= Html::encode($r['alias'] ?? '') ?>"></td>
                            <td><input class="form-control form-control-sm" name="col_label[]" value="<?= Html::encode($r['label'] ?? '') ?>"></td>
                            <td><?= $renderSelect('col_tipo[]', $tipiCol, $r['tipo'] ?? 'testo') ?></td>
                            <td><input class="form-control form-control-sm" name="col_formato[]" value="<?= Html::encode($r['formato'] ?? '') ?>"></td>
                            <td><?= $renderSelect('col_totale[]', ['1' => 'Sì', '0' => 'No'], (isset($r['totale']) && $r['totale'] === false) ? '0' : '1') ?></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add="colonne"><i class="fas fa-plus"></i> Aggiungi colonna</button>
            </div>

            <!-- PARAMETRI -->
            <div class="tab-pane fade" id="tab-parametri">
                <p class="text-muted small">Parametri richiesti all'utente (es. intervallo date).</p>
                <table class="table table-sm" id="tbl-parametri">
                    <thead><tr><th>Nome</th><th>Etichetta</th><th>Tipo</th><th>Default</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach (($def['parametri'] ?? []) as $r): ?>
                        <tr>
                            <td><input class="form-control form-control-sm" name="par_nome[]" value="<?= Html::encode($r['nome'] ?? '') ?>"></td>
                            <td><input class="form-control form-control-sm" name="par_label[]" value="<?= Html::encode($r['label'] ?? '') ?>"></td>
                            <td><?= $renderSelect('par_tipo[]', $paramTipi, $r['tipo'] ?? 'testo') ?></td>
                            <td><input class="form-control form-control-sm" name="par_default[]" value="<?= Html::encode($r['default'] ?? '') ?>" placeholder="today / first-day-month"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add="parametri"><i class="fas fa-plus"></i> Aggiungi parametro</button>
            </div>

            <!-- FILTRI -->
            <div class="tab-pane fade" id="tab-filtri">
                <p class="text-muted small">Filtri applicati con i valori dei parametri (param1/param2).</p>
                <table class="table table-sm" id="tbl-filtri">
                    <thead><tr><th>Campo</th><th>Operatore</th><th>Param 1</th><th>Param 2</th><th>Opzionale</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach (($def['filtri'] ?? []) as $r): ?>
                        <tr>
                            <td><input class="form-control form-control-sm" name="fil_campo[]" list="fonte-colonne" value="<?= Html::encode($r['campo'] ?? '') ?>"></td>
                            <td><?= $renderSelect('fil_operatore[]', $operatori, $r['operatore'] ?? '=') ?></td>
                            <td><input class="form-control form-control-sm" name="fil_param1[]" value="<?= Html::encode($r['param1'] ?? '') ?>"></td>
                            <td><input class="form-control form-control-sm" name="fil_param2[]" value="<?= Html::encode($r['param2'] ?? '') ?>"></td>
                            <td><?= $renderSelect('fil_opzionale[]', ['1' => 'Sì', '0' => 'No'], !empty($r['opzionale']) ? '1' : '0') ?></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add="filtri"><i class="fas fa-plus"></i> Aggiungi filtro</button>
            </div>

            <!-- RAGGRUPPAMENTI -->
            <div class="tab-pane fade" id="tab-rag">
                <p class="text-muted small">Campi di raggruppamento. Nei report "Raggruppato" con colonne di dettaglio ogni gruppo diventa espandibile (tasto nella prima colonna) con i subtotali calcolati dalle Aggregazioni.</p>
                <table class="table table-sm" id="tbl-rag">
                    <thead><tr><th>Campo</th><th>Alias</th><th>Etichetta</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach (($def['raggruppamenti'] ?? []) as $r): ?>
                        <tr>
                            <td><input class="form-control form-control-sm" name="rag_campo[]" list="fonte-colonne" value="<?= Html::encode($r['campo'] ?? '') ?>"></td>
                            <td><input class="form-control form-control-sm" name="rag_alias[]" value="<?= Html::encode($r['alias'] ?? '') ?>"></td>
                            <td><input class="form-control form-control-sm" name="rag_label[]" value="<?= Html::encode($r['label'] ?? '') ?>"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add="rag"><i class="fas fa-plus"></i> Aggiungi raggruppamento</button>
            </div>

            <!-- AGGREGAZIONI -->
            <div class="tab-pane fade" id="tab-agg">
                <p class="text-muted small">Misure. Per il conteggio righe usare Conteggio con campo vuoto e alias impostato. Nei report raggruppati con dettaglio diventano i totali/subtotali mostrati per gruppo.</p>
                <table class="table table-sm" id="tbl-agg">
                    <thead><tr><th>Campo</th><th>Funzione</th><th>Alias</th><th>Etichetta</th><th>Tipo</th><th>Formato</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach (($def['aggregazioni'] ?? []) as $r): ?>
                        <tr>
                            <td><input class="form-control form-control-sm" name="agg_campo[]" list="fonte-colonne" value="<?= Html::encode($r['campo'] ?? '') ?>"></td>
                            <td><?= $renderSelect('agg_funzione[]', $funzioni, $r['funzione'] ?? 'SUM') ?></td>
                            <td><input class="form-control form-control-sm" name="agg_alias[]" value="<?= Html::encode($r['alias'] ?? '') ?>"></td>
                            <td><input class="form-control form-control-sm" name="agg_label[]" value="<?= Html::encode($r['label'] ?? '') ?>"></td>
                            <td><?= $renderSelect('agg_tipo[]', $tipiCol, $r['tipo'] ?? 'numero') ?></td>
                            <td><input class="form-control form-control-sm" name="agg_formato[]" value="<?= Html::encode($r['formato'] ?? '') ?>"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add="agg"><i class="fas fa-plus"></i> Aggiungi misura</button>
            </div>

            <!-- CALCOLATI -->
            <div class="tab-pane fade" id="tab-cal">
                <p class="text-muted small">Colonne calcolate. Formula con alias delle altre colonne (es. <code>tot_ore * tariffa</code>).</p>
                <table class="table table-sm" id="tbl-cal">
                    <thead><tr><th>Nome</th><th>Etichetta</th><th>Formula</th><th>Tipo</th><th>Formato</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach (($def['calcolati'] ?? []) as $r): ?>
                        <tr>
                            <td><input class="form-control form-control-sm" name="cal_nome[]" value="<?= Html::encode($r['nome'] ?? '') ?>"></td>
                            <td><input class="form-control form-control-sm" name="cal_label[]" value="<?= Html::encode($r['label'] ?? '') ?>"></td>
                            <td><input class="form-control form-control-sm" name="cal_formula[]" value="<?= Html::encode($r['formula'] ?? '') ?>"></td>
                            <td><?= $renderSelect('cal_tipo[]', $tipiCol, $r['tipo'] ?? 'numero') ?></td>
                            <td><input class="form-control form-control-sm" name="cal_formato[]" value="<?= Html::encode($r['formato'] ?? '') ?>"></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add="cal"><i class="fas fa-plus"></i> Aggiungi calcolato</button>
            </div>

            <!-- ORDINAMENTO -->
            <div class="tab-pane fade" id="tab-ord">
                <p class="text-muted small">Ordinamento per alias/colonna di output.</p>
                <table class="table table-sm" id="tbl-ord">
                    <thead><tr><th>Campo (alias)</th><th>Direzione</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach (($def['ordinamento'] ?? []) as $r): ?>
                        <tr>
                            <td><input class="form-control form-control-sm" name="ord_campo[]" value="<?= Html::encode($r['campo'] ?? '') ?>"></td>
                            <td><?= $renderSelect('ord_dir[]', ['ASC' => 'Crescente', 'DESC' => 'Decrescente'], $r['dir'] ?? 'ASC') ?></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add="ord"><i class="fas fa-plus"></i> Aggiungi ordinamento</button>
            </div>
        </div>
    </div>

    <div id="json-section" class="card d-none">
        <div class="card-body">
            <label>Definizione JSON (modalità esperti)</label>
            <textarea name="def_json" id="def_json" class="form-control" rows="16" spellcheck="false"><?= Html::encode($model->isNewRecord ? '' : json_encode($def, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></textarea>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <button type="submit" formaction="<?= Url::to(['reportdesigner/anteprima']) ?>" formtarget="_blank" class="btn btn-info">
            <i class="fas fa-eye"></i> Anteprima
        </button>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <datalist id="fonte-colonne">
        <?php foreach ($colonne as $c): ?>
            <option value="<?= Html::encode($c['value']) ?>"><?= Html::encode($c['label']) ?></option>
        <?php endforeach; ?>
    </datalist>
    <datalist id="licenza-features">
        <?php foreach ($features as $f): ?>
            <option value="<?= Html::encode($f) ?>"></option>
        <?php endforeach; ?>
    </datalist>
    <datalist id="icone-report">
        <?php foreach (\app\components\Icone::fontAwesome() as $icona): ?>
            <option value="<?= Html::encode($icona) ?>"></option>
        <?php endforeach; ?>
    </datalist>
</div>

<?php
$templates = [
    'colonne' => '<tr>'
        . '<td><input class="form-control form-control-sm" name="col_campo[]" list="fonte-colonne"></td>'
        . '<td><input class="form-control form-control-sm" name="col_alias[]"></td>'
        . '<td><input class="form-control form-control-sm" name="col_label[]"></td>'
        . '<td>' . $renderSelect('col_tipo[]', $tipiCol, 'testo') . '</td>'
        . '<td><input class="form-control form-control-sm" name="col_formato[]"></td>'
        . '<td>' . $renderSelect('col_totale[]', ['1' => 'Sì', '0' => 'No'], '1') . '</td>'
        . '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>'
        . '</tr>',
    'parametri' => '<tr>'
        . '<td><input class="form-control form-control-sm" name="par_nome[]"></td>'
        . '<td><input class="form-control form-control-sm" name="par_label[]"></td>'
        . '<td>' . $renderSelect('par_tipo[]', $paramTipi, 'testo') . '</td>'
        . '<td><input class="form-control form-control-sm" name="par_default[]"></td>'
        . '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>'
        . '</tr>',
    'filtri' => '<tr>'
        . '<td><input class="form-control form-control-sm" name="fil_campo[]" list="fonte-colonne"></td>'
        . '<td>' . $renderSelect('fil_operatore[]', $operatori, '=') . '</td>'
        . '<td><input class="form-control form-control-sm" name="fil_param1[]"></td>'
        . '<td><input class="form-control form-control-sm" name="fil_param2[]"></td>'
        . '<td>' . $renderSelect('fil_opzionale[]', ['1' => 'Sì', '0' => 'No'], '0') . '</td>'
        . '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>'
        . '</tr>',
    'rag' => '<tr>'
        . '<td><input class="form-control form-control-sm" name="rag_campo[]" list="fonte-colonne"></td>'
        . '<td><input class="form-control form-control-sm" name="rag_alias[]"></td>'
        . '<td><input class="form-control form-control-sm" name="rag_label[]"></td>'
        . '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>'
        . '</tr>',
    'agg' => '<tr>'
        . '<td><input class="form-control form-control-sm" name="agg_campo[]" list="fonte-colonne"></td>'
        . '<td>' . $renderSelect('agg_funzione[]', $funzioni, 'SUM') . '</td>'
        . '<td><input class="form-control form-control-sm" name="agg_alias[]"></td>'
        . '<td><input class="form-control form-control-sm" name="agg_label[]"></td>'
        . '<td>' . $renderSelect('agg_tipo[]', $tipiCol, 'numero') . '</td>'
        . '<td><input class="form-control form-control-sm" name="agg_formato[]"></td>'
        . '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>'
        . '</tr>',
    'cal' => '<tr>'
        . '<td><input class="form-control form-control-sm" name="cal_nome[]"></td>'
        . '<td><input class="form-control form-control-sm" name="cal_label[]"></td>'
        . '<td><input class="form-control form-control-sm" name="cal_formula[]"></td>'
        . '<td>' . $renderSelect('cal_tipo[]', $tipiCol, 'numero') . '</td>'
        . '<td><input class="form-control form-control-sm" name="cal_formato[]"></td>'
        . '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>'
        . '</tr>',
    'ord' => '<tr>'
        . '<td><input class="form-control form-control-sm" name="ord_campo[]"></td>'
        . '<td>' . $renderSelect('ord_dir[]', ['ASC' => 'Crescente', 'DESC' => 'Decrescente'], 'ASC') . '</td>'
        . '<td><button type="button" class="btn btn-sm btn-outline-danger rm-row"><i class="fas fa-times"></i></button></td>'
        . '</tr>',
];
$targets = ['colonne' => '#tbl-colonne tbody', 'parametri' => '#tbl-parametri tbody', 'filtri' => '#tbl-filtri tbody',
    'rag' => '#tbl-rag tbody', 'agg' => '#tbl-agg tbody', 'cal' => '#tbl-cal tbody', 'ord' => '#tbl-ord tbody'];
?>

<?php
$templatesJson = json_encode($templates);
$targetsJson = json_encode($targets);
$colonneUrl = Url::to(['reportdesigner/colonne']);
$js = <<<JS
var REP_TEMPLATES = $templatesJson;
var REP_TARGETS = $targetsJson;

$(document).on('click', '[data-add]', function () {
    var key = $(this).data('add');
    $(REP_TARGETS[key]).append(REP_TEMPLATES[key]);
});
$(document).on('click', '.rm-row', function () {
    $(this).closest('tr').remove();
});

$('input[name=modalita]').on('change', function () {
    if ($('#mod-json').prop('checked')) {
        $('#wizard-sections').addClass('d-none');
        $('#json-section').removeClass('d-none');
    } else {
        $('#wizard-sections').removeClass('d-none');
        $('#json-section').addClass('d-none');
    }
});

function loadColonne(fonteId) {
    if (!fonteId) { return; }
    $.getJSON('$colonneUrl', { id: fonteId }, function (res) {
        if (!res || !res.colonne) { return; }
        var dl = document.getElementById('fonte-colonne');
        dl.innerHTML = '';
        res.colonne.forEach(function (c) {
            var o = document.createElement('option');
            o.value = c.value;
            o.textContent = c.label;
            dl.appendChild(o);
        });
    });
}
$('#fonte_id').on('change', function () { loadColonne($(this).val()); });
JS;
$this->registerJs($js);
?>
