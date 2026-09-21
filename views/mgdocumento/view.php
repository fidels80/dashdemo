<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgDocumento */

$this->title = $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Documenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgdocumento-view">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Eliminare questo documento? Il numero tornerà disponibile.',
                    'method' => 'post',
                ],
            ]) ?>
        </div>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codice_tipo',
            'anno',
            'numero',
            [
                'attribute' => 'suffisso',
                'value' => function ($model) {
                    return $model->suffisso ? '/' . $model->suffisso : '';
                },
            ],
            [
                'attribute' => 'data',
                'value' => function ($model) {
                    return $model->data ? date('d/m/Y', strtotime($model->data)) : '';
                },
            ],
            [
                'attribute' => 'id_anagrafica',
                'value' => function ($model) {
                    return $model->anagrafica->ragione_sociale ?? '';
                },
            ],
            'descrizione',
            'stato',
            [
                'attribute' => 'totale',
                'value' => function ($model) {
                    return number_format((float) $model->totale, 2, ',', '.');
                },
            ],
            'note',
            'created_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <div class="card mt-3">
        <div class="card-header">Righe</div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Codice</th>
                    <th>Descrizione</th>
                    <th class="text-right">Q.tà</th>
                    <th class="text-right">Prezzo</th>
                    <th class="text-right">Sc. %</th>
                    <th class="text-right">IVA %</th>
                    <th class="text-right">Totale</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->righe as $i => $r): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= Html::encode($r->codice_articolo) ?></td>
                        <td><?= Html::encode($r->descrizione) ?></td>
                        <td class="text-right"><?= number_format((float) $r->qta, 2, ',', '.') ?></td>
                        <td class="text-right"><?= number_format((float) $r->prezzo, 4, ',', '.') ?></td>
                        <td class="text-right"><?= number_format((float) $r->sconto, 2, ',', '.') ?></td>
                        <td class="text-right"><?= number_format((float) $r->iva, 2, ',', '.') ?></td>
                        <td class="text-right"><?= number_format((float) $r->totale, 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="7" class="text-right">Totale documento</th>
                    <th class="text-right"><?= number_format((float) $model->totale, 2, ',', '.') ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
