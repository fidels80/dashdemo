<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $filtri array */
/* @var $anagrafiche array */

$this->title = 'Documenti';
?>
<div class="portale-documenti">
    <h4 class="mb-3">I miei documenti</h4>

    <?= Html::beginForm(['documenti'], 'get', ['class' => 'row mb-3']) ?>
        <div class="col-md-3 mb-2">
            <?= Html::dropDownList('id_anagrafica', $filtri['id_anagrafica'] ?? '', $anagrafiche, ['class' => 'form-control', 'prompt' => 'Tutte le aziende']) ?>
        </div>
        <div class="col-md-3 mb-2">
            <?= Html::textInput('q', $filtri['q'] ?? '', ['class' => 'form-control', 'placeholder' => 'Cerca descrizione...']) ?>
        </div>
        <div class="col-md-2 mb-2">
            <?= Html::submitButton('<i class="fas fa-search"></i> Filtra', ['class' => 'btn btn-primary']) ?>
        </div>
    <?= Html::endForm() ?>

    <div class="card p-2">
        <table id="portale-documenti-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr><th>Documento</th><th>Data</th><th>Tipo</th><th>Azienda</th><th>Descrizione</th><th class="text-right">Totale</th><th class="no-export"></th></tr>
            </thead>
            <tbody>
            <?php foreach ($dataProvider->getModels() as $m): ?>
                <tr>
                    <td><?= Html::encode($m->etichetta) ?></td>
                    <td><?= $m->data ? Html::encode(date('d/m/Y', strtotime($m->data))) : '' ?></td>
                    <td><?= Html::encode($m->tipo->descrizione ?? '') ?></td>
                    <td><?= Html::encode($m->anagrafica->ragione_sociale ?? '') ?></td>
                    <td><?= Html::encode($m->descrizione) ?></td>
                    <td class="text-right"><?= number_format((float) $m->totale, 2, ',', '.') ?></td>
                    <td><?= Html::a('<i class="fas fa-eye"></i>', ['documento', 'id' => $m->id], ['class' => 'btn btn-sm btn-info']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php DataTables::render('portale-documenti-table', 1, 'desc'); ?>
