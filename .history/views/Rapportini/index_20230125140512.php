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

$gridColumns= 
[];
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
        'heading' => '<i class="fas  fa-book">Documenti</i>',
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
