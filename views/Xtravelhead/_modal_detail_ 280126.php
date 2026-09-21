<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\nav\NavX;
use kartik\select2\Select2;
use onmotion\apexcharts\ApexchartsWidget;
use yii\helpers\Json;
use kartik\dialog\Dialog;
use yii\web\JsExpression;
use yii\data\ArrayDataProvider;
use kartik\export\ExportMenu;
use yii\bootstrap4\Modal;
use kartik\editable\Editable;
//use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use app\models\XVenue;
use app\models\XStruttura;

//use yii\data\ArrayDataProvider;
//$usrid = Yii::$app->user->Id;
/*if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['grid_color', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrgrid = $ris['grid_color'] ?? '';*/

$filteredData = [];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <!-- Carica jQuery (PRIMA di DataTables) -->
    <style>
        @media screen and (width: 1440px) {

            .card-title,
            .table,
            .thead-dark,
            td,
            th,
            .btn,
            .panel-title,
            .panel-heading {
                font-size: calc(100% - 2px) !important;
            }

            .card-body {
                padding: 0.75rem !important;
            }

            .table td,
            .table th {
                padding: 0.3rem !important;
            }

            .fas {
                font-size: smaller !important;
            }

            .form-control,
            .select2-selection {
                font-size: smaller !important;
            }

            /* For kartik GridView components */
            .kv-panel-before,
            .kv-panel-after {
                font-size: smaller !important;
            }
        }


        @media screen and (width: 1920px) {

            .card-title,
            .table,
            .thead-dark,
            td,
            th,
            .btn,
            .panel-title,
            .panel-heading {
                font-size: calc(100% - 2px) !important;
            }

            .card-body {
                padding: 0.85rem !important;
            }

            .table td,
            .table th {
                padding: 0.2rem !important;
            }

            .fas {
                font-size: smaller !important;
            }

            .form-control,
            .select2-selection {
                font-size: smaller !important;
            }

            /* For kartik GridView components */
            .kv-panel-before,
            .kv-panel-after {
                font-size: smaller !important;
            }
        }

        html,
        body {
            background-color: #f1eef6;
        }
    </style>
</head>



<div class="d-flex flex-wrap justify-content-center align-items-start">
    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                Budget Totale per Tappa <?php



                                        if (isValidUuid($value['citta'])) {
                                            $venue = Xvenue::find()->where(['id' => $value['citta']])->one();
                                            echo $venue ? $venue->venue . ' - ' . $venue->citta : $value['citta'];
                                        } else {
                                            echo htmlspecialchars($value['citta']); // fallback sicuro se non è un UUID
                                        }

                                        //   echo $value['citta']



                                        ?></h5>
            <br>
            <br>
            <table class="table">
                <thead>
                    <TH class="table-tothead">Comm.</TH>
                    <!--TH class="table-tothead">Cliente</TH-->
                    <!--TH class="table-tothead">Inizio</TH-->
                    <TH class="table-tothead">Fine</TH>
                    <TH class="table-tothead">Impon.</TH>
                    <TH class="table-tothead">Non Imp.</TH>
                    <TH class="table-tothead">Pagato</TH>
                    <TH class="table-tothead">Fee</TH>
                    <TH class="table-tothead">Imp.Fat.</TH>

                </thead>
                <?php
                if ($tipo === null) {
                    $connection = Yii::$app->db5;
                    $command = $connection->createCommand(
                        "SELECT x_tiposhow FROM xtravelhead WHERE th_id = :id"
                    );
                    $tipo = $command->bindValue(':id', $th_id)->queryOne();
                }

                $tottassa = 0;
                $totfee = 0;
                $tottalecard = 0;
                $totgen = 0;
                $totimp = 0;
                $totpagato = 0;

                $r = xxLoadtappa2(
                    $model->th_id,
                    null,
                    str_replace("'", "''", $value['citta']),
                    $value['cd_cf_ft'] ?? null,
                    $tipo
                );

                $tmp = xLoadtappa2(
                    $model->th_id,
                    //$cliente
                    $r['cli'] ?? ($r['dettaglio'][0]['cd_cf_ft'] ?? null),
                    str_replace("'", "''", $value['citta']),
                    null,
                    $value['cd_cf_ft'] ?? null,
                    $tipo,
                    'dettaglio 179'
                );
                $ddataProvider = new ArrayDataProvider([
                    'allModels' => $tmp['dettaglio'],
                    'pagination' => [
                        'pageSize' => 2000, // Puoi modificare la paginazione a tuo piacimento
                    ],
                    'sort' => [
                        'attributes' => ['x_scdesc', 'descli', 'struttura', 'guest'], // Attributi ordinabili
                    ],
                ]);
                //yii::error($tmp['totale']);
                foreach ($tmp['totale'] as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    echo $row['x_scdesc'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['descli'];
                    echo '</td>';
                    /*echo '<td scope="col">';
                    echo date('d/m/y', strtotime($row['startdate']));
                    echo '</td>';
                    echo '<td scope="col">';
                    echo date('d/m/y', strtotime($row['enddate']));
                    echo '</td>';
                    */
                    echo '<td scope="col">€';
                    echo formatEuro($row['imponibile'], 2);
                    $totimp = $totimp + $row['imponibile'] ?? 0;
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo formatEuro($row['tassa'], 2);
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo formatEuro(($row['pagato'] ?? 0), 2);
                    $totpagato = $totpagato + ($row['pagato'] ?? 0);
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo formatEuro($row['fee'], 2);
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo formatEuro($row['fee'] + $row['imponibile'] + $row['tassa'], 2);
                    echo '</td>';
                    echo '</tr>';
                    $tottassa = $tottassa + $row['tassa'];
                    $totfee = $totfee + $row['fee'];
                    $tottalecard = $tottalecard + $row['totale'];
                    $totgen = $totgen + $row['totalegenerale'];
                }
                echo '<TR>';
                echo '<td scope="col" colspan=2    class="table-tothead2">';
                echo '<strong>TOTALE GENERALE</strong>';
                echo '</td>';
                echo '<td scope="col" class="table-tothead2">';
                echo '<strong>€' . formatEuro($totimp) . '</strong>';
                echo '</td>';
                echo '<td scope="col" class="table-tothead2">';
                echo '<strong>€' . formatEuro($tottassa) . '</strong>';
                echo '</td>';
                echo '<td scope="col" class="table-tothead2">';
                echo '<strong>€' . formatEuro($totpagato) . '</strong>';
                echo '</td>';
                echo '<td scope="col" class="table-tothead2">';
                echo '<strong>€' . formatEuro($totfee) . '</strong>';
                echo '</td>';
                echo '<td scope="col" class="table-tothead">';
                echo '<strong>€' . formatEuro($totfee + $totimp + $tottassa) . '</strong>';
                echo '</td>';
                echo '</tr>'

                ?>

            </table>
        </div>
    </div>


    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title"> Dettaglio Analitico Costi</h5>
            <br>
            <?php

            $tqta = 0;
            $tprz = 0;
            $timp = 0;
            $tcity = 0;
            $tiva = 0;
            $tfat = 0;
            $tpag = 0;
            $tfee = 0;


            $dett = $tmp['dettaglio'];
            $db = Yii::$app->db5;
            $xcitta = $value['citta'];
            $xcitta = str_replace("'", "''", $xcitta);
            if ($tipo === null) {
                $connection = Yii::$app->db5;
                $command = $connection->createCommand(
                    "SELECT x_tiposhow FROM xtravelhead WHERE th_id = :id"
                );
                $tipo = $command->bindValue(':id', $th_id)->queryOne();
            }
            switch ($tipo['x_tiposhow']) {
                case null:
                    $sql = "
 select struttura,sum(qta) as notti, avg(prezzo) as prezzo,
sum(tax_unit*qta) as city_tax,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as iimponibile,
sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
sum(prezzo*qta)+sum(tax_unit*qta) as totale_hotel,
sum(fee) as fee,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))
+sum(fee)+sum(tax_unit*qta)as fattura,
Sum(x_pagato) as pagato
from xtravelrow
left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva

where th_id=$model->th_id and (citta='$xcitta' or citta_da='$xcitta')
 and cd_ar in (select cd_ar from ar where Cd_ARClasse1='TRV' and Cd_ARClasse2='ACC')
 and cd_ar not in (select cd_ar from ar where x_isacconto=1)
