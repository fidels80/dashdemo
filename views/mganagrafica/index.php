<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Anagrafica clienti/fornitori';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mganagrafica-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-plus"></i> Nuova anagrafica', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-list"></i> Documenti', ['mgdocumento/index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <table id="mganagrafica-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Codice</th>
            <th>Ragione sociale</th>
            <th>Partita IVA</th>
            <th>Città</th>
            <th>Telefono</th>
            <th>Email</th>
            <th>Tipo</th>
            <th>Attivo</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= Html::encode($m->codice) ?></td>
                <td><?= Html::encode($m->ragione_sociale) ?></td>
                <td><?= Html::encode($m->partita_iva) ?></td>
                <td><?= Html::encode($m->citta) ?></td>
                <td><?= Html::encode($m->telefono) ?></td>
                <td><?= Html::encode($m->email) ?></td>
                <td><?= Html::encode($m->tipo) ?></td>
                <td><?= $m->attivo ? 'Sì' : 'No' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questa anagrafica?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('mganagrafica-table', 2, 'asc'); ?>
