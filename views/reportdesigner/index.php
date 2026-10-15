<?php

use yii\helpers\Html;
use app\components\DataTables;
use app\components\Licensing;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $features array */

$this->title = 'Report Designer';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reportdesigner-index card p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h2 class="m-0"><i class="fas fa-tools text-secondary"></i> <?= Html::encode($this->title) ?></h2>
        <div>
            <?= Html::a('<i class="fas fa-plus"></i> Nuovo report', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-database"></i> Fonti dati', ['fonti'], ['class' => 'btn btn-outline-primary']) ?>
            <?= Html::a('<i class="fas fa-chart-bar"></i> Centro report', ['report/catalogo'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <table id="reportdesigner-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Codice</th>
            <th>Nome</th>
            <th>Categoria</th>
            <th>Tipo</th>
            <th>Fonte</th>
            <th>Attivo</th>
            <th>Licenza</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <?php $fonte = $m->fonte; ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= Html::encode($m->codice) ?></td>
                <td><?= $m->icona ? '<i class="fas fa-' . Html::encode($m->icona) . ' text-secondary mr-1"></i>' : '' ?><?= Html::encode($m->nome) ?></td>
                <td><?= Html::encode($m->categoria) ?></td>
                <td><?= Html::encode($m->tipo) ?></td>
                <td><?= Html::encode($fonte ? $fonte->nome : '—') ?></td>
                <td><?= $m->attivo ? '<span class="badge badge-success">Sì</span>' : '<span class="badge badge-secondary">No</span>' ?></td>
                <td>
                    <?php if (empty($m->licenza_feature)): ?>
                        <span class="text-muted">—</span>
                    <?php else: ?>
                        <?php $ok = Licensing::hasFeature($m->licenza_feature); ?>
                        <span class="badge badge-<?= $ok ? 'success' : 'danger' ?>"><?= Html::encode($m->licenza_feature) ?></span>
                    <?php endif; ?>
                </td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-play"></i>', ['report/esegui', 'codice' => $m->codice], ['class' => 'btn btn-sm btn-info', 'title' => 'Esegui']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questo report?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (empty($features)): ?>
        <div class="alert alert-info mt-3 mb-0 small">
            Nessuna feature di licenza attiva: i report senza feature associata restano sempre disponibili.
        </div>
    <?php else: ?>
        <div class="alert alert-light border mt-3 mb-0 small">
            Feature di licenza attive: <strong><?= Html::encode(implode(', ', $features)) ?></strong>
        </div>
    <?php endif; ?>
</div>
<?php DataTables::render('reportdesigner-table', 0, 'asc'); ?>
