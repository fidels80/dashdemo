<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\MgSottocommessa */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Sottocommesse', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgsottocommessa-view">

    <p>
        <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Eliminare questa sottocommessa?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'codice',
            'descrizione',
            [
                'attribute' => 'id_commessa',
                'label' => 'Commessa',
                'value' => $model->commessa ? $model->commessa->codice . ' - ' . $model->commessa->descrizione : '—',
            ],
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
</div>
