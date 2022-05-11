<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
//use app\model\Site;
use app\models\doctype;
$x=Yii::$app->runAction('site/getexp');
$icon = new \thoulah\fontawesome\Icon();
yii::warning($x);
/* @var $this yii\web\View */
/* @var $searchModel app\models\Doc_headSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$listconf = ['1' => 'Confermato',
    '0' => 'Non Confermato',
];
$this->title = ' ';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="doc-head-index">

    <h1><?= Html::encode('Documenti') ?></h1>

    <p>
        <?php //Html::a('Create Doc Head', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'filterUrl' => ['Doc_headSearch[cd_doc]',
        //'cd_doc' => 'orc'],
        'autoXlFormat'=>true,
    'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
    'export'=>[
        'showConfirmAlert'=>false,
        'target'=>GridView::TARGET_BLANK
    ],
        'columns' => [
         //   ['class' => 'yii\grid\SerialColumn'],

           // 'id',
           // 'cd_doc',
           ['label'=>'Dett.Doc',
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '50px',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            // uncomment below and comment detail if you need to render via ajax
            // 'detailUrl' => Url::to(['/site/book-details']),
            'detail' => function ($model, $key, $index, $column) {
                return Yii::$app->controller->renderPartial('_expand-row', ['model' => $model]);
            },
            'headerOptions' => ['class' => 'kartik-sheet-style'] ,
            'expandOneOnly' => true
        ],

            [
                'attribute'=>'cd_doc', 
               'label' => 'Tipo Doc.', 
                'format'=>'text', 
                'width'=>'150px',
                'value' => function ($model, $key, $index, $widget) { 
                    return $model->cd_doc;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(doctype::find() 
                ->select(['cd_doc', '(cd_doc+\' \'+descrizione) as desk'])->
                    orderBy('cd_doc')->
                    asArray()->all(), 'cd_doc' , 'desk'), 
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'descrizione'],
    
    
    
    
    
            ],

            //'data',

            [
                'attribute'=>'data',
                'label' => 'Data Doc.', 
                'width'=>'200px',
                'options' => [
                    'format' => 'DD-MM-YYYY',
                    ],        
                'filterType' => GridView::FILTER_DATE_RANGE,
                'filterWidgetOptions' => ([       
                  'attribute' => 'data',
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
            ['attribute'=>'numdoc',
            'label' => 'Num. Doc.', 
           // 'header' => 'Tipo Documento'
             'width'=>'150px'],
           // 'cd_cli',
            ['attribute'=>'cd_pg',
            'label' => 'Tipo Pag.', 
            'width'=>'150px'],
                
            
            //'confermato',
            ['attribute'=>'confermato', 
            'format' => 'raw',
            'width'=>'50px',
            //'options' => ['width' => '10x','value'=>$model->confermato],
            'value'=> function ($model, $key, $index, $column) {
               if($model->confermato==1){
                $html='checked';

                $html=    Html::checkbox($model['id'], true, [ 
                'id'=>$value['id'],'checked'=>true,'disabled' => true]);
                $html='<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-square" viewBox="0 0 16 16">
                <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
                <path d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.235.235 0 0 1 .02-.022z"/>
              </svg>';
              $html=Yii::$app->fontawesome->name('check-square', 'regular')->fill('#003865');
              //$hmtl='prcod';
              //yii::warning('asda');
               }else
               {
                $html='nochecked';
                $html=    Html::checkbox($model['id'], true, [ 
                'id'=>$model['id'],'checked'=>false,'disabled' => true]);
               $html='<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-square" viewBox="0 0 16 16">
               <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
             </svg>';
             $html=Yii::$app->fontawesome->name('square', 'regular')->fill('#003865');
                    }
                return  $html;
                
            },
            'filterType' => GridView::FILTER_SELECT2,
                'filter' => $listconf, 
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'Confermato'],
            //'encodeLabel' => false

        
            ],
            'note',
            ['label' => 'Tot.Qta', 
            'vAlign' => 'middle',
            'value' => function ($model, $key, $index, $widget) { 
               // $p = compact('model', 'key', 'index');
               $t=0;
               foreach ($model->rowsall as  $value) {
                   $t=$t+$value['qta'];
               }
               
               
               return  $t;
            },'pageSummary' => true],
            ['header' => 'TotDoc', 
            'vAlign' => 'middle',
            'value' => function ($model, $key, $index, $widget) { 
                $t=0;
                foreach ($model->rowsall as  $value) {
                    $t=$t+$value['prezzo'];
                }
                return $t;
            },
            'pageSummary' => true],

            ['class' =>'yii\grid\ActionColumn','template'=>'{view}'],
        ],
     
    'panel'=>[
        'type'=>'primary',
        'heading'=>'Teste Documenti'
    ],
    'responsive'=>true,
    //'hover'=>true, 
    'resizableColumns'=>true,
    
    'showPageSummary'=>true,
     'pjax' => true,
    ]); ?>


</div>
