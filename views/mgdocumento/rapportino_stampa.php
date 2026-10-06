<?php

use yii\helpers\Html;
use app\models\MgAnagrafica;
use app\models\MgSottocommessa;

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */

$cliente = $model->cd_cli ? MgAnagrafica::findOne(['codice' => $model->cd_cli]) : null;
$sottocommessa = $model->commessa ? MgSottocommessa::findOne(['codice' => $model->commessa]) : null;
$data = $model->data ? date('d/m/Y', strtotime((string) $model->data)) : '';
$ora = function ($v) {
    return ($v === null || $v === '') ? '' : substr((string) $v, 0, 5);
};
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rapportino n. <?= (int) $model->numero ?></title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #212529; margin: 0; padding: 24px; background: #f4f6f9; }
        .sheet { background: #fff; max-width: 820px; margin: 0 auto; padding: 32px; box-shadow: 0 2px 12px rgba(0,0,0,.12); border-radius: 8px; }
        .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #2c3e50; padding-bottom: 12px; margin-bottom: 20px; }
        .head h1 { margin: 0; font-size: 1.4rem; color: #2c3e50; }
        .head .num { font-size: 1rem; color: #6c757d; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #dee2e6; padding: 8px 10px; text-align: left; font-size: .9rem; vertical-align: top; }
        th { background: #f1f3f5; width: 32%; color: #495057; font-weight: 600; }
        .note-title { font-weight: 700; color: #495057; margin-bottom: 6px; }
        .note { border: 1px solid #dee2e6; border-radius: 6px; padding: 10px 12px; min-height: 70px; font-size: .9rem; white-space: pre-wrap; }
        .actions { max-width: 820px; margin: 0 auto 16px; text-align: right; }
        .actions button { padding: 8px 16px; border: 0; border-radius: 6px; background: #2c3e50; color: #fff; cursor: pointer; font-size: .9rem; }
        .actions button.gray { background: #6c757d; margin-left: 8px; }
        @media print {
            body { background: #fff; padding: 0; }
            .sheet { box-shadow: none; border-radius: 0; max-width: none; padding: 0; }
            .actions { display: none !important; }
            th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
<div class="actions">
    <button onclick="window.print()">Stampa</button>
    <button class="gray" onclick="window.close()">Chiudi</button>
</div>
<div class="sheet">
    <div class="head">
        <div>
            <h1>Rapportino di lavoro</h1>
            <div class="num">n. <?= (int) $model->numero ?> del <?= Html::encode($data) ?></div>
        </div>
    </div>

    <table>
        <tr>
            <th>Cliente</th>
            <td><?= Html::encode($cliente ? $cliente->codice . ' - ' . $cliente->ragione_sociale : $model->cd_cli) ?></td>
        </tr>
        <tr>
            <th>Sottocommessa</th>
            <td><?= Html::encode($sottocommessa ? $sottocommessa->codice . ' - ' . $sottocommessa->descrizione : $model->commessa) ?></td>
        </tr>
        <tr>
            <th>Codice articolo</th>
            <td><?= Html::encode($model->cd_art) ?></td>
        </tr>
        <tr>
            <th>Descrizione articolo</th>
            <td><?= Html::encode($model->des_art) ?></td>
        </tr>
        <tr>
            <th>Quantità</th>
            <td><?= Html::encode($model->qta) ?></td>
        </tr>
        <tr>
            <th>Ora inizio</th>
            <td><?= Html::encode($ora($model->ora_in)) ?></td>
        </tr>
        <tr>
            <th>Ora fine</th>
            <td><?= Html::encode($ora($model->ora_out)) ?></td>
        </tr>
        <tr>
            <th>Pausa inizio</th>
            <td><?= Html::encode($ora($model->pausa_in)) ?></td>
        </tr>
        <tr>
            <th>Pausa fine</th>
            <td><?= Html::encode($ora($model->pausa_out)) ?></td>
        </tr>
    </table>

    <div class="note-title">Note</div>
    <div class="note"><?= Html::encode((string) $model->note) ?></div>
</div>
<script>
    window.onload = function () {
        setTimeout(function () { window.print(); }, 400);
    };
</script>
</body>
</html>
