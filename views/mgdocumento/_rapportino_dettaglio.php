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
$campo = function ($label, $valore, $col = 'col-md-4') {
    echo '<div class="' . $col . ' mb-3">'
        . '<label class="text-muted small mb-0 d-block">' . Html::encode($label) . '</label>'
        . '<div class="font-weight-bold">' . ($valore === '' || $valore === null ? '<span class="text-muted">—</span>' : Html::encode($valore)) . '</div>'
        . '</div>';
};
?>
<div class="rap-dettaglio">
    <div class="d-flex justify-content-between align-items-start mb-3 pb-2 border-bottom">
        <div>
            <h4 class="mb-0">Rapportino n. <?= (int) $model->numero ?></h4>
            <div class="text-muted small"><i class="fas fa-calendar-alt"></i> <?= Html::encode($data) ?></div>
        </div>
        <div class="text-right">
            <?= Html::a('<i class="fas fa-print"></i> Stampa', ['rapportino-stampa', 'id' => $model->id],
                ['class' => 'btn btn-sm btn-outline-dark', 'target' => '_blank']) ?>
        </div>
    </div>

    <div class="row">
        <?php
        $campo('Cliente', $cliente ? $cliente->codice . ' - ' . $cliente->ragione_sociale : $model->cd_cli, 'col-md-6');
        $campo('Sottocommessa', $sottocommessa ? $sottocommessa->codice . ' - ' . $sottocommessa->descrizione : $model->commessa, 'col-md-6');
        ?>
    </div>
    <div class="row">
        <?php
        $campo('Codice articolo', $model->cd_art, 'col-md-4');
        $campo('Descrizione articolo', $model->des_art, 'col-md-5');
        $campo('Quantità', $model->qta, 'col-md-3');
        ?>
    </div>
    <div class="row">
        <?php
        $campo('Ora inizio', $ora($model->ora_in), 'col-md-3');
        $campo('Ora fine', $ora($model->ora_out), 'col-md-3');
        $campo('Pausa inizio', $ora($model->pausa_in), 'col-md-3');
        $campo('Pausa fine', $ora($model->pausa_out), 'col-md-3');
        ?>
    </div>
    <div class="row">
        <?php $campo('Note', $model->note, 'col-md-12'); ?>
    </div>
</div>
