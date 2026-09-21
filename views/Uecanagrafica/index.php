<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UecanagraficaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Anagrafiche Clienti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uecanagrafica-index">

   
    <p>
        <?= Html::a('Crea Anagrafica', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'nome',
            'cognome',
            'indirizzo',
            'citta',
            //'provincia',
            //'nazione',
            //'codicefiscale',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
