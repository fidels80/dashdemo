<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AgentifilesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Agentifiles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agentifiles-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Agentifiles', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'cd_agente',
            'descrizione',
            'nota',
            'cartella',
            //'cartella_padre',
            //'f_content',
            //'nome_file',
            //'estenzione',
            //'uplfile',
            //'file',
            //'kiave_arch',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
