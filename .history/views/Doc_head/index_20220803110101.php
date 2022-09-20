<?php
use app\models\doctype;
use app\models\user;
//use yii\grid\GridView;
use kartik\grid\GridView;
//use app\model\Site;
use yii\db\Query;
use yii\helpers\ArrayHelper;
$x = Yii::$app->runAction('site/getexp');
$icon = new \thoulah\fontawesome\Icon();
//use kartik\icons\Icon;
//Icon::map($this, Icon::EL);


use yii\helpers\Html;

yii::warning($x);
/* @var $this yii\web\View */
/* @var $searchModel app\models\Doc_headSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$listconf = ['1' => 'Confermato',
    '0' => 'Non Confermato',
];
$listrif = ['1' => 'Rifiutato',
    '0' => 'Non Rifiutato',
];

$this->title = ' ';
//$this->params['breadcrumbs'][] = $this->title;

$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['grid_color', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrgrid = $ris['grid_color'];

yii::warning($usrgrid);
$eldoc = (new yii\db\Query())
    ->select(['cd_doc'])
    ->from('doc_head')
    ->where(['cd_cli' => $ris['cd_cli']])
    ->distinct()
    ->all();
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
<div class="doc-head-index">

    <h1><?=Html::encode('Documenti')?></h1>

    <p>
        <?php //Html::a('Create Doc Head', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    //'filterUrl' => ['Doc_headSearch[cd_doc]',
    //'cd_doc' => 'orc'],
    'autoXlFormat' => true,
    'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],

    'export' => [
        'showConfirmAlert' => false,
        'target' => GridView::TARGET_BLANK,
        GridView::PDF => [
        'label' => Yii::t('kvgrid', 'PDF'),
        'icon' => $isFa ? 'file-pdf-o' : 'floppy-disk',
        'iconOptions' => ['class' => 'text-danger'],
        'showHeader' => true,
        'showPageSummary' => true,
        'showFooter' => true,
        'showCaption' => true,
        'filename' => Yii::t('kvgrid', 'grid-export'),
        'alertMsg' => Yii::t('kvgrid', 'The PDF export file will be generated for download.'),
        'options' => ['title' => Yii::t('kvgrid', 'Portable Document Format')],
        'mime' => 'application/pdf',
        'config' => [
            'mode' => 'c',
            'format' => 'A4-L',
            'destination' => 'D',
            'marginTop' => 20,
            'marginBottom' => 20,
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
                    ['odd' => $pdfHeader, 'even' => $pdfHeader]
                ],
                'SetFooter' => [
                    ['odd' => $pdfFooter, 'even' => $pdfFooter]
                ],
            ],
            'options' => [
                'title' => $title,
                'subject' => Yii::t('kvgrid', 'PDF'),
                'keywords' => Yii::t('kvgrid', 'krajee, grid, export, yii2-grid, pdf')
            ],
            'contentBefore'=>'',
            'contentAfter'=>''
        ]
    ],
    ],

    'columns' => [
        //   ['class' => 'yii\grid\SerialColumn'],

        // 'id',
        // 'cd_doc',
        ['label' => 'Dett.Doc',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '50px',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            'detailRowCssClass' => 'card-header bg-' . $usrgrid . ' text-black',
            // uncomment below and comment detail if you need to render via ajax
            // 'detailUrl' => Url::to(['/site/book-details']),
            'detail' => function ($model, $key, $index, $column) {
                return Yii::$app->controller->renderPartial('_expand-row',
                    ['model' => $model]);
            },
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-black'],

            'expandOneOnly' => true,
        ],

        [
            'attribute' => 'cd_doc',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            'label' => 'Tipo Doc.',
            'format' => 'text',
            'width' => '150px',
            'value' => function ($model, $key, $index, $widget) {
                return $model->cd_doc;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => ArrayHelper::map(doctype::find()
                ->select(['cd_doc', '(cd_doc+\' \'+descrizione) as desk'])
                ->where(['cd_doc' => $eldoc])
                ->orderBy('cd_doc')->
                asArray()->all(), 'cd_doc', 'desk'),
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'descrizione'],

        ],

        //'data',

        [
            'attribute' => 'data',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            'label' => 'Data Doc.',
            'width' => '200px',
            'options' => [
                'format' => 'DD-MM-YYYY',
            ],
            'filterType' => GridView::FILTER_DATE_RANGE,
            'filterWidgetOptions' => ([
                'attribute' => 'data',
                'language' => 'it',
                'presetDropdown' => true,
                'convertFormat' => false,
                'pluginOptions' => [
                    'separator' => ' - ',
                    'language' => 'it',
                    'format' => 'DD-MM-YYYY',
                    'locale' => [
                        'format' => 'DD-MM-YYYY',
                    ],
                    'ranges' => [
                        'Oggi' => ["moment().startOf('day')", "moment().add(1,'year').startOf('day')"],
                        'Ultimo anno' => ["moment().startOf('day').subtract(1,'year')", "moment().startOf('day')"],
                        'Ultimo mese' => ["moment().startOf('day').subtract(29, 'days')", "moment().endOf('day')"],
                        'Prossimi 30 gg' => ["moment().endOf('day')", "moment().endOf('day').add(30, 'days')"],
                        'Mese in Corso' => ["moment().startOf('month')", "moment().endOf('month')"],
                        'Mese Passato' => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
                        'Tutto il prossimo mese' => ["moment().add(1, 'month').startOf('month')", "moment().add(1, 'month').endOf('month')"],
                    ],
                ],
                'pluginEvents' => [
                    "apply.daterangepicker" => "function() { apply_filter('only_date') }",
                ],
            ]),
        ],
        ['attribute' => 'numdoc',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            'label' => 'Num. Doc.',
            // 'header' => 'Tipo Documento'
            'width' => '150px'],
        // 'cd_cli',
        /*  ['attribute'=>'cd_pg',
        'label' => 'Tipo Pag.',
        'headerOptions' => ['class' => 'card-header bg-'.$usrgrid.' text-white'],
        'width'=>'150px'],*/

        //'confermato',
        ['attribute' => 'confermato',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            'format' => 'raw',
            'width' => '50px',
            //'options' => ['width' => '10x','value'=>$model->confermato],
            'value' => function ($model, $key, $index, $column) {
                if ($model->confermato == 1) {
                //    $html = Yii::$app->fontawesome->name('check-square',
                 //    'regular')->fill('#003865');

                   //  $hmtl= Icon::show('menu', ['framework' => 'custom']);

                    //$hmtl='prcod';
                    //yii::warning('asda');
                } else {
                   // $html = Yii::$app->fontawesome->name('square', 'regular')
                   // ->fill('#003865');
                    //$hmlt= Icon::show('menu', ['framework' => 'custom']);

                }
                return $html;

            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => $listconf,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Confermato'],
            'encodeLabel' => false,

        ],
        //'rifiutato',
        ['attribute' => 'rifiutato',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            'format' => 'raw',
            'width' => '50px',
            //'options' => ['width' => '10x','value'=>$model->confermato],
            'value' => function ($model, $key, $index, $column) {
                if ($model->rifiutato == 1) {
        
                 //   $html = Yii::$app->fontawesome->name('check-square', 
                  //  'regular')->fill('#003865');
                    //$hmtl='prcod';
                    //yii::warning('asda');
                } else {
                    //$html = Yii::$app->fontawesome->name('square',
                     //'regular')->fill('#003865');
                }
                return $html;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => $listrif,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Rifutato'],
            //'encodeLabel' => false
        ],

        ['label' => 'Note',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            'attribute' => 'note'],

        /*  ['label' => 'Tot.Qta',
        'headerOptions' => ['class' => 'card-header bg-'.$usrgrid.' text-white'],
        'vAlign' => 'middle',
        'value' => function ($model, $key, $index, $widget) {
        // $p = compact('model', 'key', 'index');
        $t=0;
        foreach ($model->rowsall as  $value) {
        $t=$t+$value['qta'];
        }

        return  $t;
        },'pageSummary' => true],
         */
        ['header' => 'Imponibile',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            'vAlign' => 'middle',
            'value' => function ($model, $key, $index, $widget) {
                $t = 0;
                foreach ($model->rowsall as $value) {
                    $t = $t + $value['prezzo'];
                }
                return $t;
            },
            'pageSummary' => true],
        ['header' => 'Imposta',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            'vAlign' => 'middle',
            'value' => function ($model, $key, $index, $widget) {
                $t = 0;
                // foreach ($model->rowsall as  $value) {
                //     $t=$t+$value['prezzo'];
                // }
                /*$сonnection = Yii::$app->db2;
                $command = $connection->createCommand("
                SELECT totimpostae from  DOTotali where Id_DoTes =:id_dotes
                //", [':id_dotes' => $model['xid_testa']]);
                 */
                $query =
                (new Query())->select(['totimpostae'])->from('DOTotali')
                ->where(['Id_DoTes' => $model['xid_testa']])->one(Yii::$app->db2);

//$result = $command->queryAll();
                yii::warning($query->totimpostae);

                return $query['totimpostae'];
            },
            'pageSummary' => true],

        ['class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            'template' => '{view}'],

    ],

    'panel' => [
        'type' => $ris['grid_color'],
        'heading' => '<i class="fas  fa-piggy-bank"></i> Documenti',
    ],
    'responsive' => true,
    //'hover'=>true,
    'resizableColumns' => true,

    'showPageSummary' => true,
    'pjax' => false,
]);?>


</div>
