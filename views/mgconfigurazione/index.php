<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Configurazione';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgconfigurazione-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-plus"></i> Nuovo parametro', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Parametri standard del gestionale in coppia <strong>codice/valore</strong>. I codici che iniziano con
        <code>fe.</code> sono usati per generare le <strong>fatture elettroniche</strong> (dati del cedente e
        di trasmissione al Sistema di Interscambio).
    </div>

    <table id="mgconfigurazione-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Codice</th>
            <th>Descrizione</th>
            <th>Valore</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><code><?= Html::encode($m->codice) ?></code></td>
                <td><?= Html::encode($m->descrizione) ?></td>
                <td><?= Html::encode($m->valore) ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-copy"></i>', ['create', 'from' => $m->id], ['class' => 'btn btn-sm btn-secondary', 'title' => 'Duplica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questo parametro di configurazione?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('mgconfigurazione-table', 1, 'asc'); ?>
