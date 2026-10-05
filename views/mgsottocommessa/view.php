<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgSottocommessa */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Sottocommesse', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$campo = function ($label, $valore, $col = 'col-md-3') {
    echo '<div class="' . $col . ' mb-3">'
        . '<label class="text-muted small mb-0 d-block">' . Html::encode($label) . '</label>'
        . '<div class="font-weight-bold">' . ($valore === '' || $valore === null ? '<span class="text-muted">—</span>' : Html::encode($valore)) . '</div>'
        . '</div>';
};
?>
<div class="mgsottocommessa-view">

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
        <div>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Eliminare questa sottocommessa?', 'method' => 'post'],
            ]) ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Dati sottocommessa</div>
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
                <?php
                $campo('Commessa', $model->commessa ? $model->commessa->codice . ' - ' . $model->commessa->descrizione : '', 'col-md-6');
                $campo('Anagrafica', $model->anagrafica ? $model->anagrafica->codice . ' - ' . $model->anagrafica->ragione_sociale : '', 'col-md-6');
                ?>
            </div>
        </div>
    </div>
</div>
