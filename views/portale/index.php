<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $totale int */
/* @var $aperti int */
/* @var $recenti app\models\MgCrmTicket[] */
/* @var $documenti int */
/* @var $scadenze int */

$this->title = 'Le mie richieste';
$apertiCards = [
    ['label' => 'Richieste totali', 'value' => $totale, 'icon' => 'life-ring', 'color' => 'primary'],
    ['label' => 'Richieste aperte', 'value' => $aperti, 'icon' => 'folder-open', 'color' => 'warning'],
    ['label' => 'Documenti', 'value' => $documenti, 'icon' => 'file-invoice', 'color' => 'info'],
    ['label' => 'Scadenze', 'value' => $scadenze, 'icon' => 'calendar-check', 'color' => 'success'],
];
?>
<div class="portale-index">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Benvenuto nel tuo portale</h4>
        <?= Html::a('<i class="fas fa-plus"></i> Nuova richiesta', ['richiesta-create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="row">
        <?php foreach ($apertiCards as $c): ?>
            <div class="col-md-3 col-6">
                <div class="small-box bg-<?= $c['color'] ?>">
                    <div class="inner">
                        <h3><?= (int) $c['value'] ?></h3>
                        <p><?= Html::encode($c['label']) ?></p>
                    </div>
                    <div class="icon"><i class="fas fa-<?= $c['icon'] ?>"></i></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <span><i class="fas fa-clock"></i> Richieste recenti</span>
            <?= Html::a('Vedi tutte', ['richieste'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
        <div class="card-body p-0">
            <?php if (empty($recenti)): ?>
                <p class="text-muted p-3 mb-0">Non hai ancora aperto richieste.</p>
            <?php else: ?>
                <table class="table table-striped mb-0">
                    <thead>
                    <tr><th>Ticket</th><th>Tipo</th><th>Oggetto</th><th>Stato</th><th>Data</th><th></th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recenti as $t): ?>
                        <tr>
                            <td><?= Html::encode($t->etichetta) ?></td>
                            <td><?= Html::encode($t->tipo->descrizione ?? '') ?></td>
                            <td><?= Html::encode($t->oggetto) ?></td>
                            <td><span class="badge badge-<?= Html::encode($t->statoColore) ?>"><?= Html::encode($t->stato->descrizione ?? '') ?></span></td>
                            <td><?= $t->data_apertura ? Html::encode(date('d/m/Y', strtotime($t->data_apertura))) : '' ?></td>
                            <td><?= Html::a('<i class="fas fa-eye"></i>', ['richiesta', 'id' => $t->id], ['class' => 'btn btn-sm btn-info']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
