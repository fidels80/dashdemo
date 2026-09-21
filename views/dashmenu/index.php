<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gestione Menu';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashmenu-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-user-check"></i> Assegna agli utenti', ['assegna'], ['class' => 'btn btn-outline-primary']) ?>
            <?= Html::a('<i class="fas fa-plus"></i> Nuova voce', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="alert alert-secondary mb-3">
        <i class="fas fa-info-circle"></i>
        Le voci con <strong>genitore</strong> diventano <strong>sottovoci</strong> di menu.
        Il livello <strong>100</strong> e gli utenti super vedono sempre tutte le voci.
    </div>

    <table id="dashmenu-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Etichetta</th>
            <th>Codice</th>
            <th>Icona</th>
            <th>URL</th>
            <th>Genitore</th>
            <th>Liv. min</th>
            <th>Ordine</th>
            <th>Per tutti</th>
            <th>Attivo</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= $m->genitore_id ? '— ' : '' ?><?= Html::encode($m->label) ?></td>
                <td><?= Html::encode($m->codice) ?></td>
                <td><?= Html::encode($m->icona) ?></td>
                <td><?= Html::encode($m->url) ?></td>
                <td><?= Html::encode($m->genitore->label ?? '(radice)') ?></td>
                <td><?= (int) $m->livello_min ?></td>
                <td><?= (int) $m->ordine ?></td>
                <td><?= $m->per_tutti ? 'Sì' : 'No' ?></td>
                <td><?= $m->attivo ? 'Sì' : 'No' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questa voce?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('dashmenu-table', 7, 'asc'); ?>
