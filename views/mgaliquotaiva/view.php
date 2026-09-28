<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgAliquotaIva */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Aliquote IVA', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgaliquotaiva-view">

    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questa aliquota IVA?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codice',
            'descrizione',
            ['attribute' => 'percentuale', 'value' => number_format((float) $model->percentuale, 2, ',', '.') . ' %'],
            ['attribute' => 'attivo', 'format' => 'boolean'],
        ],
    ]) ?>
</div>
