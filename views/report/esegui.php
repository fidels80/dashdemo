<?php
use yii\helpers\Html;
use app\components\DataTables;

$this->title = $report->nome;

$expandDefault = function ($default) {
    switch ((string) $default) {
        case 'today':
            return date('Y-m-d');
        case 'first-day-month':
            return date('Y-m-01');
        case 'last-day-month':
            return date('Y-m-t');
        default:
            return $default;
    }
};

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

<div class="report-esegui">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="m-0 text-dark"><i class="fas fa-table text-secondary"></i> <?= Html::encode($report->nome) ?></h1>
        <a href="<?= \yii\helpers\Url::to(['report/catalogo']) ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Catalogo
        </a>
    </div>

    <?php if ($report->descrizione): ?>
        <p class="text-muted pb-2 border-bottom"><?= Html::encode($report->descrizione) ?></p>
    <?php endif; ?>

    <?php $parametri = $def['parametri'] ?? []; ?>
    <?php if (!empty($parametri)): ?>
        <form method="get" action="<?= \yii\helpers\Url::to(['report/esegui', 'codice' => $report->codice]) ?>" class="card card-body mb-3 bg-light">
            <div class="form-row align-items-end">
                <?php foreach ($parametri as $p): ?>
                    <?php
                    $nome = $p['nome'] ?? '';
                    if ($nome === '') {
                        continue;
                    }
                    $tipo = strtolower($p['tipo'] ?? 'testo');
                    $value = $params[$nome] ?? $expandDefault($p['default'] ?? null);
                    ?>
                    <div class="form-group col-md-3">
                        <label class="mb-1"><?= Html::encode($p['label'] ?? $nome) ?></label>
                        <?php if ($tipo === 'date'): ?>
                            <input type="date" class="form-control" name="<?= Html::encode($nome) ?>"
                                   value="<?= Html::encode($value) ?>">
                        <?php elseif ($tipo === 'select'): ?>
                            <select class="form-control" name="<?= Html::encode($nome) ?>">
                                <option value="">-</option>
                                <?php foreach (($p['opzioni'] ?? []) as $ok => $ol): ?>
                                    <?php $ov = is_int($ok) ? $ol : $ok; ?>
                                    <option value="<?= Html::encode($ov) ?>" <?= ((string) $value === (string) $ov) ? 'selected' : '' ?>>
                                        <?= Html::encode($ol) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" class="form-control" name="<?= Html::encode($nome) ?>"
                                   value="<?= Html::encode($value) ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="form-group col-md-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Applica</button>
                </div>
            </div>
        </form>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><strong>Errore:</strong> <?= Html::encode($error) ?></div>
    <?php elseif ($result && !empty($result['righe'])): ?>
        <div class="card">
            <div class="card-body">
                <table id="rep-tabella" class="table table-striped table-bordered">
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
                                        <td>
                                            <?= array_key_exists($c['alias'], $result['totali'])
                                                ? $format($c['tipo'] ?? 'numero', $result['totali'][$c['alias']], $c['formato'] ?? null)
                                                : '' ?>
                                        </td>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
                <div class="text-muted small mt-2"><?= (int) $result['righe_totali'] ?> righe.</div>
            </div>
        </div>
        <?php DataTables::render('rep-tabella', 0, 'asc'); ?>
    <?php elseif ($result): ?>
        <div class="alert alert-info">Nessun dato trovato con i filtri selezionati.</div>
    <?php endif; ?>
</div>
