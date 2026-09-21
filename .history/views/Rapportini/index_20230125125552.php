<?php

use yii\helpers\Html;

use yii\widgets\Pjax;

use app\models\user;
//use app\model\Site;
use kartik\grid\GridView;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use kartik\export\ExportMenu;


/* @var $this yii\web\View */
/* @var $searchModel app\models\RapportiniSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rapportini';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rapportini-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Inserisci  Rapportino', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'cf',
            'commessa',
            'qta',
            'data',
            //'ora_in',
            //'ora_out',
            //'numero',
            //'userid',
            //'note',
            'cd_art',
            'des_art',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
