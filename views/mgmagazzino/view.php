<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgMagazzino */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Magazzini', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgmagazzino-view">

    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questo magazzino?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codice',
            'descrizione',
            [
                'attribute' => 'id_anagrafica',
                'label' => 'Anagrafica',
                'value' => $model->anagraficaLabel,
            ],
            ['attribute' => 'attivo', 'format' => 'boolean'],
        ],
    ]) ?>
</div>
