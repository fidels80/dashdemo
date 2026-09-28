<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgAttributoArticolo */

$this->title = $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Attributi articolo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgattributo-view">

    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questo attributo?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            ['attribute' => 'tipo', 'value' => $model->tipoLabel],
            'codice',
            'descrizione',
            ['attribute' => 'attivo', 'format' => 'boolean'],
        ],
    ]) ?>
</div>
