<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\GacsottoprvSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gacsottoprvs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gacsottoprv-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Gacsottoprv', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_sub_prv',
            'descrizione',
            'id_prv',
            'note:ntext',
            'tipologia',
            //'sottocommessa',
            //'datacreazione',
            //'inizioval',
            //'fineval',
            //'probacq',
            //'provvigione',
            //'apertura',
            //'chiusura',
            //'apertura_pianificata',
            //'chiusura_pianificata',
            //'stato',
            //'datastato',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
