<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UecarticoliSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Articoli';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uecarticoli-index">


    <p>
        <?= Html::a('Crea Articoli', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'codice',
            'descrizione',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
