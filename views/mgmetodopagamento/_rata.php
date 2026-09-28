<?php

use yii\helpers\Html;

/* @var $index int|string */
/* @var $model app\models\MgMetodoPagamentoRata|null */

$val = function ($attr) use ($model) {
    return $model ? $model->$attr : '';
};
?>
<tr class="rata-row">
    <td class="rata-num text-center align-middle"></td>
    <td>
        <input type="number" name="rate[<?= $index ?>][giorni]"
               class="form-control form-control-sm rata-giorni text-right"
               value="<?= Html::encode($val('giorni') === '' ? 0 : $val('giorni')) ?>">
    </td>
    <td>
        <input type="number" step="0.01" name="rate[<?= $index ?>][percentuale]"
               class="form-control form-control-sm rata-pct text-right"
               value="<?= Html::encode($val('percentuale') === '' ? 100 : $val('percentuale')) ?>">
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-danger rata-remove"><i class="fas fa-times"></i></button>
    </td>
</tr>
