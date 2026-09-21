<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model app\models\Dosottocommessa */

$this->title = "Dettaglio Sottocommessa: " . $model->Cd_DOSottoCommessa;
\yii\web\YiiAsset::register($this);
?>
<style>
    .content {
        width: 95%;
    }

    .detail-view th {
        width: 25%;
        background-color: #f8f9fa;
    }
</style>
<br>
<div class="dosottocommessa-view">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?= Html::encode($this->title) ?></h1>
        <p>
            <?php $url = Url::to(['dosottocommessa/index']);


            echo Html::button('<i class="fa fa-arrow-left"></i> Torna indietro', [
                'class' => 'button-base-support button-lift',
                'style' => 'background-color: #6c757d !important;',
                'onclick' => 'window.location.href = "' . $url . '"'
            ]);
            ?>

            <?php


            $url = Url::to(['dosottocommessa/update', 'id' => $model->Id_DOSottoCommessa]);


            echo Html::button('<i class="fas fa-pencil-alt"></i> Modifica', [
                'class' => 'button-base-support button-lift',
                'style' => 'background-color: #6c757d !important;',
                'onclick' => 'window.location.href = "' . $url . '"'
            ]);

            ?>  

                <?php   

    $url = Url::to(['dosottocommessa/delete', 'id' => $model->Id_DOSottoCommessa]);


   /* echo Html::button(' <i class="fas fa-trash"></i> Cancella', [
        'class' => 'btn btn-danger button-lift',
        'style' => 'background-color: #f50505 !important;',
        'onclick' => 'window.location.href = "' . $url . '"'
    ]);*/
                
                
                ?>

        </p>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'Id_DOSottoCommessa',
                'label' => 'ID Sistema',
            ],
            'Cd_DOCommessa',
            'Cd_DOSottoCommessa',
            'Descrizione',
            'DescrizioneBreve',
            // DECODIFICA CLIENTE
            [
                'attribute' => 'Cd_CF',
                'label' => 'Cliente',
                'value' => $model->getDescrizioneCliente(),
            ],
            // DECODIFICA STATO con Badge
            [
                'attribute' => 'Cd_DOCommessaStato',
                'label' => 'Stato Sottocommessa',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<span class="badge badge-info">' . Html::encode($model->getDescrizioneStato()) . '</span>';
                },
            ],
            // FORMATTAZIONE DATE ITALIANE
            [
                'attribute' => 'DataInizio',
                'value' => $model->DataInizio ? date('d/m/Y', strtotime($model->DataInizio)) : '-',
            ],
            [
                'attribute' => 'DataFinePresunta',
                'value' => $model->DataFinePresunta ? date('d/m/Y', strtotime($model->DataFinePresunta)) : '-',
            ],
            [
                'attribute' => 'DataFineReale',
                'value' => $model->DataFineReale ? date('d/m/Y', strtotime($model->DataFineReale)) : '-',
            ],
            [
                'attribute' => 'NoteDoSottoCommessa',
                'format' => 'ntext', // Mantiene gli a capo della textarea
            ],
            'Provvigione',
        ],
    ]) ?>

</div>