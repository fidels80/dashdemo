<script src="https://kit.fontawesome.com/a5ce0dfadd.js" crossorigin="anonymous"></script>

<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $searchModel app\models\XtravelheadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$x = Yii::$app->runAction('site/getexp');
$icon = new \thoulah\fontawesome\Icon();
//use kartik\icons\Icon;
//Icon::map($this, Icon::EL);
$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['grid_color', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrgrid = $ris['grid_color'] ?? '';
$this->title='';
$elecli = (new yii\db\Query())
    ->select(['cd_cli'])
    ->distinct()
    ->from('doc_head')
    ->where(['altcli' => $ris['cd_cli']])
    ->andWhere(['not', ['altcli' => null]])
    ->distinct()
    ->all();
$elecli[]['cd_cli'] = $ris['cd_cli'];

     $ints = yii::$app->db5
                        ->createCommand('select cd_cf as id,cd_cf+\'-\'+ descrizione as desk
                        from cf where cliente=1'  );
                        $clienti = $ints->queryAll() ;
$clif=ArrayHelper::map($clienti, 'id', 'desk');
//$clif = ArrayHelper::map($intesta);
 
    $ints2 = yii::$app->db5
        ->createCommand('select descrizione as id, descrizione as desk
                        from cf where cliente=1');
    $clienti2 = $ints2->queryAll();
    $clif2 = ArrayHelper::map($clienti2, 'id', 'desk');

 
$listconf = ['1' => 'SI',
    '0' => 'No',
];
     $dataProvider->key = 'th_id';
 $gridColumns= 
[
     /*  ['label' => 'Dett.Doc',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white' ,'style'=>'color:black;'],
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '2%',
            'format'=>'html',
            'detailRowCssClass'=>'',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
           'detail' => function ($model, $key, $index, $column) {
                return Yii::$app->controller->renderPartial('_expand-row',
                    ['model' => $model]);
            } ,
        //    'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-black' ],

            'expandOneOnly' => true,
            'hiddenFromExport'=>true, 
        ],*/

   

/*[
    'class' => 'kartik\grid\ExpandRowColumn',
    'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white' ,'style'=>'color:black;'],
    'width' => '2%',
    'value' => function ($model, $key, $index, $column) {
        return GridView::ROW_COLLAPSED;
    },
 
    'detailUrl' =>function ($model) {
        yii::warning('Dett.Doc: ' . $model->detailUrl, 'xtravelhead');
                return $model->detailUrl; // Usa il campo aggiunto nel controller
            },
    'expandOneOnly' => true,
    'hiddenFromExport' => true, 
],*/



        ['label'=>'Locandina',
        'format'=>'raw',
        
         'value' => function ($model) {
            $imageUrl = $model->imageFile ?? '/uploads/l_mancante.jpg';
            return Html::img($imageUrl, [
                'alt' => 'Locandina',
                'style' => 'max-width: 80px; max-height: 80px; display: block;',
            ]);
    },
        
          'width' => '2%',
        
  
],
  
    [
        'label' => 'Descrizione  Cliente',
        'attribute' => 'x_cfdesk',
        'width' => '12%',
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => $clif2,
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        'filterInputOptions' => ['placeholder' => 'descrizione'],
    ],

                ['attribute' => 'descrizione',
        'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white' ,'style'=>'color:black;'],
        'label' => 'Descrizione',
        // 'header' => 'Tipo Documento'
        'value' => function ($model) {
        // Verifica se th_id è presente, altrimenti non creare il link
        return $model->th_id ? Html::a($model->descrizione, ['xtravelhead/masterhotel', 'id' => $model->th_id], ['class' => 'link-class']) : $model->descrizione;
    },
        'width' => '12%',
     'format' => 'raw',
    
    ],
       [
        'label' => 'Totale Imponibile  Servizi',
        //'attribute' => 'totaleservizi',
        'width' => '5%',
        'value'=>function ($model) {
        // Verifica se th_id è presente, altrimenti non creare il link
        return $model->getImponibile()??0;
    },
        'format' => 'currency',
        'pageSummary' => true
    ],
    [
        'label' => 'Totale  tax',
       // 'attribute' => 'tax',
        'width' => '5%',
  'value'=>function ($model) {
        // Verifica se th_id è presente, altrimenti non creare il link
        return $model->getTax()??0;
    },
        'format' => 'currency',
        'pageSummary' => true
    ], 
         [
        'label' => 'Iva Pagata',
         'value'=>function ($model) {
        // Verifica se th_id è presente, altrimenti non creare il link
        return $model->getsomma('iva') ?? 0;
    },
        'width' => '5%',

        'format' => 'currency',
        'pageSummary' => true
    ],
    [
        'label' => 'Totale  pagato',
        
         'value'=>function ($model) {
        // Verifica se th_id è presente, altrimenti non creare il link
        $t=$model->getIva()  +$model->getTax()  +$model->getImponibile() ?? 0;
        return $t;
    },
        'format' => 'currency',
        'pageSummary' => true,
        'width' => '5%',
    ],
        [
        'label' => 'Fee',
       'value'=>function ($model) {
        // Verifica se th_id è presente, altrimenti non creare il link
        return $model->getsomma('fee') ?? 0;
    },
        'format' => 'currency',
        'pageSummary' => true,
        'width' => '5%',
    ],
        [
        'label' => 'Imponibile fattura da emettere',
                      'value'=>function ($model) {
        // Verifica se th_id è presente, altrimenti non creare il link
           return $model->getsomma('Totalegenerale') ?? 0;
    },

        'format' => 'currency',
        'pageSummary' => true,
        'width' => '5%',
    ],
    [
        'label' => 'Totale  Righe',
               'value'=>function ($model) {
        // Verifica se th_id è presente, altrimenti non creare il link
        return $model->getTravelRowsCount() ?? 0;
    },
        'width' => '3%',
    ],
            
        ['attribute' => 'numero',
        'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white' ,'style'=>'color:black;'],
        'label' => 'Num.Doc.',
 
        // 'header' => 'Tipo Documento'
        'width' => '5%'],
        ['attribute' => 'fatturato',
        'label'=>'Fat',
        //    'hiddenFromExport' => true,
        'headerOptions' => ['class' => 'card-header bg-' . $usrgrid .
         '','style'=>'color:black;'],
        'format' => 'raw',
        'width' => '5px',
        'class' => '\kartik\grid\BooleanColumn',
        'trueLabel' => 'SI',
        'falseLabel' => 'No',
        'contentOptions' => function ($model, $key, $index, $column) {
            if ($model->fatturato == 1) {
                return ['style' => 'background-color:green'];
            }
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => [1=>'SI',0=>'No'],
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        //'filterInputOptions' => ['placeholder' => 'Si'],
        //'encodeLabel' => false
        'value'=>function ($model, $key, $index, $column) {
                return $model->fatturato ?? 0;
        },
    ],

['attribute' => 'bloccato',
'label'=>'Blc',
        //    'hiddenFromExport' => true,
        'headerOptions' => ['class' => 'card-header bg-' . $usrgrid .
         '','style'=>'color:black;'],
        'format' => 'raw',
        'width' => '5px',
        'class' => '\kartik\grid\BooleanColumn',
        'trueLabel' => 'SI',
        'falseLabel' => 'No',
        'contentOptions' => function ($model, $key, $index, $column) {
            if ($model->bloccato == 1) {
                return ['style' => 'background-color:green'];
            }
        },
        'filterType' => GridView::FILTER_SELECT2,
        'filter' => [1=>'SI',0=>'No'],
        'filterWidgetOptions' => [
            'pluginOptions' => ['allowClear' => true],
        ],
        //'filterInputOptions' => ['placeholder' => 'Si'],
        //'encodeLabel' => false
        'value'=>function ($model, $key, $index, $column) {
                return $model->bloccato ?? 0;
        },
    ],

    ];
    

 


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
<div class="xtravelhead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Xtravelhead', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo Html::a('<i class="fa-solid fa-thumbtack"></i>
            Attivi', ['xtravelhead/index'], [
    'class' => 'btn btn-primary', // puoi personalizzare la classe per lo stile
]);?>    

