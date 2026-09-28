<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoDocumento */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Tipi documento', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgtipodocumento-view">


    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questo tipo documento?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codice',
            'descrizione',
            [
                'attribute' => 'destinazione',
                'value' => function ($model) {
                    return $model->destinazioneLabel;
                },
            ],
            'anno',
            'contatore',
            ['attribute' => 'usa_progressivo', 'format' => 'boolean'],
            ['attribute' => 'congruita', 'format' => 'boolean', 'label' => 'Congruità numeri'],
            ['attribute' => 'crea_scadenze', 'format' => 'boolean', 'label' => 'Crea scadenze'],
            ['attribute' => 'mostra_varianti', 'format' => 'boolean', 'label' => 'Mostra taglia/colore'],
            ['attribute' => 'attivo', 'format' => 'boolean'],
            'created_at',
        ],
    ]) ?>
</div>
