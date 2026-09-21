<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\XroomlistSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Nominativi';
$this->params['breadcrumbs'][] = null;
?>
<div class="xroomlist-index">

   

    <p>
        <?= Html::a('Create Xroomlist', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_guest',
            'nominativo',
            'cd_ar',
            'th_id',
            'note',
            //'evaso:boolean',
            //'ruolo',
            //'party',
            //'commessa',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
