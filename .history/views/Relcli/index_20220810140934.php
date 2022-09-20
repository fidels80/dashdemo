<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RelcliSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Relclis';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="relcli-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Relcli', ['create'], ['class' => 'btn btn-success']) ?>
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
