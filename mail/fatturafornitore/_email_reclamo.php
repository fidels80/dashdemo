<?php

use yii\helpers\Html;

/* @var $documento app\models\MgDocumento */
/* @var $fornitore app\models\MgAnagrafica|null */
/* @var $messaggio string */
/* @var $mittente string */

$oggi = date('d/m/Y');
$dataDoc = $documento->data ? date('d/m/Y', strtotime($documento->data)) : '';
?>
<div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #222;">
    <p>Gentile <?= Html::encode($fornitore ? $fornitore->ragione_sociale : 'Fornitore') ?>,</p>

    <p>
        in riferimento al vostro documento
        <strong><?= Html::encode($documento->numero) ?></strong>
        <?= $dataDoc !== '' ? 'del <strong>' . Html::encode($dataDoc) . '</strong>' : '' ?>
        <?= $documento->totale !== null ? '(importo ' . number_format((float) $documento->totale, 2, ',', '.') . ' EUR)' : '' ?>,
        vi segnaliamo quanto segue:
    </p>

    <p style="white-space: pre-line; border-left: 3px solid #ccc; padding-left: 10px; margin: 16px 0;">
        <?= Html::encode($messaggio) ?>
    </p>

    <p>
        Restiamo in attesa di un vostro cortese riscontro in merito.
    </p>

    <p>Distinti saluti.</p>

    <p style="color: #666; font-size: 12px;">
        Comunicazione generata automaticamente il <?= $oggi ?><?= $mittente !== '' ? ' da ' . Html::encode($mittente) : '' ?>.
    </p>
</div>
