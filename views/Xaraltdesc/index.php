<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use kartik\select2\Select2;

use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $searchModel app\models\XaraltdescSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$x = Yii::$app->runAction('site/getexp');
$icon = new \thoulah\fontawesome\Icon();
$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['grid_color', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrgrid = $ris['grid_color'] ?? '';
$this->title = '';


$db = Yii::$app->db5;

// 1. Estrai le città uniche
$command = $db->createCommand("select cd_ar as id ,(cd_ar+' '+ descrizione) as 
     Name from ar");
$alst_art = $command->queryAll();



$lst_art = ArrayHelper::map($alst_art, 'id', 'Name');




$gridColumns =
    [
        [
            'label' => 'Id',
            'attribute' => 'id',
            'width' => '20%',
            'headerOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px;',
            ],
        ],


        [
            'label' => 'Codice Articolo',
            'attribute' => 'cd_ar',
            'width' => '40%',
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => $lst_art,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'descrizione'],
            'headerOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; 
                font-size: 13px;',
            ],
        ],

        [
            'attribute' => 'descrizione',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid .
                ' text-white', 'style' => 'color:black;'],
            'label' => 'Descrizione',
            // 'header' => 'Tipo Documento',
            'width' => '40%',
            'headerOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px;',
            ],


        ],
    ];




$this->title = 'Descrizione alternative articoli';
//$this->params['breadcrumbs'][] = 'Descrizione alternative articoli';
?>

<?php
$this->registerCss("

    .content {
        width: 90% !important;
        margin: 0;
        padding: 0;
    }
        

");
?>
<div class="xaraltdesc">

    <div class="row">
        <div class="col-12 text-left">
            <h2 class="titolo-header"><?= Html::encode($this->title) ?></h2>
        </div>
    </div>
    <nav>
        <p>
            <?= Html::a('Create Xaraltdesc', ['create'], ['class' => 'button-base button-lift']) ?>
        </p>
    </nav>

    <?php







    $fullExportMenu = ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'target' => ExportMenu::TARGET_BLANK,
        'exportConfig' => [
            ExportMenu::FORMAT_EXCEL_X => false,
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




    // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?php echo
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'resizableColumnsOptions' => ['resizeFromBody' => true],
        'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
        'headerRowOptions' => ['class="table-tothead"'],

        'striped' => true,
        'condensed' => true,
        'columns' => $gridColumns,
        'class' => "table",
        'toolbar' => [
            '{toggleData}',
            $fullExportMenu,
            ['content' =>
            Html::a('<i class="fas fa-redo"></i>', [''], [
                'class' => 'btn btn-outline-secondary btn-default',
                'title' => Yii::t('kvgrid', 'Reset Grid'),
                'data-pjax' => 0,
            ])],

        ],
        'panel' => [
            'type' => $ris['grid_color'],
            'heading' => '<i class="fas  fa-book"> Booking</i>',
            'headingOptions' => ['language' => 'it-It'],

        ],
        'responsive' => true,
        'resizableColumns' => true,
        'showPageSummary' => true,
        'pjax' => true,
    ]);; ?>

</div>
<div></div>