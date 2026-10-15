<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $result array */
/* @var $tableId string */

$format = function ($tipo, $val, $formato) {
    if ($val === null || $val === '') {
        return '';
    }
    switch ($tipo) {
        case 'data':
            $ts = strtotime((string) $val);
            return $ts ? date($formato ?: 'd/m/Y', $ts) : Html::encode($val);
        case 'numero':
        case 'intero':
            return number_format((float) $val, $formato !== null ? (int) $formato : 2, ',', '.');
        case 'valuta':
            return number_format((float) $val, $formato !== null ? (int) $formato : 2, ',', '.') . ' &euro;';
        default:
            return Html::encode($val);
    }
};

$gruppi = isset($result['gruppi']) ? $result['gruppi'] : null;
$colonne = $result['colonne'] ?? [];
$rag = $result['raggruppamenti'] ?? [];
$misure = $result['misure'] ?? [];
$calcolati = $result['calcolati'] ?? [];
$totali = $result['totali'] ?? [];

if ($gruppi !== null):
?>
<div class="mb-2">
    <button type="button" class="btn btn-sm btn-outline-primary" data-grp="expand-all"><i class="fas fa-plus-square"></i> Espandi tutti</button>
    <button type="button" class="btn btn-sm btn-outline-secondary" data-grp="collapse-all"><i class="fas fa-minus-square"></i> Comprimi tutti</button>
</div>
<table id="<?= Html::encode($tableId) ?>" class="table table-sm table-bordered table-hover table-striped">
    <thead>
        <tr>
            <?php foreach ($rag as $r): ?><th class="grp-col"><?= Html::encode($r['label']) ?></th><?php endforeach; ?>
            <?php foreach ($colonne as $c): ?><th><?= Html::encode($c['label']) ?></th><?php endforeach; ?>
            <?php foreach ($misure as $m): ?><th class="text-right"><?= Html::encode($m['label']) ?></th><?php endforeach; ?>
            <?php foreach ($calcolati as $c): ?><th class="text-right"><?= Html::encode($c['label']) ?></th><?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($gruppi as $gi => $g): ?>
            <tr class="grp-header collapsed" data-grp="<?= (int) $gi ?>">
                <?php foreach ($rag as $i => $r): ?>
                    <td class="grp-col">
                        <?php if ($i === 0): ?>
                            <button type="button" class="btn btn-sm btn-outline-secondary grp-toggle" data-grp-toggle><i class="fas fa-chevron-right"></i></button>
                        <?php endif; ?>
                        <strong><?= $format($r['tipo'] ?? 'testo', $g['chiavi'][$r['alias']] ?? null, $r['formato'] ?? null) ?></strong>
                    </td>
                <?php endforeach; ?>
                <?php foreach ($colonne as $c): ?><td></td><?php endforeach; ?>
                <?php foreach ($misure as $m): ?><td class="text-right font-weight-bold"><?= $format($m['tipo'] ?? 'numero', $g['subtotali'][$m['alias']] ?? null, $m['formato'] ?? null) ?></td><?php endforeach; ?>
                <?php foreach ($calcolati as $c): ?><td class="text-right font-weight-bold"><?= $format($c['tipo'] ?? 'numero', $g['calcolati'][$c['nome']] ?? null, $c['formato'] ?? null) ?></td><?php endforeach; ?>
            </tr>
            <?php foreach ($g['righe'] as $row): ?>
                <tr class="grp-detail" data-grp="<?= (int) $gi ?>" style="display:none">
                    <?php foreach ($rag as $r): ?><td class="grp-col"></td><?php endforeach; ?>
                    <?php foreach ($colonne as $c): ?><td><?= $format($c['tipo'] ?? 'testo', $row[$c['alias']] ?? null, $c['formato'] ?? null) ?></td><?php endforeach; ?>
                    <?php foreach ($misure as $m): ?><td></td><?php endforeach; ?>
                    <?php foreach ($calcolati as $c): ?><td></td><?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
    <?php if (!empty($totali)): ?>
        <tfoot class="font-weight-bold bg-light">
            <tr>
                <?php foreach ($rag as $i => $r): ?>
                    <td class="grp-col"><?= $i === 0 ? 'Totali' : '' ?></td>
                <?php endforeach; ?>
                <?php foreach ($colonne as $c): ?>
                    <td class="text-right"><?= array_key_exists($c['alias'], $totali) ? $format($c['tipo'] ?? 'numero', $totali[$c['alias']], $c['formato'] ?? null) : '' ?></td>
                <?php endforeach; ?>
                <?php foreach ($misure as $m): ?>
                    <td class="text-right"><?= array_key_exists($m['alias'], $totali) ? $format($m['tipo'] ?? 'numero', $totali[$m['alias']], $m['formato'] ?? null) : '' ?></td>
                <?php endforeach; ?>
                <?php foreach ($calcolati as $c): ?><td></td><?php endforeach; ?>
            </tr>
        </tfoot>
    <?php endif; ?>
</table>
<div class="text-muted small mt-2"><?= (int) $result['righe_totali'] ?> righe in <?= count($gruppi) ?> gruppi.</div>
<?php
$this->registerJs(<<<JS
function grpToggle(header, show) {
    var idx = header.data('grp');
    var details = $('tr.grp-detail[data-grp="' + idx + '"]');
    if (show === undefined) { show = header.hasClass('collapsed'); }
    if (show) {
        header.removeClass('collapsed');
        header.find('.grp-toggle i').removeClass('fa-chevron-right').addClass('fa-chevron-down');
        details.show();
    } else {
        header.addClass('collapsed');
        header.find('.grp-toggle i').removeClass('fa-chevron-down').addClass('fa-chevron-right');
        details.hide();
    }
}
$('[data-grp-toggle]').on('click', function () { grpToggle($(this).closest('tr')); });
$('[data-grp="expand-all"]').on('click', function () {
    $('tr.grp-header').each(function () { grpToggle($(this), true); });
});
$('[data-grp="collapse-all"]').on('click', function () {
    $('tr.grp-header').each(function () { grpToggle($(this), false); });
});
JS
);
?>
<?php else: ?>
    <table id="<?= Html::encode($tableId) ?>" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <?php foreach ($colonne as $c): ?>
                    <th><?= Html::encode($c['label']) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result['righe'] as $row): ?>
                <tr>
                    <?php foreach ($colonne as $c): ?>
                        <td><?= $format($c['tipo'] ?? 'testo', $row[$c['alias']] ?? null, $c['formato'] ?? null) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <?php if (!empty($totali)): ?>
            <tfoot class="font-weight-bold bg-light">
                <tr>
                    <?php foreach ($colonne as $i => $c): ?>
                        <?php if ($i === 0): ?>
                            <td>Totali</td>
                        <?php else: ?>
                            <td>
                                <?= array_key_exists($c['alias'], $totali)
                                    ? $format($c['tipo'] ?? 'numero', $totali[$c['alias']], $c['formato'] ?? null)
                                    : '' ?>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            </tfoot>
        <?php endif; ?>
    </table>
    <div class="text-muted small mt-2"><?= (int) $result['righe_totali'] ?> righe.</div>
    <?php DataTables::render($tableId, 0, 'asc'); ?>
<?php endif; ?>
