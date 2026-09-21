<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $tipi array */
/* @var $anagrafiche array */
/* @var $filters array */

$this->title = 'Documenti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgdocumento-index">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1><?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('<i class="fas fa-list"></i> Tipi documento', ['mgtipodocumento/index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="fas fa-plus"></i> Nuovo documento', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <form method="get" action="<?= Url::to(['index']) ?>" class="card card-body mb-3">
        <div class="row">
            <div class="col-md-3">
                <?= Html::dropDownList('id_tipo', $filters['id_tipo'], $tipi, ['prompt' => 'Tipo documento...', 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-3">
                <?= Html::dropDownList('id_anagrafica', $filters['id_anagrafica'], $anagrafiche, ['prompt' => 'Cliente/Fornitore...', 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-2">
                <?= Html::textInput('anno', $filters['anno'], ['class' => 'form-control', 'placeholder' => 'Anno']) ?>
            </div>
            <div class="col-md-2">
                <?= Html::textInput('q', $filters['q'], ['class' => 'form-control', 'placeholder' => 'Cerca...']) ?>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i> Filtra</button>
            </div>
        </div>
    </form>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'codice_tipo',
                'label' => 'Tipo',
            ],
            [
                'label' => 'Documento',
                'value' => function ($model) {
                    return $model->etichetta;
                },
            ],
            [
                'attribute' => 'data',
                'value' => function ($model) {
                    return $model->data ? date('d/m/Y', strtotime($model->data)) : '';
                },
            ],
            [
                'attribute' => 'id_anagrafica',
                'value' => function ($model) {
                    return $model->anagrafica->ragione_sociale ?? '';
                },
            ],
            'descrizione',
            'stato',
            [
                'attribute' => 'totale',
                'value' => function ($model) {
                    return number_format((float) $model->totale, 2, ',', '.');
                },
                'contentOptions' => ['class' => 'text-right'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
            ],
        ],
    ]); ?>
</div>
