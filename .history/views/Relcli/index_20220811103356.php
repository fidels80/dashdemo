<?php

use yii\helpers\Html;
use kartik\grid\GridView;


/* @var $this yii\web\View */
/* @var $searchModel app\models\RelcliSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Relazioni Clienti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="relcli-index">

    <h1> </h1>

    <p>
        <?= Html::a('Crea Relazioni', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'cd_cli',
            'altcli',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
