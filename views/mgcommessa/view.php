<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgCommessa */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Commesse', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$campo = function ($label, $valore, $col = 'col-md-3') {
    echo '<div class="' . $col . ' mb-3">'
        . '<label class="text-muted small mb-0 d-block">' . Html::encode($label) . '</label>'
        . '<div class="font-weight-bold">' . ($valore === '' || $valore === null ? '<span class="text-muted">—</span>' : Html::encode($valore)) . '</div>'
        . '</div>';
};
?>
<div class="mgcommessa-view">

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
        <div>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-list"></i> Sottocommesse', ['mgsottocommessa/index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Eliminare questa commessa? Le sottocommesse collegate verranno eliminate.', 'method' => 'post'],
            ]) ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Dati commessa</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('ID', $model->id, 'col-md-2');
                $campo('Codice', $model->codice, 'col-md-3');
                $campo('Attivo', $model->attivo ? 'Sì' : 'No', 'col-md-2');
                $campo('Data inizio', $model->data_inizio ? date('d/m/Y', strtotime($model->data_inizio)) : '', 'col-md-2');
                $campo('Data fine', $model->data_fine ? date('d/m/Y', strtotime($model->data_fine)) : '', 'col-md-2');
                ?>
            </div>
            <div class="row">
                <?php $campo('Descrizione', $model->descrizione, 'col-md-12'); ?>
            </div>
            <div class="row">
                <?php $campo('Anagrafica', $model->anagrafica ? $model->anagrafica->codice . ' - ' . $model->anagrafica->ragione_sociale : '', 'col-md-6'); ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Sottocommesse</div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
                <thead>
                <tr>
                    <th>Codice</th>
                    <th>Descrizione</th>
                    <th>Data inizio</th>
                    <th>Data fine</th>
                    <th>Anagrafica</th>
                    <th>Attivo</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->sottocommesse as $s): ?>
                    <tr>
                        <td><?= Html::encode($s->codice) ?></td>
                        <td><?= Html::encode($s->descrizione) ?></td>
                        <td><?= $s->data_inizio ? date('d/m/Y', strtotime($s->data_inizio)) : '' ?></td>
                        <td><?= $s->data_fine ? date('d/m/Y', strtotime($s->data_fine)) : '' ?></td>
                        <td><?= $s->anagrafica ? Html::encode($s->anagrafica->codice . ' - ' . $s->anagrafica->ragione_sociale) : '' ?></td>
                        <td><?= $s->attivo ? 'Sì' : 'No' ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($model->sottocommesse)): ?>
                    <tr><td colspan="6" class="text-muted">Nessuna sottocommessa collegata.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
