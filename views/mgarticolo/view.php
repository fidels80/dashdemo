<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgArticolo */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Articoli', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgarticolo-view">

    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questo articolo?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'guid',
            'codice',
            'descrizione',
            'um',
            ['attribute' => 'prezzo', 'value' => number_format((float) $model->prezzo, 4, ',', '.')],
            [
                'attribute' => 'id_iva_vendita',
                'label' => 'IVA vendita',
                'value' => function ($model) {
                    return $model->ivaVendita
                        ? $model->ivaVendita->descrizione . ' (' . number_format((float) $model->ivaVendita->percentuale, 2, ',', '.') . '%)'
                        : '';
                },
            ],
            [
                'attribute' => 'id_iva_acquisto',
                'label' => 'IVA acquisto',
                'value' => function ($model) {
                    return $model->ivaAcquisto
                        ? $model->ivaAcquisto->descrizione . ' (' . number_format((float) $model->ivaAcquisto->percentuale, 2, ',', '.') . '%)'
                        : '';
                },
            ],
            ['attribute' => 'id_marca', 'label' => 'Marca', 'value' => function ($model) { return $model->marca ? $model->marca->etichetta : ''; }],
            ['attribute' => 'id_modello', 'label' => 'Modello', 'value' => function ($model) { return $model->modello ? $model->modello->etichetta : ''; }],
            ['attribute' => 'id_tessuto', 'label' => 'Tessuto', 'value' => function ($model) { return $model->tessuto ? $model->tessuto->etichetta : ''; }],
            ['attribute' => 'id_taglia', 'label' => 'Taglia', 'value' => function ($model) { return $model->taglia ? $model->taglia->etichetta : ''; }],
            ['attribute' => 'id_colore', 'label' => 'Colore', 'value' => function ($model) { return $model->colore ? $model->colore->etichetta : ''; }],
            ['attribute' => 'attivo', 'format' => 'boolean'],
        ],
    ]) ?>

    <div class="card mt-3">
        <div class="card-header">Unità di misura</div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
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
