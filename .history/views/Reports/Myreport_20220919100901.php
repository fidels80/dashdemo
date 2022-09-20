<?php
namespace app\views\reports;
 use app\vendor\koolreport\yii2\Friendship;




class MyReport extends \koolreport\KoolReport
{
    use \koolreport\yii2\Friendship;
    // By adding above statement, you have claim the friendship between two frameworks
    // As a result, this report will be able to accessed all databases of Yii2
    // There are no need to define the settings() function anymore
    // while you can do so if you have other datasources rather than those
    // defined in Laravel.

    public function setup()
    {
 
 $usrid = Yii::$app->user->Id;

if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
    //->AsArray();
}

 
        $this->src("sqlserver")
            ->query("Select   distinct
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
   IsNull(S.ImportoDare, 0)  As ImportoDare,
   IsNull(S.ImportoAvere, 0) As ImportoAvere,
   IsNull(S.Saldo, 0)        As Saldo,
	IsNull(CF.CD_CFStato, '')        As Stato_Cli,
	IsNull(CF.CD_CFSettore, '')        As Settore_Cli,
isnull( CGMovr.cd_sottocommessa,'') as cd_sottocommessa
   
From SC
	Join CF										                               On CF.Cd_CF = SC.Cd_CF
	Left Join (Select * From dbo.CGSaldo_CF(dbo.afn_CGEsercizio(GetDate()))) S On S.Cd_CF  = SC.Cd_CF
	Left Join CGMovT 						                                   On SC.Id_CGMovT = CGMovT.Id_CGMovT
		Left Join CGMovr 						                                   On SC.Id_CGMovT = CGMovr.Id_CGMovT and cd_sottocommessa is not null
Left Join DoTes 						                                   On SC.Id_DoTes = DoTes.Id_DoTes
	Left Join ScDistinta 					                                   On SC.Id_ScDistinta = ScDistinta.Id_ScDistinta

Order By SC.Cd_CF, SC.DataScadenza")
->where('sc.cd_cf',$ris['cd_cli'])
            ->pipe($this->dataStore("offices"));
    }
}
