<?php

use yii\helpers\Html;

/* @var $rows array */
/* @var $filterDesc string */

$cols = ['indirizzo', 'clienteNome', 'orario', 'nominativi', 'mezzi', 'note'];
$prev = [];
foreach ($cols as $c) {
    $prev[$c] = null;
}
$prevNominativiKey = null;
$groupColors = ['#ffffff', '#f0f4ff'];
$groupIdx = 0;
?>

<table>
    <thead>
        <tr>
            <th style="width: 22%;">Indirizzo</th>
            <th style="width: 20%;">Cliente</th>
            <th style="width: 10%;">Orario</th>
            <th style="width: 20%;">Nominativi</th>
            <th style="width: 10%;">Mezzi</th>
            <th style="width: 18%;">Note</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($rows)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">Nessun dato da esportare.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($rows as $i => $r): ?>
                <?php
                $isNewGroup = ($r['nominativiKey'] !== $prevNominativiKey);
                if ($isNewGroup) {
                    $groupIdx++;
                    $prevNominativiKey = $r['nominativiKey'];
                }
                $bgColor = $groupColors[($groupIdx - 1) % count($groupColors)];
                $bold = $isNewGroup ? 'font-weight: bold;' : '';

                $dIndirizzo = ($r['indirizzo'] === $prev['indirizzo']) ? '' : $r['indirizzo'];
                $dClienteNome = ($r['clienteNome'] === $prev['clienteNome']) ? '' : $r['clienteNome'];
                $dOrario = ($r['orario'] === $prev['orario']) ? '' : $r['orario'];
                $dNominativi = ($r['nominativi'] === $prev['nominativi']) ? '' : $r['nominativi'];
                $dMezzi = ($r['mezzi'] === $prev['mezzi']) ? '' : $r['mezzi'];
                $dNote = ($r['note'] === $prev['note']) ? '' : $r['note'];

                $prev['indirizzo'] = $r['indirizzo'];
                $prev['clienteNome'] = $r['clienteNome'];
                $prev['orario'] = $r['orario'];
                $prev['nominativi'] = $r['nominativi'];
                $prev['mezzi'] = $r['mezzi'];
                $prev['note'] = $r['note'];
                ?>
                <tr style="background-color: <?= $bgColor ?>;">
                    <td style="<?= $bold ?>"><?= $dIndirizzo ? Html::encode($dIndirizzo) : '<span class="cell-empty">"</span>' ?></td>
                    <td style="<?= $bold ?>"><?= $dClienteNome ? Html::encode($dClienteNome) : '<span class="cell-empty">"</span>' ?></td>
                    <td style="white-space: nowrap; <?= $bold ?>"><?= $dOrario ? Html::encode($dOrario) : '<span class="cell-empty">"</span>' ?></td>
                    <td class="nominativi-cell" style="<?= $bold ?>"><?= $dNominativi ? Html::encode($dNominativi) : '<span class="cell-empty">"</span>' ?></td>
                    <td style="<?= $bold ?>"><?= $dMezzi ? Html::encode($dMezzi) : '<span class="cell-empty">"</span>' ?></td>
                    <td><?= $dNote ? Html::encode($dNote) : '' ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
