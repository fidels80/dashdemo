<?php
namespace app\reports;
 use \koolreport\yii2\Friendship;
class _MyReport extends \koolreport\KoolReport
{
    use \koolreport\yii2\Friendship;
    // By adding above statement, you have claim the friendship between two frameworks
    // As a result, this report will be able to accessed all databases of Yii2
    // There are no need to define the settings() function anymore
    // while you can do so if you have other datasources rather than those
    // defined in Laravel.

    public function setup()
    {
        $this->src("sqlserver")
            ->query("Declare  @DataMaxPagamento	SmallDateTime
		,@ConsideraPagata	Bit

Select	 @DataMaxPagamento	= Null
		,@ConsideraPagata	= 0

SELECT
     SC.Cd_CF
	, CF_Descrizione		= CF.Descrizione
	, SC.DataFattura
	, SC.NumFattura
	, SC.Protocollo
	, SC.DataScadenza
	, SC.DataPagamento
	, SC.PartNum
	, SC.PartAnno
	, SC.TipoRata
	, SC_Descrizione		= SC.Descrizione
	, SC.NumEffetto
	, SC.TotEffetti
	, SC.ImportoE
	, SC.Cd_VL
	, VL.PicSep
	, SC.Cambio
	, SC.Decimali
	, SC.ImportoV
	, SC.Bloccata
	, SC.Emessa
	, SC.Contabilizzata
	, SC.Insoluta
	, Pagata				= Convert(bit, 
								Case 
									When SC.Pagata = 0 
										then 0
									When SC.Pagata = 1 And @DataMaxPagamento Is Not Null And SC.DataPagamento > @DataMaxPagamento  
										then 0 
									Else 
										1 
								end )
	, PagataPost			= Convert(bit, 
								Case 
									When SC.Pagata = 1 And @DataMaxPagamento Is Not Null And SC.DataPagamento > @DataMaxPagamento  
										then 1 
									else 
										0 
								end )
	, SC.Cd_PG
	, SC.Cd_AbiCab
	, SC.BicCode
	, SC.Id_SC_P_Ins
	, SC.Cig
	, SC.Cup
	, SC.FTE_TipoPagamento 
	, ImportoDaPagareV		= SC.ImportoV * ~Convert(bit, 
												Case 
													When SC.Pagata = 0 
														then 0
													When SC.Pagata = 1 And @DataMaxPagamento Is Not Null And SC.DataPagamento > @DataMaxPagamento  
														then 0 
													Else 
														1 
												end )
	, ImportoDaPagareE		= SC.ImportoE * ~Convert(bit, 
												Case 
													When SC.Pagata = 0 
														then 0
													When SC.Pagata = 1 And @DataMaxPagamento Is Not Null And SC.DataPagamento > @DataMaxPagamento  
														then 0 
													Else 
														1 
												end )
	, SegnoCF				= (Case CF.Cliente When 1 Then 1 Else -1 End)
	, CF_Cliente			= CF.Cliente
From				SC
	Inner Join	CF      		ON CF.Cd_CF 				= SC.Cd_CF
	Left  Join	VL      		ON SC.Cd_VL					= VL.Cd_VL
	Left  Join	CGMovT			ON SC.Id_CGMovT		   = CGMovT.Id_CGMovT
	Left  Join	DOTes			ON Sc.Id_DOTes				= DoTes.Id_DOTes
	Left  Join	SCDistinta		ON SC.Id_ScDistinta		= SCDistinta.Id_ScDistinta
	Left  Join  FactoringProject ON FactoringProject.Id_FactoringProject	= DoTes.Id_FactoringProject
Where
	 (SC.Cd_CF LIKE 'C%') And Left(SC.Cd_CF, 1) = 'C' AND ( Sc.Insoluta = 0  And ((FactoringProject.Id_FactoringProject Is     Null Or  FactoringProject.Status  = 'AREN') Or (FactoringProject.Id_FactoringProject Is Not Null And FactoringProject.Status != 'AREN' And SC.RiemessaSuInsoluto = 1)) AND SC.TipoGruppo IN (1,2)) AND Sc.TipoRata IN ( 'R', 'P', 'L', 'C', 'T', 'B', 'A', 'D', 'I', 'M') 
Order by
	SC.DataScadenza
")
            ->pipe($this->dataStore("offices"));
    }
}