group by struttura";
                    break;
                case 1:
                    //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                    $sql = "
 select struttura,sum(qta) as notti, avg(prezzo) as prezzo,
sum(tax_unit*qta) as city_tax,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as iimponibile,
sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
sum(prezzo*qta)+sum(tax_unit*qta) as totale_hotel,
sum(fee) as fee,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))
+sum(fee)+sum(tax_unit*qta)as fattura,
Sum(x_pagato) as pagato
from xtravelrow
left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva

where th_id=$model->th_id and (citta='$xcitta' or citta_da='$xcitta')
 and cd_ar in (select cd_ar from ar where Cd_ARClasse1='TRV' and Cd_ARClasse2='ACC')
 and cd_ar not in (select cd_ar from ar where x_isacconto=1)
group by struttura";
                    break;
                case 2:
                    $sql = "
 select struttura,sum(qta) as notti, avg(prezzo) as prezzo,
sum(tax_unit*qta) as city_tax,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as iimponibile,
sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
sum(prezzo*qta)+sum(tax_unit*qta) as totale_hotel,
sum(fee) as fee,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))
+sum(fee)+sum(tax_unit*qta)as fattura,
Sum(x_pagato) as pagato
from xtravelrow
left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva

where th_id=$model->th_id and sottocommessa='$xcitta'
 and cd_ar in (select cd_ar from ar where Cd_ARClasse1='TRV' and Cd_ARClasse2='ACC')
 and cd_ar not in (select cd_ar from ar where x_isacconto=1)
group by struttura";
                    break;
                case 3:
                    $xcliente = $value['cd_cf_ft'];
                    $sql = "
 select struttura,sum(qta) as notti, avg(prezzo) as prezzo,
sum(tax_unit*qta) as city_tax,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as iimponibile,
sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
sum(prezzo*qta)+sum(tax_unit*qta) as totale_hotel,
sum(fee) as fee,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))
+sum(fee)+sum(tax_unit*qta)as fattura,
Sum(x_pagato) as pagato
from xtravelrow
left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva

where th_id=$model->th_id and sottocommessa='$xcitta'
and cd_cf_ft='$xcliente'
 and cd_ar in (select cd_ar from ar where Cd_ARClasse1='TRV' and Cd_ARClasse2='ACC')
 and cd_ar not in (select cd_ar from ar where x_isacconto=1)
