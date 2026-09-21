<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgAnagrafica */

$this->title = $model->ragione_sociale;
$this->params['breadcrumbs'][] = ['label' => 'Anagrafica', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mganagrafica-view">

    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questa anagrafica?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codice',
            'ragione_sociale',
            'partita_iva',
            'codice_fiscale',
            'indirizzo',
            'cap',
            'citta',
            'provincia',
            'telefono',
            'email',
            'tipo',
            ['attribute' => 'attivo', 'format' => 'boolean'],
        ],
    ]) ?>
</div>
