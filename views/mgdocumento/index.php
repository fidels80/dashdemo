<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $tipi array */
/* @var $anagrafiche array */
/* @var $filters array */

$this->title = 'Documenti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgdocumento-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-list"></i> Tipi documento', ['mgtipodocumento/index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="fas fa-plus"></i> Nuovo documento', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?= Html::beginForm(['index'], 'get', ['class' => 'card card-body mb-3']) ?>
        <div class="row">
            <div class="col-md-3 mb-2">
                <?= Html::dropDownList('id_tipo', $filters['id_tipo'], $tipi, ['prompt' => 'Tipo documento...', 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-3 mb-2">
                <?= Html::dropDownList('id_anagrafica', $filters['id_anagrafica'], $anagrafiche, ['prompt' => 'Cliente/Fornitore...', 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-2 mb-2">
                <?= Html::textInput('anno', $filters['anno'], ['class' => 'form-control', 'placeholder' => 'Anno']) ?>
            </div>
            <div class="col-md-2 mb-2">
                <?= Html::textInput('q', $filters['q'], ['class' => 'form-control', 'placeholder' => 'Cerca...']) ?>
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i> Filtra</button>
            </div>
        </div>
    <?= Html::endForm() ?>

    <table id="mgdocumento-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Documento</th>
            <th>Data</th>
            <th>Cliente/Fornitore</th>
            <th>Descrizione</th>
            <th>Stato</th>
            <th>Totale</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= Html::encode($m->codice_tipo) ?></td>
                <td><?= Html::encode($m->etichetta) ?></td>
                <td><?= $m->data ? date('d/m/Y', strtotime($m->data)) : '' ?></td>
                <td><?= Html::encode($m->anagrafica->ragione_sociale ?? '') ?></td>
                <td><?= Html::encode($m->descrizione) ?></td>
                <td><?= Html::encode($m->stato) ?></td>
                <td class="text-end"><?= number_format((float) $m->totale, 2, ',', '.') ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questo documento? Il numero tornerà disponibile.', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('mgdocumento-table', 0, 'desc'); ?>
