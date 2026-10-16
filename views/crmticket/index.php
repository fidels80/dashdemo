<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $filtri array */
/* @var $tipi array */
/* @var $stati array */
/* @var $priorita array */
/* @var $operatori array */
/* @var $anagrafiche array */

$this->title = 'Ticket CRM';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crmticket-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-plus"></i> Nuova richiesta', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-tags"></i> Configurazione', ['crmconfig/index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?= Html::beginForm(['index'], 'get', ['class' => 'row mb-3']) ?>
        <div class="col-md-2 mb-2">
            <?= Html::dropDownList('id_stato', $filtri['id_stato'] ?? '', $stati, ['class' => 'form-control', 'prompt' => 'Stato']) ?>
        </div>
        <div class="col-md-2 mb-2">
            <?= Html::dropDownList('id_tipo', $filtri['id_tipo'] ?? '', $tipi, ['class' => 'form-control', 'prompt' => 'Tipo']) ?>
        </div>
        <div class="col-md-2 mb-2">
            <?= Html::dropDownList('id_priorita', $filtri['id_priorita'] ?? '', $priorita, ['class' => 'form-control', 'prompt' => 'Priorità']) ?>
        </div>
        <div class="col-md-2 mb-2">
            <?= Html::dropDownList('assegnato_a', $filtri['assegnato_a'] ?? '', $operatori, ['class' => 'form-control', 'prompt' => 'Assegnato a']) ?>
        </div>
        <div class="col-md-2 mb-2">
            <?= Html::dropDownList('aperto', $filtri['aperto'] ?? '', ['1' => 'Solo aperti', '0' => 'Solo chiusi'], ['class' => 'form-control', 'prompt' => 'Aperti/chiusi']) ?>
        </div>
        <div class="col-md-2 mb-2">
            <div class="input-group">
                <?= Html::textInput('q', $filtri['q'] ?? '', ['class' => 'form-control', 'placeholder' => 'Cerca...']) ?>
                <div class="input-group-append">
                    <?= Html::submitButton('<i class="fas fa-search"></i>', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    <?= Html::endForm() ?>

    <table id="crmticket-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>Ticket</th>
            <th>Data</th>
            <th>Tipo</th>
            <th>Cliente</th>
            <th>Oggetto</th>
            <th>Stato</th>
            <th>Priorità</th>
            <th>Assegnato a</th>
            <th>Aperto da</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= Html::encode($m->etichetta) ?></td>
                <td><?= $m->data_apertura ? Html::encode(date('d/m/Y', strtotime($m->data_apertura))) : '' ?></td>
                <td><?= Html::encode($m->tipo->descrizione ?? '') ?></td>
                <td><?= Html::encode($m->anagrafica->ragione_sociale ?? '') ?></td>
                <td><?= Html::encode($m->oggetto) ?></td>
                <td><span class="badge badge-<?= Html::encode($m->statoColore) ?>"><?= Html::encode($m->stato->descrizione ?? '') ?></span></td>
                <td><span class="badge badge-<?= Html::encode($m->prioritaColore) ?>"><?= Html::encode($m->priorita->descrizione ?? '—') ?></span></td>
                <td><?= Html::encode($m->assegnato->username ?? '—') ?></td>
                <td><?= Html::encode($m->utente->username ?? '—') ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('crmticket-table', 1, 'desc'); ?>
