<script src="https://kit.fontawesome.com/a5ce0dfadd.js" crossorigin="anonymous"></script>
<?php

use yii\helpers\Html;
    use kartik\export\ExportMenu;
    use kartik\grid\GridView;
use app\models\Uecanagrafica;
use yii\helpers\ArrayHelper;
    /* @var $this yii\web\View */
    /* @var $searchModel app\models\UectestaSearch */
    /* @var $dataProvider yii\data\ActiveDataProvider */
    $listconf = [
        '1' => 'Confermato',
        '0' => 'Non Confermato',
    ];
    $listrif = [
        '1' => 'Rifiutato',
        '0' => 'Non Rifiutato',
    ];

    $tipopag = ['Carta' => 'Carta', 'Contanti' => 'Contanti', 'Assegno' => 'Assegno', 'Bancomat' => 'Bancomat'];


    
    $usrid = Yii::$app->user->Id;
    if ($usrid !== null) {
        $ris = (new \yii\db\Query())
            ->select(['grid_color', 'cd_cli'])
            ->from('user')
            ->where(['id' => $usrid])
            ->one();
    }
    $usrgrid = $ris['grid_color'];
$clif = ArrayHelper::map(
    Uecanagrafica::find()
        ->select(['id', '(nome+\' \'+cognome) as desk'])
       // ->where(['IN', 'cd_cli', $elecli])
        ->orderBy('id')->asArray()->all(),
    'id',
    'desk'
);



$this->title = 'Documenti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uectesta-index">

    

    <p>
        <?= Html::a('Crea Documento', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php 
    
    $gridColumns= 
[


        
         ['attribute' => 'numero',
        'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white' ,'style'=>'color:black;'],
        'label' => 'Num.Doc.',
        // 'header' => 'Tipo Documento'
        'width' => '5%'],

        [
            'attribute' => 'data',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white' ,'style'=>'color:black;'],
            'label' => 'Data Doc.',
            'width' => '10%',
           // 'language'=>'it-It',

 'exportMenuStyle' => ['numberFormat' => ['formatCode' => 'DD-MM-YYYY']],
'value' => function ($model, $key, $index, $widget) {

                return date('d/m/Y', (strtotime($model->data)));

            },
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
     
        ['attribute' => 'cliente',
            'label' => 'Intestatario',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white','style'=>'color:black;'],
            'format' => 'text',
            'width' => '15%',
            'visible' => true,
            'value' => function ($model, $key, $index, $widget) {

                $ris2 = Uecanagrafica::find()->where(['id' => $model->cliente])->one();
                return ($ris2->nome.' '.$ris2->cognome) ?? null;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' =>$clif
           
              
                ,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'descrizione'],
        ],
        [
            'attribute' => 'tipopag',
            'label' => 'Tipo Pagamento',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white', 'style' => 'color:black;'],
            'format' => 'text',
            'width' => '15%',
            'visible' => true,
             
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => $tipopag,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'descrizione'],
        ],

      
        ['attribute' => 'esportato',
            //  'hiddenFromExport' => true,
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white','style'=>'color:black;'],
            'format' => 'raw',
            'width' => '5%',
            //'options' => ['width' => '10x','value'=>$model->confermato],
            //   'class' => '\kartik\grid\CheckboxColumn',
            'class' => '\kartik\grid\BooleanColumn',
            'trueLabel' => 'SI',
            'falseLabel' => 'No',
            'contentOptions' => function ($model, $key, $index, $column) {
                if ($model->esportato == 1) {
                    return ['style' => 'background-color:green'];
                }

            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' => $listconf,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'Esportato'],
            'encodeLabel' => false,
            'value'=>function ($model, $key, $index, $column) {
                    return $model->esportato ?? 0;


            },

        ],

        [
            'class' => '\kartik\grid\ActionColumn',
            //  'hiddenFromExport' => true,

            'width' => '4%',
            'header' => "Dett.",
            'headerOptions' => ['class' => 'skip-export-pdf card-header bg-' . $usrgrid . '
             ', 'style' => 'color:black;'],
            'template' => ' {myaction}',
            'buttons' => [
                'myaction' => function ($url, $model, $key) {
                    //here create glyphicon with URL pointing to your action where you can download file, something like 
                    return $model->id ? Html::a('<i class="fa-regular fa-eye '
                    .  '"></i>', ['uectesta/view', 'id' =>
                    $model->id]) : null;
                }
            ]

        ], 
    ];



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






    ?>


























    <?php 
    $isFa = 'file-pdf-o';
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
            ]),],

        ],
        'panel' => [
            'type' => $ris['grid_color'],
            //'heading' => '<i class="fas  fa-book"> Documenti</i>',
            'headingOptions' => ['language' => 'it-It'],
            /*'heading'=>'<h3 class="panel-title"><i class="fas fa-globe"></i> Countries</h3>',
        'type'=>'success',
        'before'=>Html::a('<i class="fas fa-plus"></i> Create Country', ['create'], ['class' => 'btn btn-success']),
        'after'=>Html::a('<i class="fas fa-redo"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
        'footer'=>false
     */
        ],
        'containerOptions' => [
            'style' => 'background-color: #ffffff;', // Imposta lo sfondo bianco
        ],
        'responsive' => true,
        'resizableColumns' => true,
        'showPageSummary' => true,
        'pjax' => true,
    ]); ?>


</div>
