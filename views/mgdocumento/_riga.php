<?php

use yii\helpers\Html;

/* @var $index int|string */
/* @var $model app\models\MgDocumentoRiga|null */
/* @var $articoliModels app\models\MgArticolo[] */

$val = function ($attr) use ($model) {
    return $model ? $model->$attr : '';
};
$qta = (float) $val('qta');
$prezzo = (float) $val('prezzo');
$sconto = (float) $val('sconto');
$totale = round($qta * $prezzo * (1 - $sconto / 100), 2);
?>
<tr class="riga-row">
    <td>
        <select name="righe[<?= $index ?>][id_articolo]" class="form-control form-control-sm riga-articolo">
            <option value="">--</option>
            <?php foreach ($articoliModels as $a): ?>
                <option value="<?= $a->id ?>"
                        data-codice="<?= Html::encode($a->codice) ?>"
                        data-prezzo="<?= Html::encode($a->prezzo) ?>"
                        data-iva="<?= Html::encode($a->iva) ?>"
                        data-descrizione="<?= Html::encode($a->descrizione) ?>"
                    <?= ((string) $val('id_articolo') === (string) $a->id) ? 'selected' : '' ?>>
                    <?= Html::encode($a->codice . ' - ' . $a->descrizione) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
    <td><input type="text" name="righe[<?= $index ?>][codice_articolo]" class="form-control form-control-sm riga-codice" value="<?= Html::encode($val('codice_articolo')) ?>"></td>
    <td><input type="text" name="righe[<?= $index ?>][descrizione]" class="form-control form-control-sm riga-desc" value="<?= Html::encode($val('descrizione')) ?>"></td>
    <td><input type="number" step="0.0001" name="righe[<?= $index ?>][qta]" class="form-control form-control-sm riga-qta text-right" value="<?= Html::encode($val('qta')) ?>"></td>
    <td><input type="number" step="0.0001" name="righe[<?= $index ?>][prezzo]" class="form-control form-control-sm riga-prezzo text-right" value="<?= Html::encode($val('prezzo')) ?>"></td>
    <td><input type="number" step="0.01" name="righe[<?= $index ?>][sconto]" class="form-control form-control-sm riga-sconto text-right" value="<?= Html::encode($val('sconto')) ?>"></td>
    <td><input type="number" step="0.01" name="righe[<?= $index ?>][iva]" class="form-control form-control-sm riga-iva text-right" value="<?= Html::encode($val('iva')) ?>"></td>
    <td><input type="text" class="form-control form-control-sm riga-totale text-right" value="<?= number_format($totale, 2, ',', '.') ?>" readonly></td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-danger riga-remove"><i class="fas fa-times"></i></button>
    </td>
</tr>
