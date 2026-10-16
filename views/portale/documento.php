<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgDocumento */

$this->title = $model->etichetta;
?>
<div class="portale-documento">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><?= Html::encode($model->etichetta) ?></h4>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Indietro', ['documenti'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fas fa-info-circle"></i> Dettagli documento</div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Tipo</dt><dd class="col-sm-9"><?= Html::encode($model->tipo->descrizione ?? '') ?></dd>
                <dt class="col-sm-3">Azienda</dt><dd class="col-sm-9"><?= Html::encode($model->anagrafica->ragione_sociale ?? '—') ?></dd>
                <dt class="col-sm-3">Data</dt><dd class="col-sm-9"><?= $model->data ? Html::encode(date('d/m/Y', strtotime($model->data))) : '—' ?></dd>
                <dt class="col-sm-3">Descrizione</dt><dd class="col-sm-9"><?= Html::encode($model->descrizione) ?></dd>
                <dt class="col-sm-3">Totale</dt><dd class="col-sm-9"><?= number_format((float) $model->totale, 2, ',', '.') ?> €</dd>
            </dl>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fas fa-list"></i> Righe</div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
                <thead>
                <tr><th>Descrizione</th><th class="text-right">Q.tà</th><th class="text-right">Prezzo</th><th class="text-right">Totale</th></tr>
                </thead>
                <tbody>
                <?php foreach ($model->righe as $r): ?>
                    <tr>
                        <td><?= Html::encode($r->descrizione) ?></td>
                        <td class="text-right"><?= number_format((float) $r->qta, 2, ',', '.') ?></td>
                        <td class="text-right"><?= number_format((float) $r->prezzo, 2, ',', '.') ?></td>
                        <td class="text-right"><?= number_format((float) $r->totale, 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="fas fa-calendar-check"></i> Scadenze</div>
        <div class="card-body p-0">
            <?php if (empty($model->scadenze)): ?>
                <p class="text-muted p-3 mb-0">Nessuna scadenza.</p>
            <?php else: ?>
                <table class="table table-sm table-striped mb-0">
                    <thead><tr><th>Rata</th><th>Data</th><th class="text-right">Importo</th><th>Stato</th></tr></thead>
                    <tbody>
                    <?php foreach ($model->scadenze as $s): ?>
                        <tr>
                            <td><?= (int) $s->progressivo ?></td>
                            <td><?= Html::encode(date('d/m/Y', strtotime($s->data_scadenza))) ?></td>
                            <td class="text-right"><?= number_format((float) $s->importo, 2, ',', '.') ?> €</td>
                            <td><?= Html::encode($s->stato) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
