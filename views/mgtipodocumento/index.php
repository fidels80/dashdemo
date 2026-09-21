<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipi documento';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgtipodocumento-index">


    <p>
        <?= Html::a('<i class="fas fa-plus"></i> Nuovo tipo documento', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'codice',
            'descrizione',
            'anno',
            'contatore',
            [
                'attribute' => 'usa_progressivo',
                'format' => 'boolean',
            ],
            [
                'attribute' => 'congruita',
                'format' => 'boolean',
                'label' => 'Congruità numeri',
            ],
            [
                'attribute' => 'attivo',
                'format' => 'boolean',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>
</div>