<?php echo Html::a('Torna indietro', Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-secondary']);?>
    <?php 
    /*echo Html::a('<i class="fa-solid fa-box-archive"></i>
              Archivio', ['xtravelhead/index3'], [
    'class' => 'btn btn-primary', // puoi personalizzare la classe per lo stile
]);*/
?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  $isFa = 'file-pdf-o';



$this->registerCss("
    @media (max-width: 768px) {
        .kv-grid-table {
            font-size: 12px;
        }
        .kv-grid-table th, .kv-grid-table td {
            padding: 4px;
            white-space: nowrap;
        }
    }


}
");

echo
GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'resizableColumnsOptions' => ['resizeFromBody' => true],
    'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
    'striped' => true,
    'condensed' => true,
    'columns' => $gridColumns,
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
        /*'heading'=>'<h3 class="panel-title"><i class="fas fa-globe"></i> Countries</h3>',
    'type'=>'success',
    'before'=>Html::a('<i class="fas fa-plus"></i> Create Country', ['create'], ['class' => 'btn btn-success']),
    'after'=>Html::a('<i class="fas fa-redo"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
    'footer'=>false
     */],
    'responsive' => true,
    'resizableColumns' => true,
    'showPageSummary' => true,
    'pjax' => true,
]);
 ?>


</div>
