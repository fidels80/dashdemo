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
use app\models\anacli;
use app\models\dosottocommessa;



/* @var $this yii\web\View */
/* @var $searchModel app\models\RapportiniSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rapportini';
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

//yii::warning($usrgrid);
$eldoc = (new yii\db\Query())
    ->select(['cd_doc'])

    ->from('doc_head')
    ->where(['cd_cli' => $ris['cd_cli']])
    ->orwhere(['altcli' => $ris['cd_cli']])
    ->distinct()
    ->all();

$elecli = (new yii\db\Query())
    ->select(['cd_cli'])
    ->distinct()
    ->from('doc_head')
    ->where(['altcli' => $ris['cd_cli']])
    ->andWhere(['not', ['altcli' => null]])
    ->distinct()
    ->all();
$elecli[]['cd_cli'] = $ris['cd_cli'];
//yii::error($elecli);
//yii::error($eldoc);
$clif = ArrayHelper::map(
    anacli::find()
        ->select(['cd_cli', '(cd_cli+\' \'+Desk) as desk'])
        ->where(['IN', 'cd_cli', $elecli])
        ->orderBy('cd_cli')->
        asArray()->all()
    , 'cd_cli', 'desk');

$sottoc= ArrayHelper::map(
dosottocommessa::find()
->select(['Cd_DOSottoCommessa as id','Descrizione'])
->orderBy('cd_DoSottoCommessa')->asArray()->all(),'id','Descrizione'

);

$tmpid = 3;

Modal::begin([
    //'header'=>'<h4>Clienti</h4>',
    'id' => 'cli' . $tmpid,
    'size' => 'modal-lg', //classe bootstrap
]);
echo "<div id='modalContent'></div>";
Modal::end();
$this->registerJs("
  $('#modalcli_$tmpid').click(function (){
  $('#cli$tmpid').modal('show')
  .find('#modalContent')
  .load($(this).attr('value'));
  });"
);
$url = Url::to(['create', 
//   'mod'->$model
]);



    //if ($usr_ris['gruppo'] != 'users') {
echo '<tr><td width="30%">';
echo Html::button('Aggiungi Nota', ['value' => $url,
    'class' => 'btn btn-info', 'id' => 'modalcli_' . $tmpid]);
echo '</td> ';
    //}

$gridColumns= 
[
['attribute'=>'commessa',
 'label' => 'Commessa',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white','style'=>'color:black;'],
            'format' => 'text',
            'width' => '15%',
            'visible' => true,
            'value' => function ($model, $key, $index, $widget) {
             $ris2 = Dosottocommessa::find()
             ->where(['cd_DoSottoCommessa' => $model->commessa])->one();
             return $ris2->Desk ?? null;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' =>$sottoc     ,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'descrizione'],
],
  ['attribute' => 'cd_cli',
            'label' => 'Intestatario',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white','style'=>'color:black;'],
            'format' => 'text',
            'width' => '15%',
            'visible' => true,
            'value' => function ($model, $key, $index, $widget) {
               $ris2 = anacli::find()->where(['cd_cli' => $model->cd_cli])->one();
                return $ris2->Desk ?? null;
            },
            'filterType' => GridView::FILTER_SELECT2,
            'filter' =>$clif ,
            'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
            ],
            'filterInputOptions' => ['placeholder' => 'descrizione'],
        ],
        
['attribute'=>'data',
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

['attribute'=>'numero'],
['attribute'=>'ora_in'],

['attribute'=>'ora_out'],

['attribute'=>'note'],     
               ['class' => '\kartik\grid\ActionColumn',
            //  'hiddenFromExport' => true,
            'width' => '4%',
            'header' => "Dett.",
            'headerOptions' => ['class' => 'skip-export-pdf card-header bg-' . $usrgrid . '
             ','style'=>'color:black;'],
            'template' => '{view},{create},{update}'], 






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
<div class="rapportini-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Inserisci  Rapportino', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php $isFa = 'file-pdf-o';
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
        'heading' => '<i class="fas  fa-book">Rapportini</i>',
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

    <?php Pjax::end(); ?>

</div>
