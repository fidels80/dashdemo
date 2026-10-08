<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $tipi array */
/* @var $tipoSelezionato string|null */

$titolo = 'Attributi articolo';
if ($tipoSelezionato !== null && $tipoSelezionato !== '' && isset($tipi[$tipoSelezionato])) {
    $titolo = $tipi[$tipoSelezionato];
}
$this->title = $titolo;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgattributo-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-plus"></i> Nuovo attributo', ['create', 'tipo' => $tipoSelezionato], ['class' => 'btn btn-success']) ?>
            <?= Html::a('<i class="fas fa-box"></i> Articoli', ['mgarticolo/index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="fas fa-ruler"></i> Unità di misura', ['mgunitamisura/index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?= Html::beginForm(['index'], 'get', ['class' => 'card card-body mb-3']) ?>
        <div class="row">
            <div class="col-md-4 mb-2">
                <?= Html::dropDownList('tipo', $tipoSelezionato, $tipi, ['prompt' => 'Tutti i tipi...', 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i> Filtra</button>
            </div>
        </div>
    <?= Html::endForm() ?>

    <table id="mgattributo-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Codice</th>
            <th>Descrizione</th>
            <th>Attivo</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= Html::encode($m->tipoLabel) ?></td>
                <td><?= Html::encode($m->codice) ?></td>
                <td><?= Html::encode($m->descrizione) ?></td>
                <td><?= $m->attivo ? 'Sì' : 'No' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-copy"></i>', ['create', 'from' => $m->id, 'tipo' => $m->tipo], ['class' => 'btn btn-sm btn-secondary', 'title' => 'Duplica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questo attributo?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('mgattributo-table', 3, 'asc'); ?>
