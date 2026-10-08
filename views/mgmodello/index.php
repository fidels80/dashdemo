<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $modelli app\models\MgAttributoArticolo[] */
/* @var $articoliPerModello array */
/* @var $tessutiPerModello array */

$this->title = 'Modelli articolo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgmodello-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-plus"></i> Nuovo modello', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-boxes"></i> Articoli', ['mgarticolo/index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="fas fa-magic"></i> Wizard prodotti', ['mgwizard/index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <table id="mgmodello-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Codice</th>
            <th>Modello</th>
            <th>Tessuti associati</th>
            <th class="text-right">Articoli</th>
            <th>Attivo</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($modelli as $m): ?>
            <?php $tessuti = $tessutiPerModello[(int) $m->id] ?? []; ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= Html::encode($m->codice) ?></td>
                <td><?= Html::encode($m->descrizione) ?></td>
                <td><?= Html::encode(implode(', ', array_filter($tessuti))) ?></td>
                <td class="text-right"><?= (int) ($articoliPerModello[(int) $m->id] ?? 0) ?></td>
                <td><?= $m->attivo ? 'Sì' : 'No' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-copy"></i>', ['create', 'from' => $m->id], ['class' => 'btn btn-sm btn-secondary', 'title' => 'Duplica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questo modello?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('mgmodello-table', 2, 'asc'); ?>
