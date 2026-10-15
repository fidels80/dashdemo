<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

/* @var $model app\models\DashReportFonte */
/* @var $def array */
/* @var $connessioni array */

$this->title = $model->isNewRecord ? 'Nuova fonte dati' : 'Modifica fonte: ' . $model->nome;
$this->params['breadcrumbs'][] = ['label' => 'Report Designer', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Fonti dati', 'url' => ['fonti']];
$this->params['breadcrumbs'][] = $this->title;

$renderSelect = function ($name, $options, $selected) {
    $html = '<select class="form-control form-control-sm" name="' . $name . '">';
    foreach ($options as $v => $l) {
        $sel = ((string) $selected === (string) $v) ? ' selected' : '';
        $html .= '<option value="' . Html::encode($v) . '"' . $sel . '>' . Html::encode($l) . '</option>';
    }
    return $html . '</select>';
};

$baseDb = $def['base']['db'] ?? ($model->connessione ?: 'db');
$baseSchema = $def['base']['schema'] ?? 'dbo';
$baseTabella = $def['base']['tabella'] ?? ($model->base_tabella ?? '');
$baseAlias = $def['base']['alias'] ?? 'b';
$isSql = ($model->tipo === 'sql');
?>
<div class="reportdesigner-fonte-form">

    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $msgs): ?>
        <?php foreach ((array) $msgs as $msg): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : Html::encode($type) ?>"><?= $msg ?></div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <?php $form = ActiveForm::begin(['id' => 'fonte-form']); ?>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-4"><?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-3"><?= $form->field($model, 'tipo')->dropDownList(
                    ['strutturata' => 'Tabella + join', 'sql' => 'SQL tecnico'],
                    ['id' => 'fonte-tipo']
                ) ?></div>
                <div class="col-md-2"><?= $form->field($model, 'connessione')->dropDownList($connessioni, ['id' => 'fonte-connessione']) ?></div>
                <div class="col-md-1 pt-4"><?= $form->field($model, 'attivo')->checkbox() ?></div>
            </div>
            <div class="row">
                <div class="col-md-8"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
            </div>
        </div>
    </div>

    <div id="sezione-strutturata" class="<?= $isSql ? 'd-none' : '' ?>">
        <div class="card mb-3">
            <div class="card-header"><strong>Tabella base</strong></div>
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label>Database</label>
                        <?= $renderSelect('base_db', $connessioni, $baseDb) ?>
                    </div>
                    <div class="col-md-2">
                        <label>Schema</label>
                        <input class="form-control form-control-sm" name="base_schema" id="base-schema"
                               value="<?= Html::encode($baseSchema) ?>">
                    </div>
                    <div class="col-md-5">
                        <label>Tabella</label>
                        <select class="form-control form-control-sm" name="base_tabella" id="base-tabella">
                            <?php if ($baseTabella !== ''): ?>
                                <option value="<?= Html::encode($baseTabella) ?>" selected>
                                    <?= Html::encode($baseSchema . '.' . $baseTabella) ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Alias</label>
                        <input class="form-control form-control-sm" name="base_alias" value="<?= Html::encode($baseAlias) ?>">
                    </div>
                </div>
                <div class="small text-muted mt-2" id="base-tabelle-info"></div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Join</strong>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-join"><i class="fas fa-plus"></i> Aggiungi join</button>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2">
                    Una condizione per riga nel formato <code>alias.campo = altro_alias.campo</code>.
                    Per join tra database diversi (stessa istanza SQL Server) scegli il database del join.
                </p>
                <table class="table table-sm" id="tbl-join">
                    <thead><tr><th>Tipo</th><th>Database</th><th>Schema</th><th>Tabella</th><th>Alias</th><th>Condizioni (on)</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach (($def['join'] ?? []) as $j): ?>
                        <?php
                        $on = [];
                        foreach (($j['on'] ?? []) as $c) {
                            $on[] = ($c['left'] ?? '') . ' = ' . ($c['right'] ?? '');
                        }
                        $jschema = $j['schema'] ?? 'dbo';
                        $jtab = $j['tabella'] ?? '';
                        ?>
                        <tr>
                            <td><?= $renderSelect('join_tipo[]', ['left' => 'LEFT', 'inner' => 'INNER', 'right' => 'RIGHT'], $j['tipo'] ?? 'left') ?></td>
                            <td class="join-db-cell"><?= $renderSelect('join_db[]', $connessioni, $j['db'] ?? $baseDb) ?></td>
                            <td><input class="form-control form-control-sm join-schema" name="join_schema[]" value="<?= Html::encode($jschema) ?>"></td>
                            <td>
                                <select class="form-control form-control-sm join-tabella" name="join_tabella[]">
                                    <?php if ($jtab !== ''): ?>
                                        <option value="<?= Html::encode($jtab) ?>" data-schema="<?= Html::encode($jschema) ?>" selected>
                                            <?= Html::encode($jschema . '.' . $jtab) ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <td><input class="form-control form-control-sm" name="join_alias[]" value="<?= Html::encode($j['alias'] ?? '') ?>"></td>
                            <td><textarea class="form-control form-control-sm" name="join_on[]" rows="2"><?= Html::encode(implode("\n", $on)) ?></textarea></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger rm-join"><i class="fas fa-times"></i></button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="sezione-sql" class="card mb-3 <?= $isSql ? '' : 'd-none' ?>">
        <div class="card-header"><strong>Query SQL</strong></div>
        <div class="card-body">
            <p class="text-muted small">Query tecnica (riservata). Le colonne vanno riferite come <code>t.colonna</code> nel report.</p>
            <textarea name="fonte_sql" class="form-control" rows="10" spellcheck="false"><?= Html::encode($isSql ? $model->definizione : '') ?></textarea>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['fonti'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$tabelleUrl = Url::to(['reportdesigner/tabelle']);
$joinTipo = $renderSelect('join_tipo[]', ['left' => 'LEFT', 'inner' => 'INNER', 'right' => 'RIGHT'], 'left');
$joinDb = $renderSelect('join_db[]', $connessioni, $baseDb);
$joinTemplate = '<tr>'
    . '<td>' . $joinTipo . '</td>'
    . '<td class="join-db-cell">' . $joinDb . '</td>'
    . '<td><input class="form-control form-control-sm join-schema" name="join_schema[]" value="dbo"></td>'
    . '<td><select class="form-control form-control-sm join-tabella" name="join_tabella[]"></select></td>'
    . '<td><input class="form-control form-control-sm" name="join_alias[]"></td>'
    . '<td><textarea class="form-control form-control-sm" name="join_on[]" rows="2"></textarea></td>'
    . '<td><button type="button" class="btn btn-sm btn-outline-danger rm-join"><i class="fas fa-times"></i></button></td>'
    . '</tr>';
$joinTemplateJson = json_encode($joinTemplate);

$js = <<<JS
var JOIN_TEMPLATE = $joinTemplateJson;

$('#fonte-tipo').on('change', function () {
    var sql = $(this).val() === 'sql';
    $('#sezione-sql').toggleClass('d-none', !sql);
    $('#sezione-strutturata').toggleClass('d-none', sql);
});
$(document).on('click', '.rm-join', function () {
    $(this).closest('tr').remove();
});

function fillTableSelect(\$sel, tables, current) {
    \$sel.empty();
    var found = false;
    tables.forEach(function (t) {
        var \$o = $('<option>').val(t.tabella).text(t.schema + '.' + t.tabella).attr('data-schema', t.schema);
        if (current && t.tabella === current) { \$o.prop('selected', true); found = true; }
        \$sel.append(\$o);
    });
    if (!found && current) {
        \$sel.append($('<option>').val(current).text(current).prop('selected', true));
    }
}

function loadTables(componente, \$sel, infoSel) {
    if (!componente) { return; }
    if (infoSel) { $(infoSel).text('Caricamento tabelle...'); }
    $.getJSON('$tabelleUrl', { componente: componente }, function (res) {
        var tables = (res && res.tabelle) ? res.tabelle : [];
        fillTableSelect(\$sel, tables, \$sel.val() || \$sel.data('selected'));
        if (infoSel) { $(infoSel).text(tables.length + ' tabelle/viste disponibili in ' + componente); }
    }).fail(function () {
        if (infoSel) { $(infoSel).text('Impossibile caricare le tabelle.'); }
    });
}

function reloadBase() {
    var \$t = $('#base-tabella');
    var cur = \$t.val() || \$t.data('selected') || '';
    \$t.data('selected', cur);
    loadTables($('select[name=base_db]').val(), \$t, '#base-tabelle-info');
}
function reloadJoinRow(\$row) {
    var \$t = \$row.find('.join-tabella');
    var cur = \$t.val() || \$t.data('selected') || '';
    \$t.data('selected', cur);
    loadTables(\$row.find('select[name="join_db[]"]').val(), \$t);
}

$('select[name=base_db]').on('change', reloadBase);
$('#base-tabella').on('change', function () {
    var sch = $(this).find('option:selected').data('schema');
    if (sch) { $('#base-schema').val(sch); }
});
$(document).on('change', 'select[name="join_db[]"]', function () {
    reloadJoinRow($(this).closest('tr'));
});
$(document).on('change', '.join-tabella', function () {
    var sch = $(this).find('option:selected').data('schema');
    if (sch) { $(this).closest('tr').find('.join-schema').val(sch); }
});
$('#add-join').on('click', function () {
    var \$row = $(JOIN_TEMPLATE);
    $('#tbl-join tbody').append(\$row);
    reloadJoinRow(\$row);
});

// Caricamento iniziale: tabella base e join esistenti.
reloadBase();
$('#tbl-join tbody tr').each(function () { reloadJoinRow($(this)); });
JS;
$this->registerJs($js);
?>
