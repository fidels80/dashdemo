<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Changelog';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashchangelog-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <h1 class="h4 mb-1"><i class="fas fa-clipboard-list"></i> <?= Html::encode($this->title) ?></h1>
            <p class="text-muted mb-0">Registro di tutto ciò che è stato modificato e in che modo. Pagina riservata agli operatori di livello elevato.</p>
        </div>
    </div>

    <table id="changelog-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>Data</th>
            <th>Versione</th>
            <th>Tipo</th>
            <th>Titolo</th>
            <th>Autore</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td data-order="<?= $m->data_commit ? (int) strtotime($m->data_commit) : 0 ?>"><?= $m->data_commit ? Html::encode(date('d-m-Y H:i', strtotime($m->data_commit))) : '—' ?></td>
                <td><?= Html::encode($m->versione) ?></td>
                <td><?= Html::encode($m->tipoLabel) ?></td>
                <td><?= Html::encode($m->titolo) ?></td>
                <td><?= Html::encode($m->autore) ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi dettaglio']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('changelog-table', 0, 'desc'); ?>
