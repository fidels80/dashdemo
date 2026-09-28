<?php

use yii\helpers\Html;

/* @var $attivo int */

$steps = [
    1 => 'Modello',
    2 => 'Tessuti',
    3 => 'Colori',
    4 => 'Taglie',
    5 => 'Riepilogo',
];
?>
<ol class="list-unstyled d-flex flex-wrap mb-4">
    <?php foreach ($steps as $n => $label): ?>
        <li class="mr-4 mb-2 <?= $n == $attivo ? 'font-weight-bold' : ($n < $attivo ? 'text-success' : 'text-muted') ?>">
            <span class="badge <?= $n <= $attivo ? 'badge-success' : 'badge-secondary' ?>"><?= $n ?></span>
            <?= Html::encode($label) ?>
        </li>
    <?php endforeach; ?>
</ol>
