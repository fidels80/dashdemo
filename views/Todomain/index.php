<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ToDo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="todomain-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <h1 class="h4 mb-1"><i class="fas fa-check-square"></i> <?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Gestione dei task: crea, assegna e segui lo stato delle attività.</p>
        </div>
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-plus"></i> Nuovo Task', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-columns"></i> Board', ['board'], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-list-ol"></i> Backlog', ['backlog'], ['class' => 'btn btn-outline-primary']) ?>
        </div>
    </div>

    <table id="todomain-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>Stato</th>
            <th>Priorità</th>
            <th>Progresso</th>
            <th>Cliente</th>
            <th>Descrizione</th>
            <th>Data inizio</th>
            <th>Data fine</th>
            <th>Data scadenza</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= Html::encode($m->statodett->stato ?? '') ?></td>
                <td><?= Html::encode($m->priodett->priorita ?? '') ?></td>
                <td><?= ($m->progresso !== null && $m->progresso !== '') ? Html::encode($m->progresso) . '%' : '' ?></td>
                <td><?= Html::encode($m->clidett->ragione_sociale ?? '') ?></td>
                <td><?= Html::encode($m->descrizione) ?></td>
                <td data-order="<?= $m->data_inizio ? (int) strtotime($m->data_inizio) : 0 ?>"><?= $m->data_inizio ? Html::encode(date('d/m/Y', strtotime($m->data_inizio))) : '' ?></td>
                <td data-order="<?= $m->data_fine ? (int) strtotime($m->data_fine) : 0 ?>"><?= $m->data_fine ? Html::encode(date('d/m/Y', strtotime($m->data_fine))) : '' ?></td>
                <td data-order="<?= $m->data_scadenza ? (int) strtotime($m->data_scadenza) : 0 ?>"><?= $m->data_scadenza ? Html::encode(date('d/m/Y', strtotime($m->data_scadenza))) : '' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questo task?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('todomain-table', 0, 'asc'); ?>