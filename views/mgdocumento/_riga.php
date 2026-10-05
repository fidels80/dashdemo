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

$unitaJson = function ($articolo) {
    $out = [];
    foreach ($articolo->unitaMisura as $u) {
        $out[] = [
            'id' => (int) $u->id_unita_misura,
            'codice' => $u->unitaMisura ? $u->unitaMisura->codice : '',
            'etichetta' => $u->unitaMisura ? $u->unitaMisura->etichetta : '',
            'fattore' => (float) $u->fattore,
            'predefinita' => (int) $u->predefinita,
        ];
    }
    return json_encode($out);
};
?>
<tr class="riga-row" data-um="<?= Html::encode($val('id_unita_misura')) ?>">
    <td>
        <select name="righe[<?= $index ?>][id_articolo]" class="form-control form-control-sm riga-articolo">
            <option value="">--</option>
            <?php foreach ($articoliModels as $a): ?>
                <option value="<?= $a->id ?>"
                        data-codice="<?= Html::encode($a->codice) ?>"
                        data-prezzo="<?= Html::encode($a->prezzo) ?>"
                        data-iva="<?= Html::encode($a->iva) ?>"
                        data-iva-vendita="<?= Html::encode($a->ivaVenditaPerc) ?>"
                        data-iva-acquisto="<?= Html::encode($a->ivaAcquistoPerc) ?>"
                        data-descrizione="<?= Html::encode($a->descrizione) ?>"
                        data-taglia="<?= Html::encode($a->taglia ? $a->taglia->descrizione : '') ?>"
                        data-colore="<?= Html::encode($a->colore ? $a->colore->descrizione : '') ?>"
                        data-tessuto="<?= Html::encode($a->tessuto ? $a->tessuto->descrizione : '') ?>"
                        data-um="<?= Html::encode($unitaJson($a)) ?>"
                    <?= ((string) $val('id_articolo') === (string) $a->id) ? 'selected' : '' ?>>
                    <?= Html::encode($a->codice . ' - ' . $a->descrizione) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
    <td><input type="text" name="righe[<?= $index ?>][codice_articolo]" class="form-control form-control-sm riga-codice" value="<?= Html::encode($val('codice_articolo')) ?>"></td>
    <td><input type="text" name="righe[<?= $index ?>][descrizione]" class="form-control form-control-sm riga-desc" value="<?= Html::encode($val('descrizione')) ?>"></td>
    <td class="col-varianti"><input type="text" name="righe[<?= $index ?>][taglia]" class="form-control form-control-sm riga-taglia" value="<?= Html::encode($val('taglia')) ?>"></td>
    <td class="col-varianti"><input type="text" name="righe[<?= $index ?>][colore]" class="form-control form-control-sm riga-colore" value="<?= Html::encode($val('colore')) ?>"></td>
    <td class="col-varianti"><input type="text" name="righe[<?= $index ?>][tessuto]" class="form-control form-control-sm riga-tessuto" value="<?= Html::encode($val('tessuto')) ?>"></td>
    <td>
        <select name="righe[<?= $index ?>][id_unita_misura]" class="form-control form-control-sm riga-um"></select>
        <input type="hidden" name="righe[<?= $index ?>][um]" class="riga-um-codice" value="<?= Html::encode($val('um')) ?>">
        <input type="hidden" name="righe[<?= $index ?>][fattore]" class="riga-fattore" value="<?= Html::encode($val('fattore') !== '' && $val('fattore') !== null ? $val('fattore') : 1) ?>">
        <input type="hidden" class="riga-prezzo-base" value="<?= Html::encode($prezzo) ?>">
        <input type="hidden" name="righe[<?= $index ?>][id_rapportino]" class="riga-id-rap" value="<?= Html::encode($val('id_rapportino')) ?>">
    </td>
    <td><input type="number" step="any" name="righe[<?= $index ?>][qta]" class="form-control form-control-sm riga-qta text-right" value="<?= Html::encode($val('qta')) ?>"></td>
    <td><input type="number" step="any" name="righe[<?= $index ?>][prezzo]" class="form-control form-control-sm riga-prezzo text-right" value="<?= Html::encode($val('prezzo')) ?>"></td>
    <td><input type="number" step="any" name="righe[<?= $index ?>][sconto]" class="form-control form-control-sm riga-sconto text-right" value="<?= Html::encode($val('sconto')) ?>"></td>
    <td><input type="number" step="any" name="righe[<?= $index ?>][iva]" class="form-control form-control-sm riga-iva text-right" value="<?= Html::encode($val('iva')) ?>"></td>
    <td><input type="text" class="form-control form-control-sm riga-totale text-right" value="<?= number_format($totale, 2, ',', '.') ?>" readonly></td>
    <td class="text-center text-nowrap">
        <button type="button" class="btn btn-sm btn-outline-info riga-rap-dettaglio" data-id-rap="<?= Html::encode($val('id_rapportino')) ?>" title="Dettaglio rapportino" style="<?= $val('id_rapportino') ? '' : 'display:none;' ?>"><i class="fas fa-file-alt"></i></button>
        <button type="button" class="btn btn-sm btn-outline-primary riga-duplica" title="Duplica riga"><i class="fas fa-copy"></i></button>
        <button type="button" class="btn btn-sm btn-danger riga-remove" title="Rimuovi riga"><i class="fas fa-times"></i></button>
    </td>
</tr>
