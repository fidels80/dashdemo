<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $stato array */
/* @var $modello app\models\MgAttributoArticolo */
/* @var $tessuti array */
/* @var $colori array */
/* @var $taglie array */
/* @var $combinazioni array */
/* @var $unita app\models\MgUnitaMisura|null */
/* @var $ivaVendita app\models\MgAliquotaIva|null */
/* @var $ivaAcquisto app\models\MgAliquotaIva|null */

$this->title = 'Wizard prodotti - Riepilogo';
$this->params['breadcrumbs'][] = ['label' => 'Articoli', 'url' => ['mgarticolo/index']];
$this->params['breadcrumbs'][] = ['label' => 'Wizard prodotti', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Riepilogo';

$defaults = $stato['defaults'] ?? [];
$maxAnteprima = 300;
?>
<div class="mgwizard-riepilogo card p-3 shadow-sm">
    <?= $this->render('_steps', ['attivo' => 5]) ?>

    <h5 class="mb-3">5. Riepilogo e generazione</h5>

    <div class="row">
        <div class="col-md-6">
            <table class="table table-sm">
                <tr><th style="width:40%">Modello</th><td><?= Html::encode($modello ? $modello->etichetta : '') ?></td></tr>
                <tr><th>Tessuti</th><td><?= $tessuti ? Html::encode(implode(', ', $tessuti)) : '<span class="text-muted">nessuno</span>' ?></td></tr>
                <tr><th>Colori</th><td><?= $colori ? Html::encode(implode(', ', $colori)) : '<span class="text-muted">nessuno</span>' ?></td></tr>
                <tr><th>Taglie</th><td><?= $taglie ? Html::encode(implode(', ', $taglie)) : '<span class="text-muted">nessuna</span>' ?></td></tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-sm">
                <tr><th style="width:40%">Prezzo</th><td><?= Html::encode(number_format((float) ($defaults['prezzo'] ?? 0), 4, ',', '.')) ?></td></tr>
                <tr><th>IVA vendita</th><td><?= $ivaVendita ? Html::encode($ivaVendita->descrizione) : '<span class="text-muted">--</span>' ?></td></tr>
                <tr><th>IVA acquisto</th><td><?= $ivaAcquisto ? Html::encode($ivaAcquisto->descrizione) : '<span class="text-muted">--</span>' ?></td></tr>
                <tr><th>Unità di misura</th><td><?= $unita ? Html::encode($unita->etichetta) : '<span class="text-muted">--</span>' ?></td></tr>
            </table>
        </div>
    </div>

    <div class="alert alert-info">
        Verranno creati <strong><?= count($combinazioni) ?></strong> articoli, ognuno con il proprio GUID.
    </div>

    <?php if (!empty($combinazioni)): ?>
        <div class="table-responsive" style="max-height:400px;overflow:auto;">
            <table class="table table-sm table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Codice</th>
                    <th>Descrizione</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (array_slice($combinazioni, 0, $maxAnteprima) as $i => $c): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= Html::encode($c['codice']) ?></td>
                        <td><?= Html::encode($c['descrizione']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (count($combinazioni) > $maxAnteprima): ?>
            <p class="text-muted small">Anteprima limitata ai primi <?= $maxAnteprima ?> articoli.</p>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-warning">Nessuna combinazione da generare. Torna indietro e seleziona almeno un attributo.</div>
    <?php endif; ?>

    <?= Html::beginForm(['genera'], 'post') ?>
    <div class="form-group mb-0">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Indietro', ['taglie'], ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::submitButton('<i class="fas fa-magic"></i> Genera ' . count($combinazioni) . ' articoli',
            ['class' => 'btn btn-success', 'disabled' => empty($combinazioni)]) ?>
    </div>
    <?= Html::endForm() ?>
</div>
