<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $report app\models\DashReport */
/* @var $def array */
/* @var $result array|null */
/* @var $error string|null */

$this->title = 'Anteprima: ' . $report->nome;

$format = function ($tipo, $val, $formato) {
    if ($val === null || $val === '') {
        return '';
    }
    switch ($tipo) {
        case 'data':
            $ts = strtotime((string) $val);
            return $ts ? date($formato ?: 'd/m/Y', $ts) : $val;
        case 'numero':
        case 'intero':
            return number_format((float) $val, $formato !== null ? (int) $formato : 2, ',', '.');
        case 'valuta':
            return number_format((float) $val, $formato !== null ? (int) $formato : 2, ',', '.') . ' &euro;';
        default:
            return Html::encode($val);
    }
};
?>
<div class="reportdesigner-anteprima container-fluid py-3">
    <h4 class="mb-3"><i class="fas fa-eye text-info"></i> <?= Html::encode($this->title) ?></h4>

    <?php if ($error): ?>
        <div class="alert alert-danger"><strong>Errore:</strong> <?= Html::encode($error) ?></div>
    <?php elseif ($result && !empty($result['righe'])): ?>
        <table id="anteprima-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr>
                <?php foreach ($result['colonne'] as $c): ?>
                    <th><?= Html::encode($c['label']) ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($result['righe'] as $row): ?>
                <tr>
                    <?php foreach ($result['colonne'] as $c): ?>
                        <td><?= $format($c['tipo'] ?? 'testo', $row[$c['alias']] ?? null, $c['formato'] ?? null) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <?php if (!empty($result['totali'])): ?>
                <tfoot class="font-weight-bold bg-light">
                <tr>
                    <?php foreach ($result['colonne'] as $i => $c): ?>
                        <?php if ($i === 0): ?>
                            <td>Totali</td>
                        <?php else: ?>
                            <td><?= array_key_exists($c['alias'], $result['totali'])
                                ? $format($c['tipo'] ?? 'numero', $result['totali'][$c['alias']], $c['formato'] ?? null)
                                : '' ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
                </tfoot>
            <?php endif; ?>
        </table>
        <div class="text-muted small"><?= (int) $result['righe_totali'] ?> righe.</div>
    <?php elseif ($result): ?>
        <div class="alert alert-info">Nessun dato con la definizione corrente.</div>
    <?php endif; ?>
</div>
<?php if ($result && !empty($result['righe'])): ?>
    <?php DataTables::render('anteprima-table', 0, 'asc'); ?>
<?php endif; ?>