group by struttura";
                    break;
            }
            //yii::warning($sql);




            $command = $db->createCommand($sql);
            if (isset($tot_hotel)) {
                unset($tot_hotel);
            }
            $tot_hotel = $command->queryAll();

            //          yii::warning((count($tot_hotel)));
            ?>

            <?php if (count($tot_hotel) > 0): ?>
                <br>
                <h5 class="card-title" style="color: #4a6fa5;">Hotel - Ripilogo</h5>
                <br>
                <div style="max-height: 180px; overflow-y: auto;">



                    <table class="table">
                        <?php if (count($tot_hotel) > 0): ?>
                            <thead>
                                <TH class="table-totheadhotel" width="10%">Strutt.</TH>
                                <TH class="table-totheadhotel" width="3%">RM</TH>
                                <TH class="table-totheadhotel" width="7%"> Impon.</TH>
                                <TH class="table-totheadhotel" width="7%">C.Tax</TH>
                                <TH class="table-totheadhotel" width="7%">Iva</TH>
                                <TH class="table-totheadhotel" width="7%">Pagato</TH>
                                <TH class="table-totheadhotel" width="7%">FEE</TH>
                                <TH class="table-totheadhotel" width="7%">Imp.Fat</TH>
                                <th style="background-color: white;   border:0px" width="2%"></th>
                                <TH class="table-totheadhotel" width="7%">%BGD</TH>
                                <TH class="table-totheadhotel" width="7%">Media</TH>
                            <?php endif; ?>
                            <?php
                            /*                    <TH>Totale Hotel</TH> 
                    <TH>Tot Fattura</TH> */




                            if (count($tot_hotel) > 0) {

                                $tmp = xLoadtappa2h(
                                    $model->th_id,
                                    $r['cli'] ?? ($r['dettaglio'][0]['cd_cf_ft'] ?? null),
                                    str_replace("'", "''", $value['citta']),
                                    null,
                                    $xcliente ?? null,
                                    $tipo
                                );

                                $ddataProvider = new ArrayDataProvider([
                                    'allModels' => $tmp['dettaglio'],
                                    'pagination' => [
                                        'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
                                    ],
                                    'sort' => [
                                        'attributes' => ['x_scdesc', 'descli', 'struttura', 'guest'],
                                        // Attributi ordinabili
                                    ],
                                ]);




                                $total_fattura = array_sum(array_column($tot_hotel, 'fattura'));

                                foreach ($tot_hotel as $row) {
                                    //  yii::warning($row);
                                    echo '<TR>';
                                    echo '<td scope="col" class="table-info">';
                                    $tmpmodaln = str_replace('|', '_', str_replace(
                                        ' ',
                                        '_',
                                        $row['struttura']
                                    )) . uniqid();
                                    $tmpmodaln = str_replace('&', '_', $tmpmodaln);
                                    //   echo '<a href="#" data-toggle="modal" data-target="#struttura_'.$tmpmodaln.'">';


                                    $valore = $row['struttura'];

                                    // CASO 1: È solo un numero (ID) -> Serve AJAX
                                    if (is_numeric($valore)) {
                                        // Creiamo un ID univoco per il tag HTML di questa cella per poterlo selezionare con JS
                                        // Usiamo uniqid() per evitare conflitti se lo stesso ID struttura appare più volte
                                        $cellId = 'struttura_' . $valore . '_' . uniqid();

                                        // 1. Stampiamo il contenitore vuoto (o con un loader)
                                        echo "<span id='{$cellId}' class='loading-data'><i class='fa fa-spinner fa-spin'></i></span>";

                                        // 2. Stampiamo lo script JS specifico per questa cella
                                        // Nota: Usiamo json_encode per passare le variabili PHP a JS in modo sicuro
                                        echo "<script>
        (function() {
            var cellId = " . json_encode($cellId) . ";
            var data = " . json_encode($valore) . ";
            
            // Inizializza l'oggetto cache globalmente se non esiste
            window.strutturaCache = window.strutturaCache || {};

            // Controllo se abbiamo già il dato in cache per evitare la chiamata
            if (window.strutturaCache[data]) {
                 $('#' + cellId).text(window.strutturaCache[data]);
                 return;
            }

            setTimeout(function() {
                $.ajax({
                    url: 'index.php?r=xtravelhead/getstruttura',
                    type: 'GET',
                    data: { id: data },
                    success: function(response) {
                        if (response.success && response.data) {
                            // Salviamo in cache
                            window.strutturaCache[data] = response.data.nome;
                            
                            // Aggiorniamo la cella
                            $('#' + cellId)
                                .text(response.data.nome)
                                .attr('title', response.data.nome);
                                
                            // Se usi DataTables e hai la variabile 'table' accessibile globalmente:
                            if(typeof table !== 'undefined'){
                                // table.cell('#' + cellId).invalidate(); 
                                // Attenzione: invalidate() potrebbe ridisegnare la riga cancellando questo span se non gestito bene
                            }
                        } else {
                            $('#' + cellId).text('ID: ' + data);
                        }
                    },
                    error: function() {
                        $('#' + cellId).text('Err');
                    }
                });
            }, 0);
        })();
    </script>";
                                    }
                                    // CASO 2: È una stringa composta (es. "Hotel Roma | 123")
                                    elseif (strpos($valore, '|') !== false) {
                                        echo strstr($valore, '|', true);
                                    }
                                    // CASO 3: È una stringa semplice (es. "Hotel Roma")
                                    else {
                                        echo $valore;
                                    }


                                    echo '</a>';
                                    echo '</td>';
                                    echo '<td scope="col" class="table-info">';
                                    echo  $row['notti'];
                                    $tqta = $tqta + $row['notti'];
                                    echo '</td>';
                                    echo '<td scope="col" class="table-info">';
                                    echo '€' .  formatEuro($row['iimponibile']);
                                    echo '</td>';

                                    echo '<td scope="col" class="table-info">';
                                    echo '€' .  formatEuro($row['city_tax']);
                                    //  echo  formatEuro($row['prezzo'],2);
                                    $tprz = $tprz + $row['prezzo'];
                                    echo '</td>';
                                    echo '<td scope="col" class="table-info">';
                                    echo '€' .  formatEuro($row['iva']);
                                    $timp = $timp + $row['iimponibile'];
                                    echo '</td>';
                                    echo '<td scope="col" class="table-info">';
                                    // echo '€' .  formatEuro($row['city_tax']);
                                    echo '€' .  formatEuro($row['pagato']);
                                    $tpag = $tpag + $row['pagato'];
                                    $tcity = $tcity + $row['city_tax'];
                                    echo '</td>';
                                    echo '<td scope="col" class="table-info">';
                                    echo '€' .  formatEuro($row['fee']);
                                    $tiva = $tiva + $row['iva'];
                                    echo '</td>';
                                    echo '<td scope="col" class="table-info">';
                                    echo '€' .  formatEuro($row['iimponibile'] + $row['fee'] + $row['city_tax']);
                                    $tfat = $tfat + $row['fattura'];
                                    echo '</td>';
                                    echo '<td scope="col" class="table-info">';
                                    echo '</td>';
                                    echo '<td scope="col" class="table-info">';
                                    //echo '€' .  formatEuro($row['fee']);
                                    $tfee = $tfee + $row['fee'];

                                    if ($total_fattura > 0) {
                                        $percentuale = ($row['fattura'] / $total_fattura) * 100;
                                        echo number_format($percentuale, 2) . '%';
                                    } else {
                                        echo '0%';
                                    }

                                    echo '</td>';
                                    echo '<td scope="col" class="table-info">';
                                    echo '€' .  formatEuro($row['prezzo']);
                                    //$tfee=$tfee+$row['fee'];
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                echo '<tfoot>';
                                echo '<tr>';
                                echo '</tr>';
                                echo '</tfoot>';
                                echo '<td scope="col" class="table-totheadhotelft"><strong>Totali</strong>';
                                echo '</td>';

                                echo '<td scope="col" class="table-totheadhotelft"><strong>' . $tqta;
                                echo '</td>';
                                echo '<td scope="col" class="table-totheadhotelft"><strong>€' . formatEuro($timp);
                                echo '</strong></td>';
                                echo '<td scope="col" class="table-totheadhotelft"><strong>€' . formatEuro($tcity);
                                echo '</strong></td>';
                                echo '<td scope="col" class="table-totheadhotelft"><strong>€' . formatEuro($tiva);
                                echo '</strong></td>';
                                echo '<td scope="col" class="table-totheadhotelft"><strong>€' . formatEuro($tpag);
                                echo '</strong></td>';
                                echo '<td scope="col" class="table-totheadhotelft"><strong>€' . formatEuro($tfee);
                                echo '</strong></td>';
                                echo '<td scope="col" class="table-totheadhotelft"><strong>€' .
                                    formatEuro($tfee + $timp + $tcity);
                                echo '</strong></td>';
                                echo '<td >';
                                echo '</td>';
                                echo '<td class="table-totheadhotelft">';
                                echo '</td>';
                                echo '<td class="table-totheadhotelft"> <strong>€' .
                                    formatEuro(($tpag) / (($tqta <> 0) ? $tqta : 1)) . "</strong>";
                                echo '</td>';


                                echo '</tfoot>';

                                echo  "";
                                //  echo '<br>';
                            } ?>
                    </table>
                </div>
            <?php endif; ?>
            <?php


            $stqta = 0;
            $stprz = 0;
            $stimp = 0;
            $stcity = 0;
            $stiva = 0;
            $stfat = 0;
            $stpag = 0;
            $stfee = 0;

            $db = Yii::$app->db5;
            $xcitta = $value['citta'];
            $xcitta = str_replace("'", "", $xcitta);
            $xcliente = $value['cd_cf_ft'] ?? null;
            $xcitta = str_replace("'", "''", $xcitta);
            if ($tipo === null) {
                $connection = Yii::$app->db5;
                $command = $connection->createCommand(
                    "SELECT x_tiposhow FROM xtravelhead WHERE th_id = :id"
                );
                $tipo = $command->bindValue(':id', $th_id)->queryOne();
            }
            switch ($tipo['x_tiposhow']) {
                case null:
                    $sql = "
 ;WITH base AS (
    SELECT 
        ARClasse123.SottoClasse AS servizio,
        ARClasse123.Cd_ARClasse123,
        xtravelrow.qta,
        xtravelrow.prezzo,
        xtravelrow.tax_unit,
        xtravelrow.fee,
        xtravelrow.x_pagato,
        aliquota.aliquota,
        imponibile = [dbo].[afn_Scorporo_GetImponibile]((prezzo * qta), aliquota.aliquota, 2),
        imposta    = [dbo].[afn_Scorporo_GetImposta]((prezzo * qta), aliquota.aliquota, 2)
    FROM xtravelrow
    LEFT JOIN aliquota ON aliquota.cd_aliquota = xtravelrow.codiva
    LEFT JOIN ar ON ar.Cd_AR = xtravelrow.cd_Ar
    LEFT JOIN ARClasse123 ON ar.Cd_ARClasse123 = ARClasse123.Cd_ARClasse123
    WHERE 
        th_id = $model->th_id
       AND REPLACE(citta, '''', '') = '$xcitta'
        AND xtravelrow.cd_Ar IN (
            SELECT cd_ar 
            FROM ar 
            WHERE Cd_ARClasse1 = 'TRV' 
              AND Cd_ARClasse2 IN ('BIG','TRA','VOL')
        )
        AND xtravelrow.cd_ar NOT IN (
            SELECT cd_ar FROM ar WHERE x_isacconto = 1
        )
)
SELECT 
    servizio,
    Cd_ARClasse123,
    SUM(qta) AS notti,
    AVG(prezzo) AS prezzo,
    SUM(tax_unit * qta) AS city_tax,
    SUM(imponibile) AS iimponibile,
    SUM(imposta) AS iva,
    SUM(prezzo * qta) + SUM(tax_unit * qta) AS totale_hotel,
    SUM(fee) AS fee,
    SUM(imponibile) + SUM(fee) + SUM(tax_unit * qta) AS fattura,
    SUM(x_pagato) AS pagato
FROM base
GROUP BY servizio, Cd_ARClasse123;

";
                    break;

                case 1:
                    $sql = "
 select ARClasse123.SottoClasse as servizio,ARClasse123.Cd_ARClasse123,sum(qta) as notti, avg(prezzo) as prezzo,
sum(tax_unit*qta) as city_tax,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as iimponibile,
sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
sum(prezzo*qta)+sum(tax_unit*qta) as totale_hotel,
sum(fee) as fee,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))
+sum(fee)+sum(tax_unit*qta)as fattura,
sum(x_pagato) as pagato
from xtravelrow
left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva
left join ar on ar.Cd_AR=xtravelrow.cd_Ar

left join ARClasse123 on ar.Cd_ARClasse123=ARClasse123.Cd_ARClasse123

where th_id=$model->th_id and (replace(citta,'''','')='$xcitta')

and xtravelrow.cd_Ar in (select cd_ar from ar where Cd_ARClasse1='TRV' 
 and Cd_ARClasse2 in ('BIG',
 'TRA',
 'VOL'
 
 )) and xtravelrow.cd_ar not in (select cd_ar from ar where x_isacconto=1)
group by ARClasse123.SottoClasse,ARClasse123.Cd_ARClasse123
";
                    break;

                case 2:
                    $sql = "
 select ARClasse123.SottoClasse as servizio,ARClasse123.Cd_ARClasse123,sum(qta) as notti, avg(prezzo) as prezzo,
sum(tax_unit*qta) as city_tax,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as iimponibile,
sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
sum(prezzo*qta)+sum(tax_unit*qta) as totale_hotel,
sum(fee) as fee,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))
+sum(fee)+sum(tax_unit*qta)as fattura,
sum(x_pagato) as pagato
from xtravelrow
left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva
left join ar on ar.Cd_AR=xtravelrow.cd_Ar

left join ARClasse123 on ar.Cd_ARClasse123=ARClasse123.Cd_ARClasse123

where th_id=$model->th_id and (sottocommessa='$xcitta')

and xtravelrow.cd_Ar in (select cd_ar from ar where Cd_ARClasse1='TRV' 
 and Cd_ARClasse2 in ('BIG',
 'TRA',
 'VOL'
 
 )) and xtravelrow.cd_ar not in (select cd_ar from ar where x_isacconto=1)
group by ARClasse123.SottoClasse,ARClasse123.Cd_ARClasse123
";
                case 3:
                    $sql = "
 select ARClasse123.SottoClasse as servizio,ARClasse123.Cd_ARClasse123,sum(qta) as notti, avg(prezzo) as prezzo,
sum(tax_unit*qta) as city_tax,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as iimponibile,
sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
sum(prezzo*qta)+sum(tax_unit*qta) as totale_hotel,
sum(fee) as fee,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))
+sum(fee)+sum(tax_unit*qta)as fattura,
sum(x_pagato) as pagato
from xtravelrow
left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva
left join ar on ar.Cd_AR=xtravelrow.cd_Ar

