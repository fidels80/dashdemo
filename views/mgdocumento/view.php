<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgDocumento */

$this->title = $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Documenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$bloccato = $model->hasScadenzePagate();
$mostraVarianti = $model->tipo && $model->tipo->mostra_varianti;
?>
<div class="mgdocumento-view">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <?php if ($bloccato): ?>
                <?= Html::a('<i class="fas fa-pen"></i> Modifica', '#', ['class' => 'btn btn-primary disabled', 'title' => 'Documento con scadenze pagate: non modificabile']) ?>
                <?= Html::a('<i class="fas fa-trash"></i> Elimina', '#', ['class' => 'btn btn-danger disabled', 'title' => 'Documento con scadenze pagate: non eliminabile']) ?>
            <?php else: ?>
                <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Eliminare questo documento? Il numero tornerà disponibile.',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($bloccato): ?>
        <div class="alert alert-warning">
            <i class="fas fa-lock"></i>
            Il documento ha una o più scadenze pagate: non può essere modificato né eliminato.
            Per sbloccarlo annullare il pagamento delle scadenze.
        </div>
    <?php endif; ?>

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
                'attribute' => 'id_metodo_pagamento',
                'value' => function ($model) {
                    return $model->metodoPagamento->descrizione ?? '';
                },
            ],
            [
                'label' => 'Crea scadenze (tipo)',
                'value' => function ($model) {
                    return ($model->tipo && $model->tipo->crea_scadenze) ? 'Sì' : 'No';
                },
            ],
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
                    <?php if ($mostraVarianti): ?>
                        <th>Taglia</th>
                        <th>Colore</th>
                        <th>Tessuto</th>
                    <?php endif; ?>
                    <th>U.M.</th>
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
                        <?php if ($mostraVarianti): ?>
                            <td><?= Html::encode($r->taglia) ?></td>
                            <td><?= Html::encode($r->colore) ?></td>
                            <td><?= Html::encode($r->tessuto) ?></td>
                        <?php endif; ?>
                        <td><?= Html::encode($r->um) ?></td>
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
                    <th colspan="<?= $mostraVarianti ? 11 : 8 ?>" class="text-right">Totale documento</th>
                    <th class="text-right"><?= number_format((float) $model->totale, 2, ',', '.') ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">Scadenze</div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                <tr>
                    <th>Rata</th>
                    <th>Data scadenza</th>
                    <th class="text-right">%</th>
                    <th class="text-right">Importo</th>
                    <th>Stato</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->scadenze as $s): ?>
                    <?php $pagata = ($s->stato !== 'aperta'); ?>
                    <tr>
                        <td><?= (int) $s->progressivo ?></td>
                        <td><?= $s->data_scadenza ? date('d/m/Y', strtotime($s->data_scadenza)) : '' ?></td>
                        <td class="text-right"><?= number_format((float) $s->percentuale, 2, ',', '.') ?></td>
                        <td class="text-right"><?= number_format((float) $s->importo, 2, ',', '.') ?></td>
                        <td>
                            <?php if ($pagata): ?>
                                <span class="badge badge-success">Pagata</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Aperta</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-nowrap">
                            <?= Html::a(
                                $pagata ? '<i class="fas fa-undo"></i> Annulla pagamento' : '<i class="fas fa-check"></i> Segna pagata',
                                ['toggle-scadenza', 'id' => $s->id],
                                [
                                    'class' => $pagata ? 'btn btn-sm btn-outline-warning' : 'btn btn-sm btn-outline-success',
                                    'data' => ['method' => 'post'],
                                ]
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($model->scadenze)): ?>
                    <tr><td colspan="6" class="text-muted">Nessuna scadenza generata.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
