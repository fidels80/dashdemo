<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
$listconf = ['1' => 'Pagata',
    '0' => 'Non pagata',
];
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
            'type'=>'primary',
            'heading'=>'Scadenze'
        ],
        'export'=>[
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK
        ],
        'columns' => [
          //  ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'xid_testa',
            //'cd_cli',
            'Cd_PG',
           // 'DataScadenza',

            [
                'attribute'=>'DataScadenza',
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
                    'format' => 'd-m-Y',
                    'locale' => [
                          'format' => 'd-m-Y'
                      ],
                  ],
                  'pluginEvents' => [
                    "apply.daterangepicker" => "function() { apply_filter('only_date') }",
                  ],
                ])
              ],


           // 'DataPagamento',
            [
                'attribute'=>'DataPagamento',
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
                  ],
                  'pluginEvents' => [
                    "apply.daterangepicker" => "function() { apply_filter('only_date') }",
                  ],
                ])
              ],
            //'DataFattura',
            [
                'attribute'=>'DataFattura',
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
                  ],
                  'pluginEvents' => [
                    "apply.daterangepicker" => "function() { apply_filter('only_date') }",
                  ],
                ])
              ],



            'NumFattura',
            'Protocollo',
           // 'Pagata',

            ['attribute'=>'Pagata', 
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

            'NumEffetto',
            'TotEffetti',
            ['attribute'=>'ImportoV',
            'pageSummary' => true]
            ,
            ['attribute'=> 'IncassoV', 'pageSummary' => true],

            ['class' =>'yii\grid\ActionColumn','template'=>'{view}'],
        ],'responsive'=>true,
        //'hover'=>true, 
        'resizableColumns'=>true,
        
        'showPageSummary'=>true,
      //   'pjax' => true,
    ]); ?>


</div>
