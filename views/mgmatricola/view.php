<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgMatricola */

$this->title = $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Matricole / Numeri di serie', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgmatricola-view">

    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questa matricola?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'matricola',
            'descrizione',
            [
                'attribute' => 'id_articolo',
                'label' => 'Articolo',
                'value' => $model->articoloLabel,
            ],
            'nota',
            ['attribute' => 'attivo', 'format' => 'boolean'],
            'created_at:datetime',
        ],
    ]) ?>
</div>
