<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sottocommesse';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgsottocommessa-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-plus"></i> Nuova sottocommessa', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-diagram-project"></i> Commesse', ['mgcommessa/index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= Html::encode(Yii::$app->session->getFlash('error')) ?></div>
    <?php endif; ?>

    <table id="mgsottocommessa-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>Codice</th>
            <th>Descrizione</th>
            <th>Commessa</th>
            <th>Data inizio</th>
            <th>Data fine</th>
            <th>Anagrafica</th>
            <th>Attivo</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= Html::encode($m->codice) ?></td>
                <td><?= Html::encode($m->descrizione) ?></td>
                <td><?= $m->commessa ? Html::encode($m->commessa->codice . ' - ' . $m->commessa->descrizione) : '' ?></td>
                <td><?= $m->data_inizio ? Html::encode(\yii\helpers\Formatter::asDate($m->data_inizio, 'dd/MM/yyyy')) : '' ?></td>
                <td><?= $m->data_fine ? Html::encode(\yii\helpers\Formatter::asDate($m->data_fine, 'dd/MM/yyyy')) : '' ?></td>
                <td><?= $m->anagrafica ? Html::encode($m->anagrafica->codice . ' - ' . $m->anagrafica->ragione_sociale) : '' ?></td>
                <td><?= $m->attivo ? 'Sì' : 'No' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-copy"></i>', ['create', 'from' => $m->id], ['class' => 'btn btn-sm btn-secondary', 'title' => 'Duplica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questa sottocommessa?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('mgsottocommessa-table', 0, 'asc'); ?>
