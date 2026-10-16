<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $filtri array */
/* @var $tipi array */
/* @var $stati array */

$this->title = 'Le mie richieste';
?>
<div class="portale-richieste">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Le mie richieste</h4>
        <?= Html::a('<i class="fas fa-plus"></i> Nuova richiesta', ['richiesta-create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?= Html::beginForm(['richieste'], 'get', ['class' => 'row mb-3']) ?>
        <div class="col-md-3 mb-2">
            <?= Html::dropDownList('id_stato', $filtri['id_stato'] ?? '', $stati, ['class' => 'form-control', 'prompt' => 'Stato']) ?>
        </div>
        <div class="col-md-3 mb-2">
            <?= Html::dropDownList('id_tipo', $filtri['id_tipo'] ?? '', $tipi, ['class' => 'form-control', 'prompt' => 'Tipo']) ?>
        </div>
        <div class="col-md-2 mb-2">
            <?= Html::submitButton('<i class="fas fa-search"></i> Filtra', ['class' => 'btn btn-primary']) ?>
        </div>
    <?= Html::endForm() ?>

    <div class="card p-2">
        <table id="portale-richieste-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr><th>Ticket</th><th>Data</th><th>Tipo</th><th>Oggetto</th><th>Stato</th><th>Priorità</th><th class="no-export"></th></tr>
            </thead>
            <tbody>
            <?php foreach ($dataProvider->getModels() as $m): ?>
                <tr>
                    <td><?= Html::encode($m->etichetta) ?></td>
                    <td><?= $m->data_apertura ? Html::encode(date('d/m/Y', strtotime($m->data_apertura))) : '' ?></td>
                    <td><?= Html::encode($m->tipo->descrizione ?? '') ?></td>
                    <td><?= Html::encode($m->oggetto) ?></td>
                    <td><span class="badge badge-<?= Html::encode($m->statoColore) ?>"><?= Html::encode($m->stato->descrizione ?? '') ?></span></td>
                    <td><span class="badge badge-<?= Html::encode($m->prioritaColore) ?>"><?= Html::encode($m->priorita->descrizione ?? '—') ?></span></td>
                    <td><?= Html::a('<i class="fas fa-eye"></i>', ['richiesta', 'id' => $m->id], ['class' => 'btn btn-sm btn-info']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php DataTables::render('portale-richieste-table', 1, 'desc'); ?>
