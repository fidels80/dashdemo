<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ElemailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
use app\models\user;
//use app\model\Site;
use kartik\grid\GridView;
use yii\db\Query;
use yii\helpers\ArrayHelper;
$x = Yii::$app->runAction('site/getexp');

$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['grid_color'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrgrid=$ris['grid_color'];

$icon = new \thoulah\fontawesome\Icon();
$this->title = Yii::t('app', 'ELENCO EMAIL INVIATE DAL PORTALE');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="elemail-index">

     

    <p>
       
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'autoXlFormat'=>true,
        'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
        'resizableColumnsOptions' => ['resizeFromBody' => true],
        'persistResize' => true,
        'resizeStorageKey'=>Yii::$app->user->id . '-' . date('m').'sc',
        'panel'=>[
            'type'=>$usrgrid,
            'heading'=>'<i class="fas  fa-piggy-bank"></i> Scadenze'
        ],
        'export'=>[
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK,
           // 'fontAwesome' => true
        ],   'exportConfig' => [
        'html' => [],
        'csv' => [],
        'txt' => [],
        'xls' => [],
        'pdf' => [[
            'label' => Yii::t('kvgrid', 'PDF'),
            'icon' => $isFa ? 'file-pdf-o' : 'floppy-disk',
            'iconOptions' => ['class' => 'text-danger'],
            'showHeader' => false,
            'showPageSummary' => true,
            'showFooter' => false,
            'showCaption' => true,
            'filename' => Yii::t('kvgrid', 'Portal pdf export'),
            'alertMsg' => Yii::t('kvgrid', 'The PDF export file will be generated for download.'),
            'options' => ['title' =>'Portable Document Format'],
            'mime' => 'application/pdf',
            'config' => [
                'mode' => 'c',
                'format' => 'A4-L',
                'destination' => 'D',
                'marginTop' => 20,
                'marginBottom' => 20,
                'cssFile' => 'https: //use.fontawesome.com/releases/v5.3.1/css/all.css',
                'cssInline' => '.kv-wrap{padding:20px;}' .
                '.kv-align-center{text-align:center;}' .
                '.kv-align-left{text-align:left;}' .
                '.kv-align-right{text-align:right;}' .
                '.kv-align-top{vertical-align:top!important;}' .
                '.kv-align-bottom{vertical-align:bottom!important;}' .
                '.kv-align-middle{vertical-align:middle!important;}' .
                '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}',
                'methods' => [
                   'SetHeader' => [
                        ['odd' => '$pdfHeader', 'even' => 'even'],
                    ],
                   'SetFooter' => [
                        ['odd' => '$pdfFooter', 'even' => 'evenft'],
                    ],
                ],
                'options' => [
                    'title' => 'scadenze',
                    'subject' => Yii::t('kvgrid', 'PDF'),
                    'keywords' => Yii::t('kvgrid', 'krajee, grid, export, yii2-grid, pdf'),
                ],
                'contentBefore' => 'Creato da Portale',
                'contentAfter' => 'sssssssssssssss',
                'SetHeader' => ['Krajee Privacy Policy||Generated On: ' . date("r")],
            ],
        ],],
        'json' => [],
    ],
     
         
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],

            'id',
             
            ['attribute'=>'nome',
        	'headerOptions' => ['class' => 'bg-'.$usrgrid.' text-black'],
          'label'=>'nome'
        ],
            
            ['attribute'=>'email',
        	'headerOptions' => ['class' => 'bg-'.$usrgrid.' text-black'],
          'label'=>'email'
        ]

            
            ['attribute'=>'Soggetto',
        	'headerOptions' => ['class' => 'bg-'.$usrgrid.' text-black'],
          'label'=>'Soggetto'
        ]
            'Corpo',
            //'allegati',

            [ 'class' => '\kartik\grid\ActionColumn',
            //  'hiddenFromExport' => true,
            'width' => '50px',
            'header' => "Dett.",
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-black'],
            'template' => '{view}'  ],
        ],'responsive'=>true,
        //'hover'=>true, 
        'resizableColumns'=>true,
        
        'showPageSummary'=>true,
      //   'pjax' => true,
    ]); ?>

    <?php Pjax::end(); ?>

</div>
