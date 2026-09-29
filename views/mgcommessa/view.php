<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgCommessa */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Commesse', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgcommessa-view">

    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-list"></i> Sottocommesse', ['mgsottocommessa/index'], ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questa commessa? Le sottocommesse collegate verranno eliminate.', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codice',
            'descrizione',
            'data_inizio',
            'data_fine',
            [
                'attribute' => 'id_anagrafica',
                'label' => 'Anagrafica',
                'value' => $model->anagrafica ? $model->anagrafica->codice . ' - ' . $model->anagrafica->ragione_sociale : '—',
            ],
            ['attribute' => 'attivo', 'format' => 'boolean'],
        ],
    ]) ?>

    <h3 class="mt-4">Sottocommesse</h3>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Codice</th>
            <th>Descrizione</th>
            <th>Data inizio</th>
            <th>Data fine</th>
            <th>Anagrafica</th>
            <th>Attivo</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($model->sottocommesse as $s): ?>
            <tr>
                <td><?= Html::encode($s->codice) ?></td>
                <td><?= Html::encode($s->descrizione) ?></td>
                <td><?= $s->data_inizio ? Html::encode(\yii\helpers\Formatter::asDate($s->data_inizio, 'dd/MM/yyyy')) : '' ?></td>
                <td><?= $s->data_fine ? Html::encode(\yii\helpers\Formatter::asDate($s->data_fine, 'dd/MM/yyyy')) : '' ?></td>
                <td><?= $s->anagrafica ? Html::encode($s->anagrafica->codice . ' - ' . $s->anagrafica->ragione_sociale) : '' ?></td>
                <td><?= $s->attivo ? 'Sì' : 'No' ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($model->sottocommesse)): ?>
            <tr><td colspan="6" class="text-center text-muted">Nessuna sottocommessa collegata</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
