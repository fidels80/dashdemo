<?php

use yii\helpers\Html;

/* @var $fornitore app\models\MgAnagrafica|null */
/* @var $blocchi array */
/* @var $messaggio string */
/* @var $mittente string */

$fmt = function ($v) { return number_format((float) $v, 2, ',', '.'); };
$oggi = date('d/m/Y');
?>
<div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #222;">
    <p>Gentile <?= Html::encode($fornitore ? $fornitore->ragione_sociale : 'Fornitore') ?>,</p>

    <p>
        a seguito del controllo dei documenti di trasporto ricevuti, rileviamo delle
        <strong>differenze di quantita'</strong> rispetto a quanto indicato nei documenti.
        Di seguito il dettaglio delle quantita' mancanti.
    </p>

    <?php foreach ($blocchi as $blocco): ?>
        <?php $doc = $blocco['documento']; ?>
        <h3 style="font-size: 15px; margin: 18px 0 6px;">
            Documento <?= Html::encode($doc->etichetta) ?>
            <?= $doc->data ? 'del ' . date('d/m/Y', strtotime($doc->data)) : '' ?>
        </h3>
        <table cellpadding="6" cellspacing="0" border="1"
               style="border-collapse: collapse; border-color: #ccc; width: 100%; font-size: 13px;">
            <thead>
            <tr style="background: #f2f2f2;">
                <th align="left">Codice</th>
                <th align="left">Descrizione</th>
                <th align="right">Q.ta documento</th>
                <th align="right">Q.ta riscontrata</th>
                <th align="right">Differenza</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($blocco['righe'] as $d): ?>
                <tr>
                    <td><?= Html::encode($d['riga']->codice_articolo) ?></td>
                    <td><?= Html::encode($d['riga']->descrizione) ?></td>
                    <td align="right"><?= $fmt($d['qta_documento']) ?></td>
                    <td align="right"><?= $fmt($d['qta_riscontrata']) ?></td>
                    <td align="right" style="color: <?= $d['differenza'] < 0 ? '#c00' : '#080' ?>;">
                        <?= $fmt($d['differenza']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <?php if (!empty($messaggio)): ?>
        <p style="margin-top: 16px; white-space: pre-line;"><?= Html::encode($messaggio) ?></p>
    <?php endif; ?>

    <p>
        Ti chiediamo di verificare e darci riscontro in merito alle differenze sopra indicate.
    </p>

    <p style="color: #666; font-size: 12px;">
        Comunicazione generata automaticamente il <?= $oggi ?><?= $mittente !== '' ? ' da ' . Html::encode($mittente) : '' ?>.
    </p>
</div>
