<script src="https://kit.fontawesome.com/a5ce0dfadd.js" crossorigin="anonymous"></script>
<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TodomainSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use kartik\grid\GridView;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use app\models\Todopriorita;
use app\models\Todostato;
use app\models\Anacli;
use app\models\Todocommenti;


$this->title = Yii::t('app', 'Todomains');
$this->params['breadcrumbs'][] = $this->title;


$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['grid_color', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrgrid = $ris['grid_color'];


?>
<div class="todomain-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Todomain'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>


     <?php
        /* GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user',
            'group',
            'cd_cli',
            'priorita',
            //'progresso',
            //'id_padre',
            //'descrizione',
            //'data_inizio',
            //'data_fine',
            //'data_scadenza',
            //'stato',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); 
    
    */
        $gridColumns =
            [
             [
                'label' => 'Vedi',
                //   'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white' ,'style'=>'color:black;'],
                'class' => 'kartik\grid\ExpandRowColumn',
                'width' => '2%',
                'format' => 'html',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    ob_start();
                    include_once('_comments.php');
                    $comments = Todocommenti::find()->where(['id_todo' => $model->id])->all();
                    $newCommentModel = new Todocommenti();
                    echo '<div class="custom-detail-row-class">' . renderComments($comments, null, $newCommentModel) . '</div>';
                    return ob_get_clean();
                },
                //'headerOptions' => ['style'=>'' ],
                // 'contentOptrions' => ['style'=>'' ],
                'expandOneOnly' => true,
                'hiddenFromExport' => True,
                'detailRowCssClass' => 'card-header bg-' . $usrgrid . ' text-black',
                //   'detailRowOptions' => ['class' => 'custom-detail-row-class'],
                //  'contentOptions' => ['style' => 'background-color: #ffffff; color: #ffffff;'],
            ], 
            [
                'attribute' => 'stato',
                'visible' => true,
                'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white', 'style' => 'color:black;'],
                'label' => 'Stato',
                'format' => 'text',
                'width' => '5%',
                'value' => function ($model, $key, $index, $widget) {
                    $ris2 = Todostato::find()->where(['id' => $model->stato])->one();
                    return $ris2->stato ?? null;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Todostato::find()
                ->select(['id', 'stato as desk'])
                //->where(['' => $eldoc])
                ->orderBy('id')->asArray()->all(), 'id', 'desk'),

                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'descrizione'],

            ],
            [
                'attribute' => 'priorita',
                'visible' => true,
                'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white', 'style' => 'color:black;'],
                'label' => 'Priorita',
                'format' => 'text',
                'width' => '5%',
                'value' => function ($model, $key, $index, $widget) {
                    $ris2 = Todopriorita::find()->where(['id' => $model->priorita])->one();
                    return $ris2->priorita ?? null;
 
                },
                 'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Todopriorita::find()
                ->select(['id', 'priorita as desk'])
                //->where(['' => $eldoc])
                ->orderBy('id')->asArray()->all(), 'id', 'desk'),
            
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'descrizione'],
            ],
            [
                'attribute' => 'progresso',
                'visible' => true,
                'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white', 'style' => 'color:black;'],
                'label' => 'Progresso',
                'format' => 'text',
                'width' => '5%',
                'value' => function ($model, $key, $index, $widget) {
                   // $ris2 = Todopriorita::find()->where(['id' => $model->priorita])->one();
                    $ris= ($model->progresso ?? null);
                    if ($ris !== null) {
                        return $ris . '%';
                    }

                    return $ris;

                },
             //   'filterType' => GridView::FILTER_SELECT2,
             //   'filter' => ArrayHelper::map(Todopriorita::find()
             //       ->select(['id', 'priorita as desk'])
                    //->where(['' => $eldoc])
             //       ->orderBy('id')->asArray()->all(), 'id', 'desk'),

                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'percentuale'],

            ],

            [
                'attribute' => 'cd_cli',
                'visible' => true,
                'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white', 'style' => 'color:black;'],
                'label' => 'Cliente',
                'format' => 'text',
                'width' => '15%',
                'value' => function ($model, $key, $index, $widget) {
                    $ris2 = Anacli::find()->where(['cd_cli' => $model->cd_cli])->one();
                    return $ris2->Desk ?? null;
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => ArrayHelper::map(Anacli::find()
                ->select(['cd_cli as id', 'Desk as desk'])
                //->where(['' => $eldoc])
                ->orderBy('id')->asArray()->all(), 'id', 'desk'),

                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'descrizione'],

            ],
            [
                'attribute' => 'descrizione',
                'visible' => true,
                'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white', 'style' => 'color:black;'],
                'label' => 'Descrizione',
                'format' => 'text',
                'width' => '25%',
                'value' => function ($model, $key, $index, $widget) {
                    return $model->descrizione;
                },
                // 'filterType' => GridView::FILTER_SELECT2,
                /*'filter' => ArrayHelper::map(doctype::find()
                ->select(['cd_doc', '(cd_doc+\' \'+descrizione) as desk'])
                ->where(['cd_doc' => $eldoc])
                ->orderBy('cd_doc')->asArray()->all(), 'cd_doc', 'desk'),
            */
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => 'descrizione'],

            ],
            [
                'attribute' => 'data_inizio',
                'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white', 'style' => 'color:black;'],
                'label' => 'Data inizio',
                'width' => '10%',
                // 'language'=>'it-It',

                'exportMenuStyle' => ['numberFormat' => ['formatCode' => 'DD-MM-YYYY']],
                'value' => function ($model, $key, $index, $widget) {
                    if (empty($model->data_inizio)) {
                        return ''; // Restituisce una stringa vuota se data_inizio è null o vuota
                    }
                    return date('d/m/Y', (strtotime($model->data_inizio)));
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
            [
                'attribute' => 'data_fine',
                'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white', 'style' => 'color:black;'],
                'label' => 'Data Fine',
                'width' => '10%',
                // 'language'=>'it-It',

                'exportMenuStyle' => ['numberFormat' => ['formatCode' => 'DD-MM-YYYY']],
                'value' => function ($model, $key, $index, $widget) {
                    if (empty($model->data_fine)) {
                        return ''; // Restituisce una stringa vuota se data_inizio è null o vuota
                    }

                    return date('d/m/Y', (strtotime($model->data_fine))) ?? null;
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


            [
                'attribute' => 'data_scadenza',
                'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white', 'style' => 'color:black;'],
                'label' => 'Data Scadenza',
                'width' => '10%',
                // 'language'=>'it-It',

                'exportMenuStyle' => ['numberFormat' => ['formatCode' => 'DD-MM-YYYY']],
                'value' => function ($model, $key, $index, $widget) {

                    if (empty($model->data_scadenza)) {
                        return ''; // Restituisce una stringa vuota se data_inizio è null o vuota
                    }
                    return date('d/m/Y', (strtotime($model->data_scadenza)));
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
                
                [
                    'class' => '\kartik\grid\ActionColumn',
                    'hiddenFromExport' => True,
                    'headerOptions' => ['class' => 'skip-export-pdf card-header bg-' . $usrgrid . '
             ', 'style' => 'color:black;'],
                    'header' => "Modi.",
                    'template' => ' {myaction}',
                    'buttons' => [
                        'myaction' => function ($url, $model, $key) {
                            //here create glyphicon with URL pointing to your action where you can download file, something like 

                            return $model->id ? Html::a('<i class="fa-regular fa-pen-to-square ' . ('fa-beat') . '"></i>', ['todomain/update', 'id' =>
                            $model->id]) : null;
                        }
                    ]
                ],
            [
                'class' => '\kartik\grid\ActionColumn',
                'hiddenFromExport' => True,
                'headerOptions' => ['class' => 'skip-export-pdf card-header bg-' . $usrgrid . '
             ', 'style' => 'color:black;'],
                'header' => "Vedi.",
                'template' => ' {myaction}',
                'buttons' => [
                    'myaction' => function ($url, $model, $key) {
                        //here create glyphicon with URL pointing to your action where you can download file, something like 

                        return $model->id ? Html::a('<i class="fa-regular   fa-regular fa-eye  ' . ('fa-beat') . '"></i>', ['todomain/view', 'id' =>
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
                'heading' => '<i class="fas  fa-book"> Documenti</i>',
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
        ]);

        ?> <?php Pjax::end(); ?> </div>