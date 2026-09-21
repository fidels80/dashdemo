<?php

use yii\helpers\Html;
use yii\helpers\Url;

$f = $filters;
?>
<style>
    /* Classi colori basate sullo stato di Vtiger */
    .bg-status-delivered {
        background-color: #007bff !important;
        color: white;
    }

    .bg-status-completed {
        background-color: #28a745 !important;
        color: white;
    }

    .bg-status-in-progress {
        background-color: #ffc107 !important;
        color: black;
    }

    .bg-status-archived {
        background-color: #6c757d !important;
        color: white;
    }

    .bg-status-prospecting {
        background-color: #17a2b8 !important;
        color: white;
    }

    .bg-status-initiated {
        background-color: #6610f2 !important;
        color: white;
    }

    /* Stile generale tabella */
    .gantt-table {
        font-size: 11px;
    }

    .gantt-table th {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #333 !important;
        color: white;
        padding: 10px 5px !important;
        text-align: center;
    }

    .gantt-table td {
        vertical-align: middle;
        padding: 5px !important;
        border: 1px solid #dee2e6 !important;
        text-align: center;
    }

    .month-col {
        min-width: 45px;
        font-size: 10px;
        background: #444 !important;
        color: white;
    }

    .text-left {
        text-align: left !important;
        padding-left: 10px !important;
    }

    .sticky-col {
        position: sticky;
        left: 0;
        z-index: 5;
        background: #f8f9fa !important;
    }
</style>

<div class="gantt-container">
    <div class="card p-3 mb-3 shadow-sm border-0 bg-light">
        <form method="get" action="index.php">
            <input type="hidden" name="r" value="vtiger/ganttprogetti">

            <div class="row">
                <div class="col-md-2">
                    <label class="small font-weight-bold">Dal</label>
                    <input type="date" name="dayFrom" value="<?= Html::encode($f['dayFrom'] ?? '') ?>" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">Al</label>
                    <input type="date" name="dayTo" value="<?= Html::encode($f['dayTo'] ?? '') ?>" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">Azienda (Cliente)</label>
                    <input type="text" name="listac" value="<?= Html::encode($f['listac'] ?? '') ?>" class="form-control form-control-sm" placeholder="Cerca...">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">Assegnato A</label>
                    <input type="text" name="assegnato_a" value="<?= Html::encode($f['assegnato_a'] ?? '') ?>" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">Stato</label>
                    <input type="text" name="stato" value="<?= Html::encode($f['stato'] ?? '') ?>" class="form-control form-control-sm">
                </div>
                <div class="col-md-2" style="padding-top: 25px;">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary btn-sm">Filtra</button>
                        <button type="button" onclick="exportExcel()" class="btn btn-success btn-sm">
                            <i class="fa fa-file-excel-o"></i> Excel
                        </button>
                    </div>
                    <div class="text-center mt-1">
                        <a href="<?= Url::to(['vtiger/ganttprogetti']) ?>" class="small mr-2">Reset</a>
                        <a href="<?= Url::to(['vtiger/search']) ?>" class="small">Home</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive" style="max-height: 800px; overflow-x: auto;">
        <table class="table table-bordered gantt-table table-hover">
            <thead class="thead-dark">
                <tr>
                    <th style="min-width: 150px; position: sticky; left: 0; z-index: 11; background: #222 !important;">AZIENDA</th>
                    <th style="min-width: 180px;">PROGETTO</th>
                    <th>ASSEGNATO</th>
                    <th>STATO</th>
                    <th>MONTE ORE</th>
                    <th>RESIDUO</th>
                    <th>PROG. %</th>
                    <?php foreach ($period as $dt): ?>
                        <th class="month-col"><?= $dt->format('M y') ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($progetti)): ?>
                    <tr>
                        <td colspan="100%" class="p-5 text-center text-muted">Nessun progetto trovato.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($progetti as $p): ?>
                    <tr>
                        <td class="text-left font-weight-bold sticky-col"><?= Html::encode($p['azienda'] ?: 'N.D.') ?></td>
                        <td class="text-left"><?= Html::encode($p['nome_progetto']) ?></td>
                        <td><small><?= Html::encode($p['assegnato_a']) ?></small></td>

                        <?php
                        // Calcolo classe stato
                        $cleanStatus = strtolower(str_replace(' ', '-', trim($p['stato'])));
                        $statusClass = 'bg-status-' . $cleanStatus;
                        ?>
                        <td class="<?= $statusClass ?>"><small><?= Html::encode($p['stato']) ?></small></td>

                        <td class="text-nowrap"><?= number_format($p['monte_ore'] ?? 0, 2) ?></td>
                        <td class="text-nowrap <?= ($p['residuo'] ?? 0) < 0 ? 'text-danger font-weight-bold' : '' ?>">
                            <?= number_format($p['residuo'] ?? 0, 2) ?>
                        </td>
                        <td>
                            <div class="progress" style="height: 12px; min-width: 50px;">
                                <div class="progress-bar bg-info" style="width: <?= (int)$p['progresso'] ?>%"><?= (int)$p['progresso'] ?>%</div>
                            </div>
                        </td>

                        <?php
                        foreach ($period as $date):
                            $mY = $date->format('Y-m');
                            $start = !empty($p['data_inizio']) ? date('Y-m', strtotime($p['data_inizio'])) : '9999-99';
                            $end = !empty($p['data_fine_obiettivo']) ? date('Y-m', strtotime($p['data_fine_obiettivo'])) : $start;
                            $isActive = ($mY >= $start && $mY <= $end);
                        ?>
                            <td class="<?= $isActive ? $statusClass : '' ?>"></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function exportExcel() {
        var form = document.querySelector('form[action="index.php"]');
        var originalR = form.querySelector('input[name="r"]').value;
        form.querySelector('input[name="r"]').value = 'vtiger/exportgantt';
        form.submit();
        setTimeout(function() {
            form.querySelector('input[name="r"]').value = originalR;
        }, 500);
    }
</script>