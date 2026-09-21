<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\grid\GridView;
$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['grid_color'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrgrid = $ris['grid_color'];

/* @var $this yii\web\View */
/* @var $searchModel app\models\ItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="items-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php  //Html::a('Create Items', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],

            //'codice',
            ['attribute'=>'codice', 
            'headerOptions' => ['class' => 'card-header bg-'.$usrgrid.' text-white'],
            'label'=>'Codice'],

           
              ['attribute'=>'descrizione', 
            'headerOptions' => ['class' => 'card-header bg-'.$usrgrid.' text-white'],
            'label'=>'descrizione'],
        
        /*    ['attribute'=>'nota', 
            'headerOptions' => ['class' => 'card-header bg-'.$usrgrid.' text-white'],
            'label'=>'Nota'],
            //'id',
*/
            ['class' =>'yii\grid\ActionColumn',
            
             'headerOptions' => ['class' => 'card-header bg-'.$usrgrid.' text-white'],
            'template'=>'{view}',],
        ],'panel'=>[
            'type'=>$ris['grid_color'],
            'heading'=>'<i class="fas  fa-store"></i> Articoli'
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
