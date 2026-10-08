<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgDocumento */

$this->title = $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Documenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$bloccato = $model->hasScadenzePagate();
$mostraVarianti = $model->tipo && $model->tipo->mostra_varianti;
$mostraMagPartenza = $model->tipo && $model->tipo->id_magazzino_partenza;
$mostraMagArrivo = $model->tipo && $model->tipo->id_magazzino_arrivo;
$colonneRighe = 10 + ($mostraVarianti ? 3 : 0) + ($mostraMagPartenza ? 1 : 0) + ($mostraMagArrivo ? 1 : 0);
$stati = ['bozza' => 'Bozza', 'confermato' => 'Confermato', 'chiuso' => 'Chiuso', 'annullato' => 'Annullato'];

$campo = function ($label, $valore, $col = 'col-md-3') {
    echo '<div class="' . $col . ' mb-3">'
        . '<label class="text-muted small mb-0 d-block">' . Html::encode($label) . '</label>'
        . '<div class="font-weight-bold">' . ($valore === '' || $valore === null ? '<span class="text-muted">—</span>' : Html::encode($valore)) . '</div>'
        . '</div>';
};
?>
<div class="mgdocumento-view">

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
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
        <div class="text-muted">
            <i class="fas fa-info-circle"></i>
            Creato da <strong><?= Html::encode($model->created_by) ?></strong>
            <?= $model->created_at ? 'il ' . date('d/m/Y H:i', strtotime($model->created_at)) : '' ?>
        </div>
    </div>

    <?php if ($bloccato): ?>
        <div class="alert alert-warning">
            <i class="fas fa-lock"></i>
            Il documento ha una o più scadenze pagate: non può essere modificato né eliminato.
            Per sbloccarlo annullare il pagamento delle scadenze.
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-header">Dati documento</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('Tipo documento', $model->codice_tipo, 'col-md-4');
                $campo('Data', $model->data ? date('d/m/Y', strtotime($model->data)) : '', 'col-md-2');
                $campo('Anno', $model->anno, 'col-md-2');
                $campo('Numero', $model->numero, 'col-md-2');
                $campo('Suffisso', $model->suffisso, 'col-md-2');
                ?>
            </div>
            <div class="row">
                <?php
                $campo($model->tipo && $model->tipo->destinazione === 'fornitore' ? 'Fornitore' : 'Cliente', $model->anagrafica->ragione_sociale ?? '', 'col-md-5');
                $campo('Stato', $stati[$model->stato] ?? $model->stato, 'col-md-3');
                $campo('Metodo di pagamento', $model->metodoPagamento->descrizione ?? '', 'col-md-4');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Sottocommessa', $model->sottocommessa ? $model->sottocommessa->etichetta : '', 'col-md-5');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Descrizione', $model->descrizione, 'col-md-12');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Note', $model->note, 'col-md-12');
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Righe documento</span>
            <span class="text-muted small"><?= count($model->righe) ?> righe</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                <tr>
                    <th style="width:9%">Codice</th>
                    <th>Descrizione</th>
                    <th style="width:11%">Sottocommessa</th>
                    <?php if ($mostraMagPartenza): ?>
                        <th style="width:11%">Mag. partenza</th>
                    <?php endif; ?>
                    <?php if ($mostraMagArrivo): ?>
                        <th style="width:11%">Mag. arrivo</th>
                    <?php endif; ?>
                    <?php if ($mostraVarianti): ?>
                        <th style="width:7%">Taglia</th>
                        <th style="width:7%">Colore</th>
                        <th style="width:7%">Tessuto</th>
                    <?php endif; ?>
                    <th style="width:10%">U.M.</th>
                    <th class="text-right" style="width:7%">Q.tà</th>
                    <th class="text-right" style="width:8%">Prezzo</th>
                    <th class="text-right" style="width:6%">Sc. %</th>
                    <th class="text-right" style="width:6%">IVA %</th>
                    <th class="text-right" style="width:8%">Totale</th>
                    <th style="width:8%"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->righe as $r): ?>
                    <tr>
                        <td><?= Html::encode($r->codice_articolo) ?></td>
                        <td><?= Html::encode($r->descrizione) ?></td>
                        <td><?= $r->sottocommessa ? Html::encode($r->sottocommessa->etichetta) : '' ?></td>
                        <?php if ($mostraMagPartenza): ?>
                            <td><?= Html::encode($r->magazzinoPartenzaLabel ?? '') ?></td>
                        <?php endif; ?>
                        <?php if ($mostraMagArrivo): ?>
                            <td><?= Html::encode($r->magazzinoArrivoLabel ?? '') ?></td>
                        <?php endif; ?>
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
                        <td class="text-center text-nowrap">
                            <?php if ($r->id_rapportino): ?>
                                <button type="button" class="btn btn-sm btn-outline-info riga-rap-dettaglio" data-id-rap="<?= Html::encode($r->id_rapportino) ?>" title="Dettaglio rapportino"><i class="fas fa-file-alt"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($model->righe)): ?>
                    <tr><td colspan="<?= $colonneRighe ?>" class="text-muted">Nessuna riga.</td></tr>
                <?php endif; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="<?= $colonneRighe - 2 ?>" class="text-right">Totale documento</th>
                    <th class="text-right"><?= number_format((float) $model->totale, 2, ',', '.') ?></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Scadenze</div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                <tr>
                    <th style="width:12%">Rata</th>
                    <th>Data scadenza</th>
                    <th class="text-right" style="width:20%">%</th>
                    <th class="text-right" style="width:25%">Importo</th>
                    <th style="width:15%">Stato</th>
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
                            <?php if (!$bloccato): ?>
                                <?= Html::a(
                                    $pagata ? '<i class="fas fa-undo"></i> Annulla pagamento' : '<i class="fas fa-check"></i> Segna pagata',
                                    ['toggle-scadenza', 'id' => $s->id],
                                    [
                                        'class' => $pagata ? 'btn btn-sm btn-outline-warning' : 'btn btn-sm btn-outline-success',
                                        'data' => ['method' => 'post'],
                                    ]
                                ) ?>
                            <?php endif; ?>
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

    <?= $this->render('_rapportino_modal') ?>
</div>
