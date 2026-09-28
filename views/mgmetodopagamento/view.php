<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgMetodoPagamento */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Metodi pagamento', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgmetodopagamento-view">

    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questo metodo di pagamento?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codice',
            'descrizione',
            [
                'attribute' => 'id_tipo_pagamento',
                'value' => function ($model) {
                    return $model->tipoPagamento->descrizione ?? '';
                },
            ],
            [
                'attribute' => 'partenza',
                'value' => function ($model) {
                    return $model->partenzaLabel;
                },
            ],
            'giorni_partenza',
            'n_rate',
            ['attribute' => 'attivo', 'format' => 'boolean'],
            'created_at',
        ],
    ]) ?>

    <div class="card mt-3">
        <div class="card-header">Rate</div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
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
