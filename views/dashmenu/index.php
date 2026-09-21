<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gestione Menu';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashmenu-index">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <?= Html::a('<i class="fas fa-user-check"></i> Assegna agli utenti', ['assegna'], ['class' => 'btn btn-outline-primary']) ?>
            <?= Html::a('<i class="fas fa-plus"></i> Nuova voce', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="alert alert-secondary">
        <i class="fas fa-info-circle"></i>
        Le voci con <strong>genitore</strong> diventano <strong>sottovoci</strong> di menu.
        Il livello <strong>100</strong> vede sempre tutte le voci e non può essere modificato.
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'attribute' => 'label',
                'value' => function ($m) {
                    return $m->genitore_id ? '— ' . $m->label : $m->label;
                },
            ],
            'codice',
            'icona',
            'url',
            [
                'attribute' => 'genitore_id',
                'label' => 'Genitore',
                'value' => function ($m) {
                    return $m->genitore->label ?? '(radice)';
                },
            ],
            'livello_min',
            'ordine',
            ['attribute' => 'per_tutti', 'format' => 'boolean'],
            ['attribute' => 'attivo', 'format' => 'boolean'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
