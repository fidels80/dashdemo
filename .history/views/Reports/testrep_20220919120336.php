<?php 

use kartik\grid\GridView;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use yii\data\SqlDataProvider;
$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['grid_color', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}



$count = Yii::$app->db->createCommand('
    SELECT COUNT(*)   From adb_vivendasrl.dbo.SC
Join adb_vivendasrl.dbo.CF On CF.Cd_CF = SC.Cd_CF
Left Join (Select * From adb_vivendasrl.dbo.CGSaldo_CF(adb_vivendasrl.dbo.afn_CGEsercizio(GetDate())))
S On S.Cd_CF = SC.Cd_CF
Left Join adb_vivendasrl.dbo.CGMovT On SC.Id_CGMovT = CGMovT.Id_CGMovT
Left Join adb_vivendasrl.dbo.CGMovr On SC.Id_CGMovT = CGMovr.Id_CGMovT and cd_sottocommessa is not null
Left Join adb_vivendasrl.dbo.DoTes On SC.Id_DoTes = DoTes.Id_DoTes
Left Join adb_vivendasrl.dbo.ScDistinta On SC.Id_ScDistinta = ScDistinta.Id_ScDistinta
where sc.cd_cf=:cf
', [':cf' =>  $ris['cd_cli']])->queryScalar();


$provider = new SqlDataProvider([
    'sql' => 'Select distinct
SC.Cd_CGConto_Banca,
SC.DataScadenza,
SC.Cd_CF,
CF.Descrizione,
SC.DataFattura,
SC.NumFattura,
SC.TipoRata,
SC.Emessa,
SC.Contabilizzata,
SC.Insoluta,
SC.Cd_VL,
SC.ImportoE,
SC.ImportoV,
Case SC.Pagata When 1 Then 0 Else (SC.ImportoE * Case Left(SC.Cd_CF, 1) When \'C\' Then 1 Else -1 End) End AS ImportoDaPagareE,
Case SC.Pagata When 1 Then 0 Else (SC.ImportoV * Case Left(SC.Cd_CF, 1) When \'C\' Then 1 Else -1 End) End AS ImportoDaPagareV,
SC.Pagata,
SC.FTE_TipoPagamento,
IsNull(S.ImportoDare, 0) As ImportoDare,
IsNull(S.ImportoAvere, 0) As ImportoAvere,
IsNull(S.Saldo, 0) As Saldo,
IsNull(CF.CD_CFStato, \'\') As Stato_Cli,
IsNull(CF.CD_CFSettore, \'\') As Settore_Cli,
isnull( CGMovr.cd_sottocommessa,\'\') as cd_sottocommessa

From adb_vivendasrl.dbo.SC
Join adb_vivendasrl.dbo.CF On CF.Cd_CF = SC.Cd_CF
Left Join (Select * From adb_vivendasrl.dbo.CGSaldo_CF(adb_vivendasrl.dbo.afn_CGEsercizio(GetDate())))
S On S.Cd_CF = SC.Cd_CF
Left Join adb_vivendasrl.dbo.CGMovT On SC.Id_CGMovT = CGMovT.Id_CGMovT
Left Join adb_vivendasrl.dbo.CGMovr On SC.Id_CGMovT = CGMovr.Id_CGMovT and cd_sottocommessa is not null
Left Join adb_vivendasrl.dbo.DoTes On SC.Id_DoTes = DoTes.Id_DoTes
Left Join adb_vivendasrl.dbo.ScDistinta On SC.Id_ScDistinta = ScDistinta.Id_ScDistinta
where sc.cd_cf=:cf
Order By SC.Cd_CF, SC.DataScadenza 
',
    'params' => [':cf' => $ris['cd_cli']],
    'totalCount' => $count,
    'pagination' => [
        'pageSize' => 10,
    ],
    'sort' => [
        'attributes' => [
            'Cd_CF',
            'DataScadenza',
            'DataFattura',
        ],
    ],
]);
$models = $provider->getModels();
yii::warning(array_keys($models));
$i=1;
foreach ($models as $key => $value) {
  //  echo $key;
if ($i==1) {
    foreach ($value as $Skey=>$svalue) {
     echo $Skey.'<br>';
$i=3;
    }
}
}

//echo 'ciao';


$columns=[

 ['label' => 'Cd_CGConto_Banca',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            //'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '5%',
            'attribute' => 'Cd_CGConto_Banca',
],
 ['label' => 'DataScadenza',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            //'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '5%',
            'attribute' => 'DataScadenza',
],
 ['label' => 'Cd_CF',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            //'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '5%',
            'attribute' => 'Cd_CF',
],
 ['label' => 'Descrizione',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            //'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '5%',
            'attribute' => 'Descrizione',
],
 ['label' => 'DataFattura',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            //'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '5%',
            'attribute' => 'DataFattura',
],
 ['label' => 'NumFattura',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            //'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '5%',
            'attribute' => 'NumFattura',
],
 ['label' => 'TipoRata',
            'headerOptions' => ['class' => 'card-header bg-' . $usrgrid . ' text-white'],
            //'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '5%',
            'attribute' => 'TipoRata',
],
];


echo
	GridView::widget([
    'dataProvider' => $provider,'resizableColumnsOptions' => ['resizeFromBody' => true],
    'persistResize' => true,
    'resizeStorageKey'=>Yii::$app->user->id . '-' . date('m').'sc',
    'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
    'columns' => $columns,
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
            'filename' => Yii::t('kvgrid', 'Portal pdf export'),
            'alertMsg' => Yii::t('kvgrid', 'The PDF export file will be generated for download.'),
            'options' => ['title' => Yii::t('kvgrid', 'Portable Document Format')],
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
             /*   'methods' => [
                    'SetHeader' => [
                        ['odd' => $pdfHeader, 'even' => $pdfHeader],
                    ],
                    'SetFooter' => [
                        ['odd' => $pdfFooter, 'even' => $pdfFooter],
                    ],
                ],*/
                'options' => [
                 //   'title' => $title,
                    'subject' => Yii::t('kvgrid', 'PDF'),
                    'keywords' => Yii::t('kvgrid', 'krajee, grid, export, yii2-grid, pdf'),
                ],
                'contentBefore' => '',
                'contentAfter' => '',
            ],
        ],
    ],'panel' => [
        'type' => $ris['grid_color'],
        'heading' => '<i class="fas  fa-book"></i> x_ScadCon _SC',
    ],
    'responsive' => true,
    'resizableColumns' => true,
    'showPageSummary' => true,
    'pjax' => false,
]);


?>