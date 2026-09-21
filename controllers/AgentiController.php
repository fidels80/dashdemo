<?php namespace app\controllers;

use Yii;
use yii\web\Controller;

class AgentiController extends Controller
{
public function actionIndex($cdAgente = null)
{
$sql = "
SELECT
Provvigione.Id_Provvigione AS Id_Provvigione,
Provvigione.Id_Sc AS Id_Sc,
Provvigione.Cd_Agente AS Cd_Agente,
CF_AGE.Cd_CF AS Agente_Cd_CF,
Agente.Descrizione AS Agente_Descrizione,
CF_AGE.Indirizzo AS Agente_Indirizzo,
CF_AGE.Cap AS Agente_CAP,
CF_AGE.Localita AS Agente_Localita,
CF_AGE.Cd_Provincia AS Agente_Provincia,
CF_AGE.Cd_Nazione AS Agente_Nazione,
SC.Cd_CF AS Cd_CF,
CF.Descrizione AS CF_Descrizione,
CF.Indirizzo AS Indirizzo,
CF.Cap AS Cap,
CF.Localita AS Localita,
CF.Cd_Provincia AS Cd_Provincia,
CF.Cd_Nazione AS Cd_Nazione,
SC.TipoRata AS TipoRata,
SC.ImportoE AS ImportoE,
SC.DataPagamento AS DataPagamento,
SC.DataFattura AS DataFattura,
SC.NumFattura AS NumFattura,
CASE
WHEN ISNULL(Cgmovt.TipoCausale, DoTes.TipoDocumento) IN ('1', '3', 'F') THEN 'FT'
WHEN ISNULL(Cgmovt.TipoCausale, DoTes.TipoDocumento) IN ('2', '4', 'N') THEN 'NC'
ELSE CASE WHEN SIGN(Sc.ImportoV) = -1 THEN 'NC' ELSE 'FT' END
END AS TipoCausale,
SC.DataScadenza AS DataScadenza,
SC.RiemessaSuInsoluto AS Riemessa,
ISNULL(SCAIns.Insoluta, 0) AS Insoluta,
Provvigione.Liquidata AS Liquidata,
CONVERT(BIT, CASE WHEN SCAIns.Insoluta = 1 AND PROIns.Liquidata = 1 THEN 1 ELSE 0 END) AS LiquidataInsoluta,
SC.Id_Sc_P_Ins AS Id_Sc_P_Ins,
PROIns.Id_Provvigione AS Id_Provvigione_P_Ins,
CONVERT(BIT, CASE WHEN SC.Provvisorio = 1 OR ISNULL(CGTInc.Provvisorio, 0) = 1 OR ISNULL(CGTIns.Provvisorio, 0) = 1 THEN 1 ELSE 0 END) AS Provvisorio,
Provvigione.ProvvigioneE AS ProvvigioneE,
Provvigione.DataLiquidazione AS DataLiquidazione,
CONVERT(NUMERIC(18, 2), Provvigione.ProvvigioneE * ~Sc.Pagata * ~SC.Insoluta) AS NonMaturata,
CONVERT(NUMERIC(18, 2), Provvigione.ProvvigioneE * Sc.Pagata * ~SC.Insoluta * ~Provvigione.Liquidata) AS MaturataDaLiquidare,
CONVERT(NUMERIC(18, 2), Provvigione.ProvvigioneE * ~SC.Insoluta * Provvigione.Liquidata) AS LiquidataSuPagato,
CONVERT(NUMERIC(18, 2), Provvigione.ProvvigioneE * CONVERT(BIT, CASE WHEN SCAIns.Insoluta = 1 AND PROIns.Liquidata = 1 THEN 1 ELSE 0 END)) AS LiquidataSuInsoluto,
ISNULL(CGMovT.ImportoE, DOtotali.TotDocumentoE) * SIGN(Sc.ImportoE) AS ImportoFatturaE,
ISNULL(CGMovT.ImponibileE, DOtotali.TotImponibileE) * SIGN(Sc.ImportoE) AS ImportoImponibileE
FROM Provvigione
INNER JOIN SC ON Provvigione.Id_SC = Sc.Id_SC
INNER JOIN Agente ON Provvigione.Cd_Agente = Agente.Cd_Agente
LEFT JOIN CF ON Sc.Cd_CF = CF.Cd_CF
LEFT JOIN CF CF_AGE ON CF_AGE.Cd_CF = Agente.Cd_CF_FOR
LEFT JOIN CGMovT ON Sc.Id_CGMovT = Cgmovt.Id_CGMovT
LEFT JOIN DOtes ON Sc.Id_Dotes = Dotes.Id_Dotes
LEFT JOIN DOtotali ON Sc.Id_Dotes = Dototali.Id_Dotes
LEFT JOIN CGMovR CGRInc ON SC.Id_Sc = CGRInc.Id_SC AND CGRInc.TipoContab = 4
LEFT JOIN CGMovT CGTInc ON CGRInc.Id_CGMovT = CGTInc.Id_CGMovT
LEFT JOIN SC SCAIns ON Sc.Id_Sc_P_Ins = SCAIns.Id_SC
LEFT JOIN CGMovR CGRIns ON SCAIns.Id_Sc = CGRIns.Id_SC AND CGRIns.TipoContab = 5
LEFT JOIN CGMovT CGTIns ON CGRIns.Id_CGMovT = CGTIns.Id_CGMovT
LEFT JOIN Provvigione PROIns ON SCAIns.Id_SC = PROIns.Id_SC AND Provvigione.Cd_Agente = PROIns.Cd_Agente
LEFT JOIN FactoringProject ON FactoringProject.Id_FactoringProject = Dotes.Id_FactoringProject
WHERE
Sc.TipoRata IN ('R', 'P', 'L', 'C', 'T', 'B', 'A', 'D', 'I', 'M') AND
(ISNULL(CGMovT.Provvisorio, 0) = 0) AND
(ISNULL(CGTInc.Provvisorio, 0) = 0) AND
(ISNULL(CGTIns.Provvisorio, 0) = 0) AND
SC.Insoluta = 0
";

if ($cdAgente !== null) {
$sql .= " AND Provvigione.Cd_Agente = :cdAgente";
}

$sql .= " ORDER BY Cd_Agente, NumFattura, DataFattura, Cd_CF, DataScadenza";

$command = Yii::$app->db5->createCommand($sql);

if ($cdAgente !== null) {
$command->bindValue(':cdAgente', $cdAgente);
}

$result = $command->queryAll();
        $sumLiquidata = 0;
        $sumLiquidataInsoluta = 0;
        $sumProvvigioneE = 0;
        $sumMaturataDaLiquidare = 0;
        $sumNonMaturata=0;

        foreach ($result as $row) {
            $sumLiquidata += $row['Liquidata'];
            $sumLiquidataInsoluta += $row['LiquidataInsoluta'];
            $sumProvvigioneE += $row['ProvvigioneE'];
            $sumMaturataDaLiquidare += $row['MaturataDaLiquidare'];
            $sumNonMaturata += $row['NonMaturata'];
        }




        $sql = "
            SELECT
                CF.Descrizione as Cd_CF,
                SUM(ISNULL(CGMovT.ImportoE, DOtotali.TotDocumentoE) * SIGN(SC.ImportoE)) AS TotaleFatturato,
                SUM(Provvigione.ProvvigioneE) AS TotaleProvvigioni
            FROM Provvigione
            INNER JOIN SC ON Provvigione.Id_SC = SC.Id_SC
            left join cf on cf.cd_cf=sc.cd_cf
            LEFT JOIN CGMovT ON SC.Id_CGMovT = CGMovT.Id_CGMovT
            LEFT JOIN DOtotali ON SC.Id_Dotes = DOtotali.Id_Dotes
            WHERE SC.TipoRata IN ('R', 'P', 'L', 'C', 'T', 'B', 'A', 'D', 'I', 'M') 
              AND (ISNULL(CGMovT.Provvisorio, 0) = 0) 
              AND SC.Insoluta = 0
        ";

        if ($cdAgente !== null) {
            $sql .= " AND Provvigione.Cd_Agente = :cdAgente";
        }

        $sql .= " GROUP BY cf.descrizione";

        $command = Yii::$app->db5->createCommand($sql);

        if ($cdAgente !== null) {
            $command->bindValue(':cdAgente', $cdAgente);
        }

        $result2 = $command->queryAll();



        $cdCfs = [];
        $totaleFatturato = [];
        $totaleProvvigioni = [];

        foreach ($result2 as $row) {
            $cdCfs[] = $row['Cd_CF'];
            $totaleFatturato[] = $row['TotaleFatturato'];
            $totaleProvvigioni[] = $row['TotaleProvvigioni'];
        }


        $sql= "select agente.Cd_Agente,agente.Descrizione,cf.Indirizzo,cf.Localita,cf.CodiceFiscale,cf.PartitaIva from agente
left join cf on cf.cd_cf=Agente.Cd_CF_For ";
        if ($cdAgente !== null) {
            $sql .= " where agente.Cd_Agente = :cdAgente";
        }
        $command = Yii::$app->db5->createCommand($sql);

        if ($cdAgente !== null) {
            $command->bindValue(':cdAgente', $cdAgente);
        }

        $result3 = $command->queryAll();




        $sql = "
            SELECT
                cf.descrizione,
                SC.DataFattura,
                SUM(ISNULL(CGMovT.ImportoE, DOtotali.TotDocumentoE) * SIGN(SC.ImportoE)) AS TotaleFatturato
            FROM Provvigione
            INNER JOIN SC ON Provvigione.Id_SC = SC.Id_SC
            LEFT JOIN CGMovT ON SC.Id_CGMovT = CGMovT.Id_CGMovT
            LEFT JOIN DOtotali ON SC.Id_Dotes = DOtotali.Id_Dotes
             left join cf on cf.cd_cf=sc.cd_cf
            WHERE SC.TipoRata IN ('R', 'P', 'L', 'C', 'T', 'B', 'A', 'D', 'I', 'M') 
              AND (ISNULL(CGMovT.Provvisorio, 0) = 0) 
              AND SC.Insoluta = 0
        ";

        if ($cdAgente !== null) {
            $sql .= " AND Provvigione.Cd_Agente = :cdAgente";
        }

        $sql .= " GROUP BY cf.descrizione, SC.DataFattura";

        $command = Yii::$app->db5->createCommand($sql);

        if ($cdAgente !== null) {
            $command->bindValue(':cdAgente', $cdAgente);
        }

        $result4 = $command->queryAll();

        $data = [];
        foreach ($result4 as $row) {
            $data[$row['descrizione']][] = [
                'x' => $row['DataFattura'],
                'y' => $row['TotaleFatturato']
            ];
        }



$sql = "select cd_agente, descrizione from agente" ;
  
 $command = Yii::$app->db5->createCommand($sql);



$eleagenti=$command->queryAll();




return $this->render('index', ['result' => $result, 'sumLiquidata' => $sumLiquidata,
            'sumLiquidataInsoluta' => $sumLiquidataInsoluta,
            'sumProvvigioneE' => $sumProvvigioneE,
            'sumMaturataDaLiquidare' => $sumMaturataDaLiquidare,
            'sumNonMaturata'=> $sumNonMaturata,
            'cdCfs' => $cdCfs,
            'totaleFatturato' => $totaleFatturato,
            'totaleProvvigioni' => $totaleProvvigioni,
            'result2'=>$result2,
            'result3' => $result3,
            'data'=>$data,
            'eleagenti'=>$eleagenti
        ]);
}
}