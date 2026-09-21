<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Articoli';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgarticolo-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-plus"></i> Nuovo articolo', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fas fa-list"></i> Documenti', ['mgdocumento/index'], ['class' => 'btn btn-outline-secondary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'codice',
            'descrizione',
            'um',
            ['attribute' => 'prezzo', 'value' => function ($m) { return number_format((float) $m->prezzo, 4, ',', '.'); }, 'contentOptions' => ['class' => 'text-right']],
            ['attribute' => 'iva', 'value' => function ($m) { return number_format((float) $m->iva, 2, ',', '.'); }, 'contentOptions' => ['class' => 'text-right']],
            ['attribute' => 'attivo', 'format' => 'boolean'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
