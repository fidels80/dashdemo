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


$fullExportMenu = ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'target' => ExportMenu::TARGET_BLANK,
    'exportConfig' => [ExportMenu::FORMAT_EXCEL_X => false,
        ExportMenu::FORMAT_EXCEL => ['label' => 'Excel'],
    ],
    'pjaxContainerId' => 'kv-pjax-container',
    'showConfirmAlert' => false,
    'exportContainer' => [
        'class' => 'btn-group mr-2 me-2',
    ],
    'dropdownOptions' => [
        'label' => 'Export',
        'class' => 'btn btn-outline-secondary btn-default',
        'itemsBefore' => [
            '<div class="dropdown-header">Esporta tutti i dati visibili</div>',
        ],
    ],
]);

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
