<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgLotto */

$this->title = $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Lotti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mglotto-view">

    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questo lotto?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codice_lotto',
            'descrizione',
            [
                'attribute' => 'id_articolo',
                'label' => 'Articolo',
                'value' => $model->articolo ? $model->articolo->codice . ' - ' . $model->articolo->descrizione : $model->codice_articolo,
            ],
            'data_scadenza',
            'nota',
            'created_at:datetime',
        ],
    ]) ?>
</div>
