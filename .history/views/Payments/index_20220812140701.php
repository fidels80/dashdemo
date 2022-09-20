<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;


\Yii::$app->language ='it';
$listconf = ['1' => 'Pagata',
    '0' => 'Non pagata',
];

//use app\model\user;
$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['grid_color'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrgrid=$ris['grid_color'];

yii::warning($usrgrid);
//$ris;


/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-index">

    <h1><?php // Html::encode('Scadenze') ?></h1>

    <p>
        <?php 
        //Html::a('Create Payments', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
        'pdf' => [],
        'json' => [],
    ],
        'columns' => [
          //  ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'xid_testa',
            //'cd_cli',
            ['attribute'=>'Cd_PG',
        	'headerOptions' => ['class' => 'card-header bg-'.$usrgrid.' text-white'],
          'label'=>'Cod.PG'
          ],
           // 'DataScadenza',

            [
                'attribute'=>'DataScadenza',
                //	'header' => 'Profit Margin<br>(%)', 
	'headerOptions' => ['class' =>  'card-header bg-'.$usrgrid.' text-white'],
                'label' => 'Data Scad.', 
                'width'=>'200px',
                'value'=> function ($model, $key, $index, $widget) {
                    
            
                    return   date('d/m/Y',(strtotime($model->DataScadenza)));
                
            },
                'options' => [
                    'format' => 'd-m-Y',
                    ],        
                    'exportMenuStyle' => ['numberFormat' => ['formatCode' => 'd-m-Y']] ,
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => ([       
                  'attribute' => 'DataScadenza',
                  'presetDropdown' => true,
                  'convertFormat' => false,
                  'pluginOptions' => [
                    'separator' => ' - ',
                    'format' => 'DD-MM-YYYY',
                    'locale' => [
                          'format' => 'DD-MM-YYYY'
                      ],
                      'ranges'=> [
 'Oggi' => ["moment().startOf('day')", "moment().add(1,'year').startOf('day')"],
 'Ultimo anno' => ["moment().startOf('day').subtract(1,'year')", "moment().startOf('day')"],
 'Ultimo mese' => ["moment().startOf('day').subtract(29, 'days')", "moment().endOf('day')"],
 'Prossimi 30 gg' => ["moment().endOf('day')", "moment().endOf('day').add(30, 'days')"],
 'Mese in Corso' => ["moment().startOf('month')", "moment().endOf('month')"],
 'Mese Passato' => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
 'Tutto il prossimo mese' => ["moment().add(1, 'month').startOf('month')", "moment().add(1, 'month').endOf('month')"]
]
                  ],
                  'pluginEvents' => [
                    "apply.daterangepicker" => "function() { apply_filter('only_date') }",
                  ],
                ])
              ],


           // 'DataPagamento',
            [
                'attribute'=>'DataPagamento',
                	'headerOptions' => ['class' =>  'card-header bg-'.$usrgrid.' text-white'],
                'label' => 'Data Pg.', 
                'width'=>'200px',
                'value'=> function ($model, $key, $index, $widget) {
                    
            
                    return   date('d/m/Y',(strtotime($model->DataPagamento)));
                
            },
                'options' => [
                    'format' => 'DD-MM-YYYY',
                    ],        
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => ([       
                  'attribute' => 'DataPagamento',
                  'presetDropdown' => true,
                  'convertFormat' => false,
                  'pluginOptions' => [
                    'separator' => ' - ',
                    'format' => 'DD-MM-YYYY',
                    'locale' => [
                          'format' => 'DD-MM-YYYY'
                      ],
                      'ranges'=> [
 'Oggi' => ["moment().startOf('day')", "moment().add(1,'year').startOf('day')"],
 'Ultimo anno' => ["moment().startOf('day').subtract(1,'year')", "moment().startOf('day')"],
 'Ultimo mese' => ["moment().startOf('day').subtract(29, 'days')", "moment().endOf('day')"],
 'Prossimi 30 gg' => ["moment().endOf('day')", "moment().endOf('day').add(30, 'days')"],
 'Mese in Corso' => ["moment().startOf('month')", "moment().endOf('month')"],
 'Mese Passato' => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
 'Tutto il prossimo mese' => ["moment().add(1, 'month').startOf('month')", "moment().add(1, 'month').endOf('month')"]
]
                  ],
                  'pluginEvents' => [
                    "apply.daterangepicker" => "function() { apply_filter('only_date') }",
                  ],
                ])
              ],
            //'DataFattura',
            [
                'attribute'=>'DataFattura',
                	'headerOptions' => ['class' =>  'card-header bg-'.$usrgrid.' text-white'],
                'label' => 'Data FT.', 
                'width'=>'200px',
                'value'=> function ($model, $key, $index, $widget) {
                    
            
                    return   date('d/m/Y',(strtotime($model->DataFattura)));
                
            },
                'options' => [
                    'format' => 'DD-MM-YYYY',
                    ],        
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => ([       
                  'attribute' => 'DataFattura',
                  'presetDropdown' => true,
                  'convertFormat' => false,
                  'pluginOptions' => [
                    'separator' => ' - ',
                    'format' => 'DD-MM-YYYY',
                    'locale' => [
                          'format' => 'DD-MM-YYYY'
                      ],
                      'ranges'=> [
 'Oggi' => ["moment().startOf('day')", "moment().add(1,'year').startOf('day')"],
 'Ultimo anno' => ["moment().startOf('day').subtract(1,'year')", "moment().startOf('day')"],
 'Ultimo mese' => ["moment().startOf('day').subtract(29, 'days')", "moment().endOf('day')"],
 'Prossimi 30 gg' => ["moment().endOf('day')", "moment().endOf('day').add(30, 'days')"],
 'Mese in Corso' => ["moment().startOf('month')", "moment().endOf('month')"],
 'Mese Passato' => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
 'Tutto il prossimo mese' => ["moment().add(1, 'month').startOf('month')", "moment().add(1, 'month').endOf('month')"]
]
                  ],
                  'pluginEvents' => [
                    "apply.daterangepicker" => "function() { apply_filter('only_date') }",
                  ],
                ])
              ],



       
                        ['attribute'=>'NumFattura',
        	'headerOptions' => ['class' => 'card-header bg-'.$usrgrid.' text-white'],
          'label'=> 'Num. Ft'
          ],
                          ['attribute'=>'Protocollo',
        	'headerOptions' => ['class' => 'card-header bg-'.$usrgrid.' text-white'],
          'label'=> 'Protocollo'
          ],
           // 'Pagata',

            ['attribute'=>'Pagata', 
            	'headerOptions' => ['class' =>  'card-header bg-'.$usrgrid.' text-white'],
            'format' => 'raw','width'=>'50px',
            //'options' => ['width' => '10x','value'=>$model->confermato],
            'value'=> function ($model, $key, $index, $column) {
               if($model->Pagata==1){
                $html='checked';

                $html=    Html::checkbox($model['id'], true, [ 
                'id'=>$value['id'],'checked'=>true,'disabled' => true]);

               }else
               {
                $html='nochecked';
                $html=    Html::checkbox($model['id'], true, [ 
                'id'=>$model['id'],'checked'=>false,'disabled' => true]);
               }
                return  $html;
                
            },
            'filterType' => GridView::FILTER_SELECT2,
                'filter' => $listconf, 
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Pagata'],
            //'encodeLabel' => false

        
            ],

     
           
                                  ['attribute'=>   'NumEffetto',
        	'headerOptions' => ['class' =>  'card-header bg-'.$usrgrid.' text-white'],
          'label'=>    'NumEffetto'
          ],
                                ['attribute'=> 'TotEffetti',
        	'headerOptions' => ['class' =>  'card-header bg-'.$usrgrid.' text-white'],
          'label'=>  'TotEffetti',
          ],

            ['attribute'=>'ImportoV','format'=>['decimal',3],
            	'headerOptions' => ['class' =>  'card-header bg-'.$usrgrid.' text-white'],
            'pageSummary' => true]
            ,
            ['attribute'=> 'IncassoV', 'format'=>['decimal',3],
              	'headerOptions' => ['class' =>  'card-header bg-'.$usrgrid.' text-white'],
     
            'pageSummary' => true],

        ['class' => '\kartik\grid\ActionColumn',
            //  'hiddenFromExport' => true,
            'width' => '50px',
            'header' => "Dett.",
            'headerOptions' => ['class' => 'skip-export-pdf card-header bg-' . $usrgrid . ' text-white'],
            'template' => '{view}'],

    ],
        ],'responsive'=>true,
        //'hover'=>true, 
        'resizableColumns'=>true,
        
        'showPageSummary'=>true,
      //   'pjax' => true,
    ]); ?>


</div>
