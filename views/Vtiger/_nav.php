<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $active string|null  azione corrente (search|progetti|ganttprogetti|ticket) */

$active = $active ?? null;

$items = [
    'search' => ['label' => 'Operatori', 'url' => ['vtiger/search'], 'icon' => 'fa-magnifying-glass'],
    'progetti' => ['label' => 'Progetti', 'url' => ['vtiger/progetti'], 'icon' => 'fa-diagram-project'],
    'ganttprogetti' => ['label' => 'Gantt', 'url' => ['vtiger/ganttprogetti'], 'icon' => 'fa-chart-gantt'],
    'ticket' => ['label' => 'Ticket', 'url' => ['vtiger/ticket'], 'icon' => 'fa-ticket'],
];
?>
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="btn-group flex-wrap" role="group" aria-label="Strumenti Vtiger">
            <?php foreach ($items as $key => $item): ?>
                <?php
                $isActive = ($active === $key);
                $class = 'btn btn-sm ' . ($isActive ? 'btn-primary' : 'btn-outline-primary');
                ?>
                <?= Html::a('<i class="fas ' . $item['icon'] . ' mr-1"></i> ' . $item['label'], $item['url'], [
                    'class' => $class,
                ]) ?>
            <?php endforeach; ?>
        </div>
        <a href="http://crm.ilvbc.it:8090/index.php" target="_blank" rel="noopener"
           class="btn btn-sm btn-outline-success">
            <i class="fas fa-external-link-alt mr-1"></i> Apri Vtiger
        </a>
    </div>
</div>