left join ARClasse123 on ar.Cd_ARClasse123=ARClasse123.Cd_ARClasse123

where th_id=$model->th_id and (sottocommessa='$xcitta')
and cd_cf_ft='$xcliente'
and xtravelrow.cd_Ar in (select cd_ar from ar where Cd_ARClasse1='TRV' 
 and Cd_ARClasse2 in ('BIG',
 'TRA',
 'VOL'
 
 )) and xtravelrow.cd_ar not in (select cd_ar from ar where x_isacconto=1)
group by ARClasse123.SottoClasse,ARClasse123.Cd_ARClasse123
";
                    break;
            }

            //yii::error('struttura'.$sql);

            $command->bindValue(':th_id', $model->th_id);
            $command->bindValue(':xcitta', $xcitta);
            $command = $db->createCommand($sql);
            $tot_mezzi = $command->queryAll();
            // yii::error($tot_mezzi);
            if ((str_replace("'", "", $value['citta'])) == 'GRADISCA DISONZO') {
                //   yii::error($tot_mezzi);
            }
            if (count($tot_mezzi) == 0) {
                switch ($tipo['x_tiposhow']) {
                    case null:
                        $sql = "
        select ARClasse123.SottoClasse as servizio,ARClasse123.Cd_ARClasse123,sum(qta) as notti, avg(prezzo) as prezzo,
       sum(tax_unit*qta) as city_tax,
       sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as iimponibile,
       sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
       sum(prezzo*qta)+sum(tax_unit*qta) as totale_hotel,
       sum(fee) as fee,
       sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))
       +sum(fee)+sum(tax_unit*qta)as fattura,
       sum(x_pagato) as pagato
       from xtravelrow
       left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva
       left join ar on ar.Cd_AR=xtravelrow.cd_Ar
       
       left join ARClasse123 on ar.Cd_ARClasse123=ARClasse123.Cd_ARClasse123
       
       where th_id=$model->th_id and (replace(citta_da,'''','')='$xcitta')
       
       and xtravelrow.cd_Ar in (select cd_ar from ar where Cd_ARClasse1='TRV' 
        and Cd_ARClasse2 in ('BIG',
        'TRA',
        'VOL'
        
        )) and xtravelrow.cd_ar not in (select cd_ar from ar where x_isacconto=1)
       group by ARClasse123.SottoClasse,ARClasse123.Cd_ARClasse123
       ";
                        break;
                    case 2:
                        $sql = "
        select ARClasse123.SottoClasse as servizio,ARClasse123.Cd_ARClasse123,sum(qta) as notti, avg(prezzo) as prezzo,
       sum(tax_unit*qta) as city_tax,
       sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as iimponibile,
       sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
       sum(prezzo*qta)+sum(tax_unit*qta) as totale_hotel,
       sum(fee) as fee,
       sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))
       +sum(fee)+sum(tax_unit*qta)as fattura,
       sum(x_pagato) as pagato
       from xtravelrow
       left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva
       left join ar on ar.Cd_AR=xtravelrow.cd_Ar
       
       left join ARClasse123 on ar.Cd_ARClasse123=ARClasse123.Cd_ARClasse123
       
       where th_id=$model->th_id and (sottocommessa='$xcitta')
       
       and xtravelrow.cd_Ar in (select cd_ar from ar where Cd_ARClasse1='TRV' 
        and Cd_ARClasse2 in ('BIG',
        'TRA',
        'VOL'
        
        )) and xtravelrow.cd_ar not in (select cd_ar from ar where x_isacconto=1)
       group by ARClasse123.SottoClasse,ARClasse123.Cd_ARClasse123
       ";
                        break;
                    case 3:
                        $sql = "
        select ARClasse123.SottoClasse as servizio,ARClasse123.Cd_ARClasse123,sum(qta) as notti, avg(prezzo) as prezzo,
       sum(tax_unit*qta) as city_tax,
       sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as iimponibile,
       sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
       sum(prezzo*qta)+sum(tax_unit*qta) as totale_hotel,
       sum(fee) as fee,
       sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))
       +sum(fee)+sum(tax_unit*qta)as fattura,
       sum(x_pagato) as pagato
       from xtravelrow
       left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva
       left join ar on ar.Cd_AR=xtravelrow.cd_Ar
       
       left join ARClasse123 on ar.Cd_ARClasse123=ARClasse123.Cd_ARClasse123
       
       where th_id=$model->th_id and (sottocommessa='$xcitta')
       and cd_cf_ft='$xcliente'
       and xtravelrow.cd_Ar in (select cd_ar from ar where Cd_ARClasse1='TRV' 
        and Cd_ARClasse2 in ('BIG',
        'TRA',
        'VOL'
        
        )) and xtravelrow.cd_ar not in (select cd_ar from ar where x_isacconto=1)
       group by ARClasse123.SottoClasse,ARClasse123.Cd_ARClasse123
       ";
                        break;
                }



                //  yii::error($sql);
                $command = $db->createCommand($sql);
                $tot_mezzi = $command->queryAll();
            }


            if (count($tot_mezzi) > 0) {


                $tmp = xLoadtappa2(
                    $model->th_id,
                    //$cliente
                    $r['cli'] ?? ($r['dettaglio'][0]['cd_cf_ft'] ?? null),
                    str_replace("'", "''", $value['citta']),
                    1,
                    $xcliente,
                    $tipo
                );
                $ddataProvider = new ArrayDataProvider([
                    'allModels' => $tmp['dettaglio'],
                    'pagination' => [
                        'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
                    ],
                    'sort' => [
                        'attributes' => ['x_scdesc', 'descli', 'struttura', 'guest'], // Attributi ordinabili
                    ],
                ]);
            ?>
                <h5 class="card-title" style="color: #938fa1;">Viaggi - Riepilogo</h5>
                <br>
                <br>

                <div style="max-height: 180px; overflow-y: auto;">
                    <table class="table">
                        <thead>
                            <TH class="table-totheadviaggi" width="15%">Servizi</TH>
                            <TH class="table-totheadviaggi" width="3%">Tkt</TH>
                            <TH class="table-totheadviaggi" width="7%">Impon.</TH>
                            <TH class="table-totheadviaggi" width="7%">Iva</TH>
                            <TH class="table-totheadviaggi" width="7%"> Pagato</TH>
                            <TH class="table-totheadviaggi" width="7%">Fee</TH>
                            <TH class="table-totheadviaggi" width="7%">Imp.Fatt</TH>
                            <th style="background-color:rgb(255, 255, 255); 
                                color: #002c48;font-size: 13px;  border:0px" width="2%"></th>
                            <TH class="table-totheadviaggi" width="7%">% Bgd</TH>
                            <TH class="table-totheadviaggi" width="7%">Media</TH>


                        <?php
                        //    <TH>Totale fattura</TH> 
                        /*$tqta=0;
$tprz=0;
$timp=0;
$tcity=0;
$tiva=0;
$tfat=0;*/

                        $total_fattura = array_sum(array_column($tot_mezzi, 'fattura'));
                        foreach ($tot_mezzi as $row) {
                            echo '<TR>';
                            echo '<td scope="col" class="table-info">';
                            $biglietto_tmpmodaln = str_replace('|', '_', str_replace(' ', '_', $row['servizio'] ?? 'N.D.')) . uniqid();
                            //  echo '<a href="#"                     data-toggle="modal" data-target="#biglietto_'.$biglietto_tmpmodaln.'">';
                            echo  $row['servizio'] ?? 'N.D.';
                            echo '</a>';


                            echo '</td>';
                            echo '<td scope="col" class="table-info">';
                            echo  $row['notti'];
                            $tqta = $tqta + $row['notti'];
                            $stqta = $stqta + $row['notti'];
                            echo '</td>';
                            echo '<td scope="col" class="table-info">';
                            echo '€' .  formatEuro($row['iimponibile']);
                            echo '</td>';

                            echo '<td scope="col" class="table-info">€ ';
                            echo  formatEuro($row['iva'], 2);
                            $tprz = $tprz + $row['prezzo'];
                            $stprz = $stprz + $row['prezzo'];
                            echo '</td>';
                            echo '<td scope="col" class="table-info">';
                            echo  formatEuro($row['pagato'], 2);
                            $timp = $timp + $row['iimponibile'];
                            $stimp = $stimp + $row['iimponibile'];

                            echo '</td>';
                            echo '<td scope="col" class="table-info">';
                            echo '€' .  formatEuro($row['fee']);
                            $tpag = $tpag + $row['pagato'];
                            $stpag = $stpag + $row['pagato'];
                            $stiva = $stiva + $row['iva'];
                            echo '</td>';
                            echo '<td scope="col" class="table-info">';
                            echo '€' .  formatEuro($row['fee'] + $row['iimponibile']);
                            $tfee = $tfee + $row['fee'];
                            $stfee = $stfee + $row['fee'];
                            echo '</td>';

                            echo '<td scope="col" class="table-info">';
                            echo '</td>';
                            echo '<td class="table-info" 
                                >';
                            if ($total_fattura > 0) {
                                $percentuale = ($row['fattura'] / $total_fattura) * 100;
                                echo number_format($percentuale, 2) . '%';
                            } else {
                                echo '0%';
                            }
                            echo '</td>';
                            echo '<td style="background-color:rgb(255, 255, 255)" >€';
                            echo  formatEuro($row['prezzo'], 2);
                            echo '</td>';
                            echo '</tr>';
                        }
                        echo '<tfoot>';
                        echo '<tr>';
                        echo '</tr>';
                        echo '</tfoot>';
                        echo '<td scope="col" class="table-totheadviaggift"><strong>Totali</strong>';
                        echo '</td>';

                        echo '<td scope="col" class="table-totheadviaggift"><strong>' . $stqta;
                        echo '</strong></td>';
                        echo '<td scope="col" class="table-totheadviaggift"><strong>€' . formatEuro($stimp);
                        echo '</strong></td>';
                        echo '<td scope="col" class="table-totheadviaggift"><strong>€' . formatEuro($stiva);
                        echo '</strong></td>';
                        echo '<td scope="col" class="table-totheadviaggift"><strong>€' . formatEuro($stpag);
                        echo '</strong></td>';
                        echo '<td scope="col" class="table-totheadviaggift"><strong>€' . formatEuro($stfee);
                        echo '</strong></td>';
                        echo '<td scope="col" class="table-totheadviaggift"><strong>€' . formatEuro($stfee + $stimp);
                        echo '</strong></td>';
                        echo '<td scope="col" style="background-color:rgb(255, 255, 255)"> ';
                        echo '</td>';
                        echo '<td class="table-totheadviaggift">';
                        echo '</td>';
                        echo '<td class="table-totheadviaggift"><strong>€'
                            . formatEuro(($stpag) / (($stqta <> 0) ? $stqta : 1)) . "</strong>";
                        echo '</td>';



                        echo '</tfoot>';




                        echo     "";
                    }

                        ?>
                    </table>
                </div>
        </div>
    </div>




</div>

<br>


<style>
    .expand-row-container {
        max-height: 300px;
        /* Adatta in base alle necessità */
        overflow-y: auto;
    }
</style>


<?php


// Definisci le colonne che desideri esportare

$zgridColumns = [
    [
        'attribute' => 'x_scdesc',
        'label' => 'Commessa',
        'group' => true,
    ],
    [
        'attribute' => 'descli',
        'label' => 'Cliente',
        'group' => true,
        'subGroupOf' => 0,
    ],
    [
        'attribute' => 'struttura',
        'label' => 'Struttura',
        'group' => true,
        'subGroupOf' => 1,
    ],
    [
        'attribute' => 'guest',
        'label' => 'Ospite',
    ],
    [
        'attribute' => 'ruolo',
        'label' => 'Ruolo',
    ],


    [
        'attribute' => 'check_in',
        'label' => 'Data Check-in',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'attribute' => 'check_out',
        'label' => 'Data Check-out',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'attribute' => 'cd_ar',
        'label' => 'articolo',
    ],
    [
        'attribute' => 'qta',
        'label' => 'Quantità',
        'format' => ['integer'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'prezzo',
        'label' => 'Prezzo per Notte',
        //   'format' => ['decimal', 2], // Visualizzazione standard in HTML
        'pageSummary' => true,
        //   'xlSFormat' => '#,##0.00_-"€"', // Formattazione specifica per Excel
    ],
    [
        'attribute' => 'tax_unit',
        'label' => 'Tassa per Unità',
        //   'format' => ['decimal', 2], // Visualizzazione standard in HTML
        'pageSummary' => true,
        //  'xlSFormat' => '#,##0.00_-"€"', // Formattazione specifica per Excel
    ],
    [
        'attribute' => 'totale',
        'label' => 'Totale',
        //  'format' => ['decimal', 2], // Visualizzazione standard in HTML
        'pageSummary' => true,
        //   'xlSFormat' => '#,##0.00_-"€"', // Formattazione specifica per Excel
    ],
    [
        'attribute' => 'tax',
        'label' => 'Tassa',
        //   'format' => ['decimal', 2], // Visualizzazione standard in HTML
        'pageSummary' => true,
        //   'xlSFormat' => '#,##0.00_-"€"', // Formattazione specifica per Excel
    ],
    [
        'attribute' => 'fee',
        'label' => 'Fee',
        //  'format' => ['decimal', 2], // Visualizzazione standard in HTML
        'pageSummary' => true,
        //    'xlSFormat' => '#,##0.00_-"€"', // Formattazione specifica per Excel
    ],
    [
        'attribute' => 'iva',
        'label' => 'IVA',
        //  'format' => ['decimal', 2], // Visualizzazione standard in HTML
        'pageSummary' => true,
        //     'xlSFormat' => '#,##0.00_-"€"', // Formattazione specifica per Excel
    ],
];





// Crea il menu di esportazione
//yii::error($ddataProvider);
$exportConfig = [
    ExportMenu::FORMAT_EXCEL => [
        'label' => 'Excel',
        'filename' => 'Export_Excel_' . date('Y-m-d_H-i-s'),
        'alertMsg' => 'Il file Excel verrà generato per il download.',
        'options' => ['title' => 'Esporta in Excel'],
    ],
    ExportMenu::FORMAT_CSV => [
        'label' => 'CSV',
        'filename' => 'Export_CSV_' . date('Y-m-d_H-i-s'),
        'alertMsg' => 'Il file CSV verrà generato per il download.',
        'options' => ['title' => 'Esporta in CSV'],
    ],
    ExportMenu::FORMAT_TEXT => [
        'label' => 'Text',
        'filename' => 'Export_Text_' . date('Y-m-d_H-i-s'),
        'alertMsg' => 'Il file Text verrà generato per il download.',
        'options' => ['title' => 'Esporta in Text'],
    ],
    // Disabilita altri formati se non necessari
    ExportMenu::FORMAT_PDF => false,
    ExportMenu::FORMAT_HTML => false,
];
$defaultStyle = [
    'borders' => [
        'outline' => [
            //'//borderStyle' => Border::BORDER_MEDIUM,
            'color' => ['argb' => 'black'],
        ],
        'inside' => [
            //   'borderStyle' => Border::BORDER_DOTTED,
            'color' => ['argb' => 'BLACK'],
        ]
    ],
];

?>
<div id="<?php echo uniqid(); ?>">
    <?php

    //ob_clean(); // Pulisci il buffer
    echo ExportMenu::widget([
        'dataProvider' => $ddataProvider,
        'columns' => $zgridColumns,
        //  'id' => 'exp_button'.uniqid(),
        'target' => ExportMenu::TARGET_SELF,
        'filename' => 'Export_' . date('Y-m-d_H-i-s'),
        //  'pjaxContainerId' => 'pjax-container',
        'showConfirmAlert' => false,
        'clearBuffers' => true,
        'exportConfig' => $exportConfig,

        'dropdownOptions' => [
            'label' => 'Esporta Dati',
        ],
        'enableFormatter' => false,
        // --- AGGIUNGI DA QUI ---
        'onRenderDataCell' => function ($cell, $content, $model, $key, $index, $widget) {
            // Elenco delle colonne che devono essere numeriche/valuta
            $colonneMonetarie = ['prezzo', 'tax_unit', 'totale', 'tax', 'fee', 'iva'];

            // Se la colonna corrente è nell'elenco, forziamo il formato Excel
            if (
                isset($widget->attribute) &&
                in_array($widget->attribute, $colonneMonetarie)
            ) {
                return [
                    'styleOptions' => [
                        'numberFormat' => [
                            'formatCode' => '_-€* #,##0.00_-;-€* #,##0.00_-;_-€* "-"??_-;_-@_-'
                        ]
                    ]
                ];
            }
        },
    ]);



    ?>

</div>


<?php

//tot_hotel

if (count($tot_hotel) > 0) {
    $txid = $model->th_id;
    $hoteldt = new ArrayDataProvider([
        'allModels' => $tot_hotel,  // Usa l'array come sorgente dati
        'pagination' => [
            'pageSize' => 1000,  // Imposta il numero di righe per pagina
        ],

    ]);
    $value = $value ?? '';
    $gridhotel = [
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '50px',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            // uncomment below and comment detail if you need to render via ajax
            // 'detailUrl' => Url::to(['/site/book-details']),
            'detail' => function ($model, $key, $index, $column)
            use ($tmp, $value, $txid, $r, $tipo) {
                // return Yii::$app->controller->renderPartial
                // ('_expand-row-details', ['model' => $model]);

                //      $connection = Yii::$app->db5;
                //      $command = $connection->createCommand(
                //          "SELECT x_tiposhow FROM xtravelhead WHERE th_id = :id"
                //      );
                //      $tipo = $command->bindValue(':id', $txid)->queryOne();

                $tmp = xLoadtappa2h(
                    $txid,
                    //$cliente
                    $r['cli'] ?? ($r['dettaglio'][0]['cd_cf_ft'] ?? null),
                    str_replace("'", "''", $value['citta']),
                    null,
                    null,
                    $tipo
                );

                $ddataProvider = new ArrayDataProvider([
                    'allModels' => $tmp['dettaglio'],
                    'pagination' => [
                        'pageSize' => false,
                        // Puoi modificare la paginazione a tuo piacimento
                    ],
                    'sort' => [
                        'attributes' => ['x_scdesc', 'descli', 'struttura', 'guest'], // Attributi ordinabili
                    ],
                ]);


                return   $this->render('_ddetail_gridh', [
                    'xttmp' => $tmp['dettaglio'],
                    'value' => $value,
                    'xddataProvider' => $ddataProvider,
                    'struttura' => $model['struttura'],
                    'txid' => $txid
                ]);
            },
            'headerOptions' => [
                'class' => 'my-header-class',
            ],
            'pageSummaryOptions' => [
                'class' => 'my-summary-class',
            ],
            'expandOneOnly' => true
        ],

        [
            'attribute' => 'struttura',
            'label' => 'Struttura',
            'group' => true,
            'subGroupOf' => 1,
            'headerOptions' => [
                'class' => 'my-header-class',
            ],
            'pageSummaryOptions' => [
                'class' => 'my-summary-class',
            ],
            'value' => function ($model, $key, $index, $column) {

                if (is_numeric($model['struttura'])) {
                    $ris = XStruttura::find()
                        ->select(['struttura'])
                        ->where(['id' => $model['struttura']])->scalar();
                    // yii::error(($ris));
                }

                return $ris ?? $model['struttura'];
            },


        ],
        [
            'attribute' => 'notti',
            'label' => 'Notti',
            'format' => ['integer'],
            'pageSummary' => true,
            'headerOptions' => [
                'class' => 'my-header-class',
            ],
            'pageSummaryOptions' => [
                'class' => 'my-summary-class',
            ],
        ],

        [
            'attribute' => 'iimponibile',
            'label' => 'Imp. Hotel',
            'format' => ['currency', 'EUR'],
            'pageSummary' => true,
            'headerOptions' => [
                'class' => 'my-header-class',
            ],
            'pageSummaryOptions' => [
                'class' => 'my-summary-class',
            ],
        ],
        [
            'attribute' => 'city_tax',
            'label' => 'City Tax',
            'format' => ['currency', 'EUR'],
            'pageSummary' => true,
            'headerOptions' => [
                'class' => 'my-header-class',
            ],
            'pageSummaryOptions' => [
                'class' => 'my-summary-class',
            ],
        ],
        [
            'attribute' => 'iva',
            'label' => 'Iva Hotel',
            'format' => ['currency', 'EUR'],
            'pageSummary' => true,
            'headerOptions' => [
                'class' => 'my-header-class',
            ],
            'pageSummaryOptions' => [
                'class' => 'my-summary-class',
            ],
        ],
        [
            'attribute' => 'pagato',
            'label' => 'Totale Pagato',
            'format' => ['currency', 'EUR'],
            'pageSummary' => true,
            'headerOptions' => [
                'class' => 'my-header-class',
            ],
            'pageSummaryOptions' => [
                'class' => 'my-summary-class',
            ],
            'value' => function ($model, $key, $index, $column) {
                return ($model['iimponibile'] + $model['iva'] + $model['city_tax']) ?? 0;
            }
        ],
        [
            'attribute' => 'fee',
            'label' => 'Fee',
            'format' => ['currency', 'EUR'],
            'pageSummary' => true,
            'headerOptions' => [
                'class' => 'my-header-class',
            ],
            'pageSummaryOptions' => [
                'class' => 'my-summary-class',
            ],
        ],
        [
            'attribute' => 'prezzo',
            'label' => 'Prezzo medio per Notte',
            'format' => ['currency', 'EUR'],
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_AVG,
            'headerOptions' => [
                'class' => 'my-header-class',
            ],
            'pageSummaryOptions' => [
                'class' => 'my-summary-class',
            ],
        ],



    ];

    echo
    GridView::widget([
        'dataProvider' => $hoteldt,
        // 'filterModel' => $searchModel,
        'id' => 'gridhotel' . $model->th_id . preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']) . uniqid(),
        // 'resizableColumnsOptions' => ['resizeFromBody' => true],
        'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
        'striped' => true,
        'condensed' => true,
        'columns' => $gridhotel,
        'persistResize' => true,
        'toolbar' => [
            '{toggleData}',
            // $fullExportMenu,
            ['content' =>
            Html::a('<i class="fas fa-redo"></i>', [''], [
                'class' => 'btn btn-outline-secondary btn-default',
                'title' => Yii::t('kvgrid', 'Reset Grid'),
                'data-pjax' => 0,
            ])],

        ],
        'headerRowOptions' => ['class="table-tothead"'],
        'panel' => [
            'type' => $ris['grid_color'] ?? '',
            'heading' => '<i class="fas  fa-book"> Hotel</i>',
            'headingOptions' => ['language' => 'it-It'],
            /*'heading'=>'<h3 class="panel-title"><i class="fas fa-globe"></i> Countries</h3>',
    'type'=>'success',
    'before'=>Html::a('<i class="fas fa-plus"></i> Create Country', ['create'], ['class' => 'btn btn-success']),
    'after'=>Html::a('<i class="fas fa-redo"></i> Reset Grid', ['index'], ['class' => 'btn btn-info']),
    'footer'=>false
     */
        ],
        'responsive' => true,
        'resizableColumns' => true,
        'showPageSummary' => true,
        'pjax' => true,
        'floatHeader' => true,     // fissa le intestazioni
        'floatHeaderOptions' => ['top' => 50], // offset se hai una navbar fissa
        'containerOptions' => ['style' => 'max-height:300px; overflow-y:auto;'], // scroll
    ]);
}




if (count($tot_mezzi) > 0) {
    $txid = $model->th_id;
    $mezzidt = new ArrayDataProvider([
        'allModels' => $tot_mezzi,  // Usa l'array come sorgente dati
        'pagination' => [
            'pageSize' => 1000,  // Imposta il numero di righe per pagina
        ],

    ]);
    $value = $value ?? '';
    $gridmezzi = [

        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'width' => '50px',
            'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
            },
            // uncomment below and comment detail if you need to render via ajax
            // 'detailUrl' => Url::to(['/site/book-details']),
            'detail' => function ($model, $key, $index, $column)
            use ($tmp, $value, $ddataProvider, $txid) {
                // return Yii::$app->controller->renderPartial
                // ('_expand-row-details', ['model' => $model]);
                return   $this->render('_ddetail_grids', [
                    'xttmp' => $tmp['dettaglio'],
                    'value' => $value,
                    'xddataProvider' => $ddataProvider,
                    'servizio' => $model['servizio'] ?? 'N.D.',
                    'txid' => $txid
                ]);
            },
            'headerOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px;',
            ],
            'pageSummaryOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px; font-weight: bold;',
            ],
            'expandOneOnly' => true
        ],

        [
            'attribute' => 'servizio',
            'label' => 'Cat.Servizio',
            'headerOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px;',
            ],
            'pageSummaryOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px; font-weight: bold;',
            ],
        ],
        [
            'attribute' => 'notti',
            'label' => 'Emissioni',
            'format' => ['integer'],
            'pageSummary' => true,
            'headerOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px;',
            ],
            'pageSummaryOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px; font-weight: bold;',
            ],
        ],


        [
            'attribute' => 'iimponibile',
            'label' => 'Imp. Serivizi',
            'format' => ['currency', 'EUR'],
            'pageSummary' => true,
            'headerOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px;',
            ],
            'pageSummaryOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px; font-weight: bold;',
            ],
        ],

        [
            'attribute' => 'iva',
            'label' => 'Iva Servizi',
            'format' => ['currency', 'EUR'],
            'pageSummary' => true,
            'headerOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px;',
            ],
            'pageSummaryOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px; font-weight: bold;',
            ],
        ],
        [
            'attribute' => 'pagato',
            'label' => 'Totale Pagato',
            'format' => ['currency', 'EUR'],
            'pageSummary' => true,
            'headerOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px;',
            ],
            'pageSummaryOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px; font-weight: bold;',
            ],
        ],
        [
            'attribute' => 'fee',
            'label' => 'Fee',
            'format' => ['currency', 'EUR'],
            'pageSummary' => true,
            'headerOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px;',
            ],
            'pageSummaryOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px; font-weight: bold;',
            ],
        ],
        [
            'label' => 'Prezzo Medio',
            'attribute' => 'prezzo',
            'format' => ['currency', 'EUR'],
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_AVG,
            'headerOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px;',
            ],
            'pageSummaryOptions' => [
                'style' => 'background-color: #f1eef6; color: #002c48; font-size: 13px; font-weight: bold;',
            ],
        ],
    ];
    echo
    GridView::widget([
        'dataProvider' => $mezzidt,
        'filterModel' => null,
        'id' => 'gridmezzi' . $model->th_id . uniqid(),
        'striped' => true,
        'condensed' => true,
        'columns' => $gridmezzi,
        'persistResize' => false,
        'resizableColumns' => true,

        'toolbar' => ['{toggleData}'],
        'panel' => [
            'type' => $ris['grid_color'] ?? '',
            'heading' => '<i class="fas  fa-ticket"> Trasporti</i>',
        ],
        'responsive' => true,
        'showPageSummary' => true,
        'floatHeader' => true,     // fissa le intestazioni
        'floatHeaderOptions' => ['top' => 50], // offset se hai una navbar fissa
        'containerOptions' => ['style' => 'max-height:300px; overflow-y:auto;'], // scroll
        //'pjax' => true, // Se usi PJAX, prova a disabilitarlo per testare il resize
    ]);
}

?>
<style>
    .detail-content {
        padding: 15px;
        /* background-color: #f8f9fa;*/
        border: 1px solid #dee2e6;
        margin: 10px 0;
        border-radius: 4px;
    }

    .detail-trigger {
        /* color:rgb(247, 247, 247);*/
        text-decoration: none;
    }

    .detail-trigger:hover {
        text-decoration: underline;
        cursor: pointer;
    }
</style>


<?php
$ter = preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']);
/*<style>
    .detail-content {
        padding: 15px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        margin: 10px 0;
        border-radius: 4px;
    }

    .detail-trigger {
        color: #007bff;
        text-decoration: none;
    }

    .detail-trigger:hover {
        text-decoration: underline;
        cursor: pointer;
    }
</style>
*/
?>
<?php



$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.copy.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);





if ((Yii::$app->user->identity->level ?? 0) >= 80):



    $txid = $model->th_id;

    $currentCitta = $value['citta'];

    $groups = [];



    // --- 1. CARICAMENTO DATI ---

    $dettagliHotel = xLoadtappa2h($txid, null, str_replace("'", "''", $currentCitta), null, null, $tipo);

    $dettagliViaggi = xLoadtappa2($txid, null, str_replace("'", "''", $currentCitta), 1, null, $tipo);



    // --- 2. ELABORAZIONE HOTEL ---

    if (isset($dettagliHotel['dettaglio']) && is_array($dettagliHotel['dettaglio'])) {

        foreach ($dettagliHotel['dettaglio'] as $d) {

            if ($d['citta'] == $currentCitta && !empty($d['struttura'])) {

                $gn = 'HOTEL: ' . $d['struttura'];


                // INIZIALIZZAZIONE: Aggiunte le chiavi tot_qta e tot_iva qui
                if (!isset($groups[$gn])) {
                    $groups[$gn] = [
                        'icon' => '<i class="fas fa-home text-primary"></i>',
                        'rows' => [],
                        'tot_imp' => 0,
                        'tot_tax' => 0,
                        'tot_fee' => 0,
                        'tot_gen' => 0,
                        'tot_qta' => 0, // <--- Importante
                        'tot_iva' => 0  // <--- Importante
                    ];
                }


                $imp = (float)$d['imponibile'];

                $tax = (float)($d['tax'] ?? $d['city_tax'] ?? 0);

                $fee = (float)($d['fee'] ?? 0);

                $gen = (float)($d['Totalegenerale'] ?? $d['totale'] ?? 0);

                $iva = (float)($d['iva'] ?? 0); // Recupero IVA
                $qta = (int)($d['qta'] ?? 0);   // Recupero QTA

                $groups[$gn]['rows'][] = [

                    'ospite' => $d['guest'],

                    'ruolo' => $d['ruolo'],

                    'desc' => $d['descrizione'],

                    'd1' => !empty($d['check_in']) ? date('d/m/Y', strtotime($d['check_in'])) : '',

                    'd2' => !empty($d['check_out']) ? date('d/m/Y', strtotime($d['check_out'])) : '',



                    'prezzo' => (float)($d['prezzo'] ?? 0),

                    'imp' => $imp,

                    'tax' => $tax,

                    'fee' => $fee,

                    'qta' => $qta,
                    'iva' => $iva,

                    'tot' => $gen,

                    'gruppa' => 'HOTEL: ' . $d['struttura']

                ];

                $groups[$gn]['tot_imp'] += $imp;

                $groups[$gn]['tot_tax'] += $tax;

                $groups[$gn]['tot_fee'] += $fee;

                $groups[$gn]['tot_gen'] += $gen;
                $groups[$gn]['tot_qta'] += $qta; // Somma QTA
                $groups[$gn]['tot_iva'] += $iva; // Somma IVA
                //  $groups[$gn]['tot_qta'] += (int)($d['qta'] ?? 1);
            }
        }
    }



    // --- 3. ELABORAZIONE VIAGGI (Anti-duplicazione Kitty) ---

    if (isset($dettagliViaggi['dettaglio']) && is_array($dettagliViaggi['dettaglio'])) {

        foreach ($dettagliViaggi['dettaglio'] as $d) {

            $isHotel = !empty($d['struttura']) || strpos($d['cd_Ar'] ?? '', 'HOT') !== false;

            if ($d['citta'] == $currentCitta && !$isHotel) {

                $gn = 'VIAGGIO: ' . ($d['descrizione'] ?? 'Trasporto');
                // INIZIALIZZAZIONE: Aggiunte le chiavi tot_qta e tot_iva qui
                if (!isset($groups[$gn])) {
                    $groups[$gn] = [
                        'icon' => '<i class="fas fa-home text-primary"></i>',
                        'rows' => [],
                        'tot_imp' => 0,
                        'tot_tax' => 0,
                        'tot_fee' => 0,
                        'tot_gen' => 0,
                        'tot_qta' => 0, // <--- Importante
                        'tot_iva' => 0  // <--- Importante
                    ];
                }

                $imp = (float)$d['imponibile'];

                $fee = (float)($d['fee'] ?? 0);

                $gen = (float)($d['Totalegenerale'] ?? $d['totale'] ?? 0);

                $iva = (float)($d['iva'] ?? 0);
                $qta = (int)($d['qta'] ?? 1);

                $groups[$gn]['rows'][] = [

                    'ospite' => $d['guest'],

                    'ruolo' => $d['ruolo'],

                    'desc' => $d['x_scdesc'],

                    'd1' => !empty($d['citta_da']) ? date('d/m/Y', strtotime($d['check_in'])) : '',

                    'd2' => ($d['citta_a'] ?? '') . ' > ' . (date('d/m/Y', strtotime($d['check_out'])) ?? ''),



                    'prezzo' => (float)($d['prezzo'] ?? 0),

                    'imp' => $imp,

                    'tax' => 0,

                    'fee' => $fee,

                    'qta' => $qta,
                    'iva' => $iva,

                    'tot' => $gen,

                    'gruppa' => 'VIAGGIO: ' . $d['descrizione']

                ];
                //      $groups[$gn]['tot_qta'] += (int)($d['qta'] ?? 1);
                $groups[$gn]['tot_imp'] += $imp;

                $groups[$gn]['tot_fee'] += $fee;

                $groups[$gn]['tot_gen'] += $gen;
                $groups[$gn]['tot_qta'] += $qta; // Somma QTA
                $groups[$gn]['tot_iva'] += $iva; // Somma IVA
            }
        }
    }



    $dtId = "table_verified_" . uniqid();

?>


    <style>
        .hidden-row {
            display: none !important;
        }

        .group-header {
            transition: background-color 0.2s;
        }

        .group-header:hover {
            background-color: #e9ecef !important;
        }
    </style>
    <div class="card shadow-sm mt-3">

        <div class="card-body p-0">
            <div class="mb-2">
                <button type="button" id="btn-expand-<?= $dtId ?>" class="btn btn-outline-secondary btn-xs">
                    <i class="fas fa-plus-square"></i> Espandi tutto
                </button>
                <button type="button" id="btn-collapse-<?= $dtId ?>" class="btn btn-outline-secondary btn-xs">
                    <i class="fas fa-minus-square"></i> Comprimi tutto
                </button>
            </div>
            <table id="<?= $dtId ?>" class="table table-bordered table-sm mb-0" style="width:100%; font-size: 0.75rem;">

                <thead class="bg-dark text-white">

                    <tr>

                        <th></th>

                        <th style="min-width: 250px;">Struttura/Servizio</th>

                        <th>Ospite / Ruolo</th>

                        <th>Descrizione</th>

                        <th>In / Part.</th>

                        <th>Out / Tratta</th>

                        <th>Q.tà</th>

                        <th>Prezzo</th>

                        <th class="text-right">Imponibile</th>

                        <th class="text-right">Tax</th>

                        <th class="text-right">Fee</th>

                        <th class="text-right">IVA</th>

                        <th class="text-right">Totale</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($groups as $groupName => $data): ?>
                        <?php
                        // Generiamo uno slug (un identificativo pulito) per il gruppo per usarlo come classe CSS
                        $groupSlug = 'group_' . preg_replace('/[^a-z0-9]/i', '_', $groupName);
                        ?>
                        <tr class="bg-light font-weight-bold group-header"
                            data-target=".<?= $groupSlug ?>"
                            style="cursor: pointer;"
                            title="Clicca per espandere/comprimere">

                            <td class="text-center"><?= $data['icon'] ?></td>

                            <td class="text-primary"><?= Html::encode($groupName) ?></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td></td>

                        </tr>



                        <?php foreach ($data['rows'] as $r): ?>

                            <tr class="<?= $groupSlug ?> detail-row hidden-row">

                                <td class="text-center"><?= $data['icon'] ?></td>

                                <td style="width: 200px;"><strong><?= Html::encode($r['gruppa']) ?></strong></td>

                                <td><strong><?= Html::encode($r['ospite']) ?></strong><br><small><?= $r['ruolo'] ?></small></td>

                                <td><?= Html::encode($r['desc']) ?></td>

                                <td><?= $r['d1'] ?></td>

                                <td><?= $r['d2'] ?></td>

                                <td class="text-center"><?= $r['qta'] ?></td>

                                <td class="text-right"><?= number_format($r['prezzo'], 2, ',', '.') ?></td>

                                <td class="text-right">€ <?= number_format($r['imp'], 2, ',', '.') ?></td>

                                <td class="text-right">€ <?= number_format($r['tax'], 2, ',', '.') ?></td>

                                <td class="text-right">€ <?= number_format($r['fee'], 2, ',', '.') ?></td>

                                <td class="text-right">€<?= number_format($r['iva'], 2, ',', '.') ?></td>

                                <td class="text-right font-weight-bold">€ <?= number_format($r['tot'], 2, ',', '.') ?></td>

                            </tr>

                        <?php endforeach; ?>



                        <tr class="table-info font-weight-bold <?= $groupSlug ?> hidden-row">

                            <td></td>

                            <td style="width: 200px;"><?= Html::encode($groupName) ?></td>



                            <td class="text-right text-uppercase">Totale Sezione:</td>

                            <td></td>

                            <td></td>

                            <td></td>

                            <td class="text-center"><?= $data['tot_qta'] ?></td>

                            <td></td>

                            <td class="text-right">€ <?= number_format($data['tot_imp'], 2, ',', '.') ?></td>

                            <td class="text-right">€ <?= number_format($data['tot_tax'], 2, ',', '.') ?></td>

                            <td class="text-right">€ <?= number_format($data['tot_fee'], 2, ',', '.') ?></td>

                            <td class="text-right">€ <?= number_format($data['tot_iva'], 2, ',', '.') ?></td>
                            <?php yii::warning($data); ?>
                            <td class="text-right text-danger">€ <?= number_format($data['tot_gen'], 2, ',', '.') ?></td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>



    <script>
        $(document).ready(function() {
            var tableId = '#<?= $dtId ?>';

            if ($.fn.DataTable.isDataTable(tableId)) {
                $(tableId).DataTable().destroy();
            }

            var table = $(tableId).DataTable({
                "autoWidth": false,
                "columnDefs": [{
                    "width": "250px",
                    "targets": 1
                }],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Italian.json"
                },
                "paging": false,
                "ordering": false,
                "info": false,
                "dom": 'fBrt',
                "buttons": [{
                        extend: 'copy',
                        className: 'btn btn-secondary btn-sm',
                        text: '<i class="fas fa-copy"></i> Copia'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-success btn-sm',
                        text: '<i class="fas fa-file-excel"></i> Excel'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        text: '<i class="fas fa-file-pdf"></i> PDF'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-info btn-sm',
                        text: '<i class="fas fa-print"></i> Stampa'
                    }
                ]
            });

            table.on('search.dt', function() {
                var searchTerm = table.search();
                var $currentTable = $('#<?= $dtId ?>'); // Selezioniamo solo QUESTA tabella

                // Reset: nascondiamo solo i dettagli di QUESTA tabella
                $currentTable.find('.detail-row, .table-info').addClass('hidden-row');

                if (searchTerm !== "") {
                    // Mostriamo solo i risultati filtrati di QUESTA tabella
                    table.rows({
                        search: 'applied'
                    }).every(function() {
                        var $row = $(this.node());
                        if ($row.hasClass('detail-row') || $row.hasClass('table-info')) {
                            $row.removeClass('hidden-row');
                        }
                    });
                }
            });
            // Espandi tutto (ID dinamico)
            $('#btn-expand-<?= $dtId ?>').on('click', function() {
                $('#<?= $dtId ?>').find('.detail-row, .table-info').removeClass('hidden-row');
            });

            // Comprimi tutto (ID dinamico)
            $('#btn-collapse-<?= $dtId ?>').on('click', function() {
                var $table = $('#<?= $dtId ?>');
                $table.find('.detail-row, .table-info').addClass('hidden-row');

                // Puliamo la ricerca specifica di questa tabella
                if ($.fn.DataTable.isDataTable('#<?= $dtId ?>')) {
                    $('#<?= $dtId ?>').DataTable().search('').draw();
                }
            });
            // Gestione Click originale (corretta)
            $(document).off('click', '.group-header').on('click', '.group-header', function() {
                var selector = $(this).attr('data-target');
                $(selector).toggleClass('hidden-row');
            });
        });
    </script>

<?php endif; ?>