<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $filtri array */

$this->title = 'FAQ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crmfaq-index card p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="fas fa-question-circle"></i> Domande frequenti generate dai ticket</h5>
        <?= Html::a('<i class="fas fa-plus"></i> Nuova FAQ', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <?= Html::beginForm(['index'], 'get', ['class' => 'row mb-3']) ?>
        <div class="col-md-4 mb-2">
            <?= Html::textInput('q', $filtri['q'] ?? '', ['class' => 'form-control', 'placeholder' => 'Cerca domanda/soluzione/articolo...']) ?>
        </div>
        <div class="col-md-2 mb-2">
            <?= Html::dropDownList('attivo', $filtri['attivo'] ?? '', ['1' => 'Attive', '0' => 'Non attive'], ['class' => 'form-control', 'prompt' => 'Tutte']) ?>
        </div>
        <div class="col-md-2 mb-2">
            <?= Html::submitButton('<i class="fas fa-search"></i> Filtra', ['class' => 'btn btn-primary']) ?>
        </div>
    <?= Html::endForm() ?>

    <table id="crmfaq-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr><th>ID</th><th>Articolo</th><th>Domanda</th><th>Attiva</th><th>Ticket</th><th class="no-export">Azioni</th></tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= Html::encode($m->articolo ? $m->articolo->codice : ($m->codice_articolo ?: '')) ?></td>
                <td><?= Html::encode($m->domanda) ?></td>
                <td><?= $m->attivo ? 'Sì' : 'No' ?></td>
                <td><?= $m->ticket ? Html::encode($m->ticket->etichetta) : '' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning']) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('crmfaq-table', 0, 'desc'); ?>
