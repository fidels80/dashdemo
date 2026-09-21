<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DocommessaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Docommessas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="docommessa-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Docommessa', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'Id_DOCommessa',
            'Cd_DOCommessa',
            'Descrizione',
            'DescrizioneBreve',
            'Cd_CF',
            //'Cd_DOCommessaStato',
            //'DataInizio',
            //'DataFinePresunta',
            //'DataFineReale',
            //'NoteDoCommessa',
            //'UserIns',
            //'UserUpd',
            //'TimeIns',
            //'TimeUpd',
            //'Ts',
            //'NoteXML',
            //'Attributi',
            //'Sconto',
            //'Provvigione',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
