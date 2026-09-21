<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Mansioni */

 

\yii\web\YiiAsset::register($this);
?>

<div class="mansioni-view card shadow-sm p-4">

    <div class="d-flex justify-content-between align-items-center border-bottom mb-4 pb-3">
        <h2 class="m-0 text-primary">
            <i class="fas fa-briefcase"></i> <?= Html::encode('Dettaglio Mansione: ' . $model->codice) ?>
        </h2>
        <div>
            <?= Html::a('<i class="fas fa-arrow-left"></i> Torna all\'elenco', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="mb-4 d-flex gap-2">
        <?= Html::a('<i class="fas fa-edit"></i> Modifica', ['update', 'id' => $model->codice], [
            'class' => 'btn btn-primary px-4'
        ]) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->codice], [
            'class' => 'btn btn-danger px-4',
            'data' => [
                'confirm' => 'Sei sicuro di voler eliminare definitivamente questa mansione?',
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <div class="table-responsive">
        <?= DetailView::widget([
            'model' => $model,
            'options' => ['class' => 'table table-striped table-bordered detail-view align-middle'],
            'attributes' => [
                [
                    'attribute' => 'codice',
                    'label' => 'Codice Identificativo',
                    'captionOptions' => ['style' => 'width: 20%; font-weight: bold;', 'class' => 'bg-light'],
                    'value' => function($model) {
                        return '<span class="badge bg-dark px-3 py-2" style="font-size: 14px;">' . Html::encode($model->codice) . '</span>';
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'descrizione',
                    'label' => 'Descrizione Mansione',
                    'captionOptions' => ['style' => 'width: 20%; font-weight: bold;', 'class' => 'bg-light'],
                    'value' => function($model) {
                        return Html::encode($model->descrizione ?: 'Nessuna descrizione inserita');
                    },
                ],
            ],
        ]) ?>
    </div>

</div>

<style>
    /* Miglioramento visuale per la DetailView */
    .detail-view th {
        vertical-align: middle;
        color: #495057;
    }
    .detail-view td {
        font-size: 15px;
        color: #212529;
    }
    .card {
        border: none;
        border-radius: 8px;
    }
    .section-title {
        font-weight: 700;
        letter-spacing: -0.5px;
    }
</style>