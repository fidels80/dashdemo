<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fonti dati report';
$this->params['breadcrumbs'][] = ['label' => 'Report Designer', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reportdesigner-fonti card p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h2 class="m-0"><i class="fas fa-database text-secondary"></i> <?= Html::encode($this->title) ?></h2>
        <div>
            <?= Html::a('<i class="fas fa-plus"></i> Nuova fonte', ['fonte-create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-arrow-left"></i> Report', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <p class="text-muted small">
        Una fonte è un dataset (tabella + join, anche tra database diversi sulla stessa istanza SQL Server)
        oppure una query SQL tecnica. I report si costruiscono sopra le fonti.
    </p>

    <table id="fonti-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Codice</th>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Connessione</th>
            <th>Tabella base</th>
            <th>Attivo</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= Html::encode($m->codice) ?></td>
                <td><?= Html::encode($m->nome) ?></td>
                <td><?= Html::encode($m->tipo) ?></td>
                <td><?= Html::encode($m->connessione) ?></td>
                <td><?= Html::encode($m->base_tabella) ?></td>
                <td><?= $m->attivo ? '<span class="badge badge-success">Sì</span>' : '<span class="badge badge-secondary">No</span>' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-pen"></i>', ['fonte-update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['fonte-delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questa fonte dati?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('fonti-table', 0, 'asc'); ?>
