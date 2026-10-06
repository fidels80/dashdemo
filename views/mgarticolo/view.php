<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgArticolo */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Articoli', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$campo = function ($label, $valore, $col = 'col-md-3') {
    echo '<div class="' . $col . ' mb-3">'
        . '<label class="text-muted small mb-0 d-block">' . Html::encode($label) . '</label>'
        . '<div class="font-weight-bold">' . ($valore === '' || $valore === null ? '<span class="text-muted">—</span>' : Html::encode($valore)) . '</div>'
        . '</div>';
};
?>
<div class="mgarticolo-view">

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
        <div>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Eliminare questo articolo?', 'method' => 'post'],
            ]) ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Dati articolo</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('ID', $model->id, 'col-md-2');
                $campo('Codice', $model->codice, 'col-md-3');
                $campo('U.M.', $model->um, 'col-md-2');
                $campo('Attivo', $model->attivo ? 'Sì' : 'No', 'col-md-2');
                ?>
            </div>
            <div class="row">
                <?php $campo('Descrizione', $model->descrizione, 'col-md-12'); ?>
            </div>
            <div class="row">
                <?php
                $campo('Prezzo', number_format((float) $model->prezzo, 4, ',', '.'), 'col-md-3');
                $campo('IVA vendita', $model->ivaVendita
                    ? $model->ivaVendita->descrizione . ' (' . number_format((float) $model->ivaVendita->percentuale, 2, ',', '.') . '%)'
                    : '', 'col-md-4');
                $campo('IVA acquisto', $model->ivaAcquisto
                    ? $model->ivaAcquisto->descrizione . ' (' . number_format((float) $model->ivaAcquisto->percentuale, 2, ',', '.') . '%)'
                    : '', 'col-md-4');
                ?>
            </div>
            <div class="row">
                <?php $campo('GUID', $model->guid, 'col-md-6'); ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Varianti</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('Marca', $model->marca ? $model->marca->etichetta : '', 'col-md-3');
                $campo('Modello', $model->modello ? $model->modello->etichetta : '', 'col-md-3');
                $campo('Tessuto', $model->tessuto ? $model->tessuto->etichetta : '', 'col-md-2');
                $campo('Taglia', $model->taglia ? $model->taglia->etichetta : '', 'col-md-2');
                $campo('Colore', $model->colore ? $model->colore->etichetta : '', 'col-md-2');
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Unità di misura</div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
                <thead>
                <tr>
                    <th>Unità</th>
                    <th class="text-right">Fattore</th>
                    <th class="text-center">Predefinita</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->unitaMisura as $u): ?>
                    <tr>
                        <td><?= Html::encode($u->unitaMisura ? $u->unitaMisura->etichetta : '') ?></td>
                        <td class="text-right"><?= number_format((float) $u->fattore, 4, ',', '.') ?></td>
                        <td class="text-center"><?= $u->predefinita ? 'Sì' : '' ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($model->unitaMisura)): ?>
                    <tr><td colspan="3" class="text-muted">Nessuna unità di misura associata.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
