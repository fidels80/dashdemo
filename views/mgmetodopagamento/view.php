<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgMetodoPagamento */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Metodi pagamento', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$campo = function ($label, $valore, $col = 'col-md-3') {
    echo '<div class="' . $col . ' mb-3">'
        . '<label class="text-muted small mb-0 d-block">' . Html::encode($label) . '</label>'
        . '<div class="font-weight-bold">' . ($valore === '' || $valore === null ? '<span class="text-muted">—</span>' : Html::encode($valore)) . '</div>'
        . '</div>';
};
?>
<div class="mgmetodopagamento-view">

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
        <div>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Eliminare questo metodo di pagamento?', 'method' => 'post'],
            ]) ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Dati metodo di pagamento</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('ID', $model->id, 'col-md-2');
                $campo('Codice', $model->codice, 'col-md-3');
                $campo('Attivo', $model->attivo ? 'Sì' : 'No', 'col-md-2');
                $campo('Tipo pagamento', $model->tipoPagamento->descrizione ?? '', 'col-md-3');
                ?>
            </div>
            <div class="row">
                <?php $campo('Descrizione', $model->descrizione, 'col-md-12'); ?>
            </div>
            <div class="row">
                <?php
                $campo('Partenza', $model->partenzaLabel, 'col-md-4');
                $campo('Giorni dalla partenza', $model->giorni_partenza, 'col-md-4');
                $campo('Numero rate', $model->n_rate, 'col-md-4');
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Rate</div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
                <thead>
                <tr>
                    <th>Rata</th>
                    <th class="text-right">Giorni dalla partenza</th>
                    <th class="text-right">% importo</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->rate as $r): ?>
                    <tr>
                        <td><?= (int) $r->progressivo ?></td>
                        <td class="text-right"><?= (int) $r->giorni ?></td>
                        <td class="text-right"><?= number_format((float) $r->percentuale, 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($model->rate)): ?>
                    <tr><td colspan="3" class="text-muted">Nessuna rata configurata.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
