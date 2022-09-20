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
Case SC.Pagata When 1 Then 0 Else (SC.ImportoE * Case Left(SC.Cd_CF, 1) When 'C' Then 1 Else -1 End) End AS ImportoDaPagareE,
Case SC.Pagata When 1 Then 0 Else (SC.ImportoV * Case Left(SC.Cd_CF, 1) When 'C' Then 1 Else -1 End) End AS ImportoDaPagareV,
SC.Pagata,
SC.FTE_TipoPagamento,
IsNull(S.ImportoDare, 0) As ImportoDare,
IsNull(S.ImportoAvere, 0) As ImportoAvere,
IsNull(S.Saldo, 0) As Saldo,
IsNull(CF.CD_CFStato, '') As Stato_Cli,
IsNull(CF.CD_CFSettore, '') As Settore_Cli,
isnull( CGMovr.cd_sottocommessa,'') as cd_sottocommessa

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

echo 'ciao';?>