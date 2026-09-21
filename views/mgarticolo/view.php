<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgArticolo */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Articoli', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgarticolo-view">

    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questo articolo?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codice',
            'descrizione',
            'um',
            ['attribute' => 'prezzo', 'value' => number_format((float) $model->prezzo, 4, ',', '.')],
            ['attribute' => 'iva', 'value' => number_format((float) $model->iva, 2, ',', '.')],
            ['attribute' => 'attivo', 'format' => 'boolean'],
        ],
    ]) ?>
</div>
