<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Scadenze';

function statoScadenzaLabel($stato)
{
    if ($stato === 'aperta') {
        return ['Aperta', 'warning'];
    }
    return ['Pagata/Chiusa', 'success'];
}
?>
<div class="portale-scadenze">
    <h4 class="mb-3">Le mie scadenze</h4>

    <div class="card p-2">
        <table id="portale-scadenze-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr><th>Data</th><th>Documento</th><th>Azienda</th><th>Rata</th><th class="text-right">Importo</th><th>Stato</th></tr>
            </thead>
            <tbody>
            <?php foreach ($dataProvider->getModels() as $s): ?>
                <?php [$lab, $col] = statoScadenzaLabel($s->stato); ?>
                <tr>
                    <td><?= Html::encode(date('d/m/Y', strtotime($s->data_scadenza))) ?></td>
                    <td><?= Html::encode($s->documento->etichetta ?? '') ?></td>
                    <td><?= Html::encode($s->documento->anagrafica->ragione_sociale ?? '') ?></td>
                    <td><?= (int) $s->progressivo ?></td>
                    <td class="text-right"><?= number_format((float) $s->importo, 2, ',', '.') ?> €</td>
                    <td><span class="badge badge-<?= $col ?>"><?= $lab ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php DataTables::render('portale-scadenze-table', 0, 'asc'); ?>
