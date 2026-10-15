<?php
use yii\helpers\Html;

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
    <?php elseif ($result && (!empty($result['righe']) || !empty($result['gruppi']))): ?>
        <div class="card">
            <div class="card-body">
                <?= $this->render('_tabella', ['result' => $result, 'tableId' => 'rep-tabella']) ?>
            </div>
        </div>
    <?php elseif ($result): ?>
        <div class="alert alert-info">Nessun dato trovato con i filtri selezionati.</div>
    <?php endif; ?>
</div>
