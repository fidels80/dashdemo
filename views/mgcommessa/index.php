<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Commesse';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgcommessa-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-plus"></i> Nuova commessa', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-diagram-project"></i> Sottocommesse', ['mgsottocommessa/index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <table id="mgcommessa-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>Codice</th>
            <th>Descrizione</th>
            <th>Data inizio</th>
            <th>Data fine</th>
            <th>Anagrafica</th>
            <th>Sottocommesse</th>
            <th>Attivo</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= Html::encode($m->codice) ?></td>
                <td><?= Html::encode($m->descrizione) ?></td>
                <td><?= $m->data_inizio ? Html::encode(\yii\helpers\Formatter::asDate($m->data_inizio, 'dd/MM/yyyy')) : '' ?></td>
                <td><?= $m->data_fine ? Html::encode(\yii\helpers\Formatter::asDate($m->data_fine, 'dd/MM/yyyy')) : '' ?></td>
                <td><?= $m->anagrafica ? Html::encode($m->anagrafica->codice . ' - ' . $m->anagrafica->ragione_sociale) : '' ?></td>
                <td><?= count($m->sottocommesse) ?></td>
                <td><?= $m->attivo ? 'Sì' : 'No' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questa commessa? Le sottocommesse collegate verranno eliminate.', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('mgcommessa-table', 0, 'asc'); ?>
