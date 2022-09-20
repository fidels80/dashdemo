<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
 //use yii\grid\GridView;
 
//use app\model\Site;
use app\models\user;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
   </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'filterUrl' => ['Doc_headSearch[cd_doc]',
        //'cd_doc' => 'orc'],
        'autoXlFormat'=>true,
    'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
    
    'export'=>[
        'showConfirmAlert'=>false,
        'target'=>GridView::TARGET_BLANK
    ],

        'columns' => []
]); ?>


</div>
