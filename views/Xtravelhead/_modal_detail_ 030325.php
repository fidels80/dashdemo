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
//use yii\data\ArrayDataProvider;
$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['grid_color', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrgrid = $ris['grid_color'] ?? '';
$filteredData=[];
?>

 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 

    <!-- Carica jQuery (PRIMA di DataTables) -->

</head>



<div class="d-flex flex-wrap justify-content-center align-items-start">
    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                Tappa   <?php echo $value['citta'] ?></h5>

            <table class="table  table-sm table-hover table-responsive-sm table-fit">
                <thead class="thead-dark">
                    <TH>Commessa</TH>
                    <TH>CLiente</TH>
                    <TH>Data Inizio</TH>
                    <TH>Data Fine</TH>
                    <TH>Totale</TH>
                    <TH>Tassa</TH>
                    <TH>Fee</TH>
                    <TH>Totale Generale</TH>
                </thead> 
      <?php
                $tottassa = 0;
                $totfee = 0;
                $tottalecard = 0;
                $totgen=0;
$r = xxLoadtappa2($model->th_id, null , $value['citta']);
 
               $tmp=xLoadtappa2($model->th_id, 
               //$cliente
               $r['cli'] ?? ($r['dettaglio'][0]['cd_cf_ft'] ?? null)
               , $value['citta']);
$ddataProvider = new ArrayDataProvider([
    'allModels' => $tmp['dettaglio'],
    'pagination' => [
        'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
    ],
    'sort' => [
        'attributes' => ['x_scdesc', 'descli', 'struttura', 'guest'], // Attributi ordinabili
    ],
]);
               foreach ($tmp['totale'] as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    echo $row['x_scdesc'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['descli'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo date('d/m/Y', strtotime($row['startdate']));
                    echo '</td>';
                    echo '<td scope="col">';
                    echo date('d/m/Y', strtotime($row['enddate']));
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo formatEuro($row['totale'], 2);
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo formatEuro($row['tassa'], 2);
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo formatEuro($row['fee'], 2);
                    echo '</td>';
                    echo '<td scope="col">€';
                    echo formatEuro($row['totalegenerale'], 2);
                    echo '</td>';
                    echo '</tr>';
                    $tottassa = $tottassa + $row['tassa'];
                    $totfee = $totfee + $row['fee'];
                    $tottalecard = $tottalecard + $row['totale'];
                    $totgen=$totgen+$row['totalegenerale'];
                }
                echo '<TR>';
                echo '<td scope="col" colspan=4  style="font-size: 16px;">';
                echo '<strong>TOTALE GENERALE</strong>';
                echo '</td>';
                echo '<td scope="col" style="font-size: 16px;">';
                echo '<strong>€' . formatEuro($tottalecard) . '</strong>';
                echo '</td>';
                echo '<td scope="col" style="font-size: 16px;">';
                echo '<strong>€' . formatEuro($tottassa) . '</strong>';
                echo '</td>';
                echo '<td scope="col" style="font-size: 16px;">';
                echo '<strong>€' . formatEuro($totfee) . '</strong>';
                echo '</td>';
    echo '<td scope="col" style="font-size: 16px;">';
    echo '<strong>€' . formatEuro($totgen) . '</strong>';
    echo '</td>';
                echo '</tr>'

                ?>

            </table>
        </div>
    </div>


    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title"> Riepilogo Costi</h5>

            
 
                </thead>
            </table>
<br>
 
   <?php     
   $tqta=0;
$tprz=0;
$timp=0;
$tcity=0;
$tiva=0;
$tfat=0;
$tpag=0;
$tfee=0;


  $dett= $tmp['dettaglio'];
 $db = Yii::$app->db5;
 $xcitta=$value['citta'];
 $command = $db->createCommand("
 select struttura,sum(qta) as notti, avg(prezzo) as prezzo,
sum(tax_unit*qta) as city_tax,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as iimponibile,
sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
sum(prezzo*qta)+sum(tax_unit*qta) as totale_hotel,
sum(fee) as fee,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))
+sum(fee)+sum(tax_unit*qta)as fattura
from xtravelrow
left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva

where th_id=$model->th_id and citta='$xcitta'
 and cd_ar in (select cd_ar from ar where Cd_ARClasse1='TRV' and Cd_ARClasse2='ACC')
 and cd_ar not in (select cd_ar from ar where x_isacconto=1)
group by struttura");


$thml=<<<EOF
  <h5 class="card-title"> Hotel</h5>
<table class="table  table-sm table-hover table-responsive-sm table-fit">
     <thead class="thead-dark">
                    <TH>Struttura</TH>
                    <TH>Camere</TH>
                    <TH>% Bgd</TH>
                    <TH>Costo Medio</TH>
                    <TH>Imp. Hotel</TH>
                    <TH>City Tax</TH>
                    <TH>Iva Hotel</TH>
                    <TH>Totale Hotel</TH> 
                       <TH>Fee</TH> 
                      <TH>Tot Fattura</TH> 

EOF;
$tot_hotel = $command->queryAll();
if (count($tot_hotel)>0){

               $tmp=xLoadtappa2h($model->th_id, 
               //$cliente
               $r['cli'] ?? ($r['dettaglio'][0]['cd_cf_ft'] ?? null)
               , $value['citta']);
               
$ddataProvider = new ArrayDataProvider([
    'allModels' => $tmp['dettaglio'],
    'pagination' => [
        'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
    ],
    'sort' => [
        'attributes' => ['x_scdesc', 'descli', 'struttura', 'guest'], // Attributi ordinabili
    ],
]);


echo $thml;

$total_fattura = array_sum(array_column($tot_hotel, 'fattura'));

 foreach ($tot_hotel as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    $tmpmodaln=str_replace('|','_',str_replace(' ', '_', $row['struttura'])).uniqid();
                    $tmpmodaln=str_replace('&','_',$tmpmodaln);
                 //   echo '<a href="#" data-toggle="modal" data-target="#struttura_'.$tmpmodaln.'">';
                    echo  $row['struttura'];
                    echo '</a>';

/*
Modal::begin([
    'id' => 'struttura_'.$tmpmodaln,
    'title' => 'Dettaglio '.$row['struttura'],
    'size' => Modal::SIZE_EXTRA_LARGE,
    'closeButton' => ['label' => 'Chiudi'],
    'options' => [
        'class' => 'fade',
        'tabindex' => false,
        'style' => 'display: none', // Ensure modal starts hidden
    ],
    'bodyOptions' => [
        'class' => 'modal-body p-3',
        'style' => 'min-height: 400px; background-color: #ffffff;width:  94%;' // Ensure minimum height and background
    ]
]);  
 echo $this->render('_modal_ddetail',['xttmp'=>$tmp['dettaglio'],
'value'=>$value,
'xddataProvider'=>$ddataProvider,
'struttura'=>$row['struttura'],
//'servizio'=>'pippo'
] );
 Modal::end();*/
                    echo '</td>';
                      echo '<td scope="col">';
                    echo  $row['notti'];
                    $tqta=$tqta+$row['notti'];
                    echo '</td>';
                    echo '<td scope="col">';
                                       if ($total_fattura > 0) {
        $percentuale = ($row['fattura'] / $total_fattura) * 100;
        echo number_format($percentuale, 2) . '%';
    } else {
        echo '0%';
    }
                    echo '</td>';
                    
                    echo '<td scope="col">€ ';
                    echo  formatEuro($row['prezzo'],2);
                    $tprz=$tprz+$row['prezzo'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo '€' .  formatEuro($row['iimponibile']);
                    $timp=$timp+$row['iimponibile'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo '€' .  formatEuro($row['city_tax']);
                    $tcity=$tcity+$row['city_tax'];
                    echo '</td>';
                      echo '<td scope="col">';
                    echo '€' .  formatEuro($row['iva']);
                    $tiva=$tiva+$row['iva'];
                    echo '</td>';                  
                                      echo '<td scope="col">';
                    echo '€' .  formatEuro($row['fattura']);
                    $tfat=$tfat+$row['fattura'];
                    echo '</td>';    
                                                         echo '<td scope="col">';
                    echo '€' .  formatEuro($row['fee']);
                    $tfee=$tfee+$row['fee'];
                    echo '</td>';  
                                                           echo '<td scope="col">';
                    echo '€' .  formatEuro($row['totale_hotel']);
                    //$tfee=$tfee+$row['fee'];
                    echo '</td>';  
                    echo '</tr>';
 }


                    


  echo  "</table>";
 }
 
echo '<br>';


 
 $db = Yii::$app->db5;
 $xcitta=$value['citta'];
 $command = $db->createCommand("
 select ARClasse12.Classe as servizio,ARClasse12.Cd_ARClasse12,sum(qta) as notti, avg(prezzo) as prezzo,
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
left join ARClasse12 on ar.Cd_ARClasse1=ARClasse12.Cd_ARClasse1
and AR.Cd_ARClasse2=ARClasse12.Cd_ARClasse2

where th_id=$model->th_id and (citta_da='$xcitta')
and xtravelrow.cd_Ar in (select cd_ar from ar where Cd_ARClasse1='TRV' 
 and Cd_ARClasse2 in ('BIG',
 'TRA',
 'VOL'
 
 )) and xtravelrow.cd_ar not in (select cd_ar from ar where x_isacconto=1)
group by ARClasse12.Classe,ARClasse12.Cd_ARClasse12
");
$tot_mezzi = $command->queryAll(); 

if (count($tot_mezzi)>0){


                   $tmp=xLoadtappa2($model->th_id, 
               //$cliente
               $r['cli'] ?? ($r['dettaglio'][0]['cd_cf_ft'] ?? null)
               , $value['citta'],1);
$ddataProvider = new ArrayDataProvider([
    'allModels' => $tmp['dettaglio'],
    'pagination' => [
        'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
    ],
    'sort' => [
        'attributes' => ['x_scdesc', 'descli', 'struttura', 'guest'], // Attributi ordinabili
    ],
]);
$htab =<<<EOF
  <h5 class="card-title">Biglietti</h5>
<table class="table  table-sm table-hover table-responsive-sm table-fit">
     <thead class="thead-dark">
                    <TH>Servizi</TH>
                    <TH>N° Righe</TH>
                    <TH>% Bgd</TH>
                    <TH>Costo Medio</TH>
                    <TH>Imp. servizi</TH>
                    <TH>Pagato</TH>
                    <TH>Fee</TH>
                    <TH>Totale fattura</TH> 
EOF;
echo $htab;
 

/*$tqta=0;
$tprz=0;
$timp=0;
$tcity=0;
$tiva=0;
$tfat=0;*/
 
$total_fattura = array_sum(array_column($tot_mezzi, 'fattura'));
 foreach ($tot_mezzi as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    $biglietto_tmpmodaln=str_replace('|','_',str_replace(' ', '_', $row['servizio'])).uniqid();
                  //  echo '<a href="#"                     data-toggle="modal" data-target="#biglietto_'.$biglietto_tmpmodaln.'">';
                    echo  $row['servizio'];
                    echo '</a>';

/*
Modal::begin([
    'id' => 'biglietto_'.$biglietto_tmpmodaln,
    'title' => 'Dettaglio '.$row['servizio'],
    'size' => Modal::SIZE_EXTRA_LARGE,
    'closeButton' => ['label' => 'Chiudi'],
    'options' => [
        'class' => 'fade',
        'tabindex' => false,
        'style' => 'display: none', // Ensure modal starts hidden
    ],
    'bodyOptions' => [
        'class' => 'modal-body p-3',
        'style' => 'min-height: 400px; background-color: #ffffff;width:  94%;' // Ensure minimum height and background
    ]
]);  
echo $this->render('_modal_ddetail',['xttmp'=>$tmp['dettaglio'],
'value'=>$value,
'xddataProvider'=>$ddataProvider,
//'struttura'=>$row['struttura'],
'servizio'=>$row['servizio']
] );
 Modal::end();*/
                    echo '</td>';
                      echo '<td scope="col">';
                    echo  $row['notti'];
                    $tqta=$tqta+$row['notti'];
                    echo '</td>';
                    echo '<td scope="col">';
                        if ($total_fattura > 0) {
        $percentuale = ($row['fattura'] / $total_fattura) * 100;
        echo number_format($percentuale, 2) . '%';
    } else {
        echo '0%';
    }
                    echo '</td>';
                    
                    echo '<td scope="col">€ ';
                    echo  formatEuro($row['prezzo'],2);
                    $tprz=$tprz+$row['prezzo'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo '€' .  formatEuro($row['iimponibile']);
                    $timp=$timp+$row['iimponibile'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo '€' .  formatEuro($row['pagato']);
                    $tpag=$tpag+$row['pagato'];
                    echo '</td>';
                      echo '<td scope="col">';
                    echo '€' .  formatEuro($row['fee']);
                    $tfee=$tfee+$row['fee'];
                    echo '</td>';                  
                                      echo '<td scope="col">';
                    echo '€' .  formatEuro($row['fattura']);
                    $tfat=$tfat+$row['fattura'];
                    echo '</td>';     
                    echo '</tr>';
 }
echo     "</table>";
}
/*$tqta=0;
$tprz=0;
$timp=0;
$tcity=0;
$tiva=0;
$tfat=0;
$tpag=0;
$tfee=0;*/
?>
<h5 class="card-title">Totali</h5>
<table class="table  table-sm table-hover table-responsive-sm table-fit">
     <thead class="thead-dark">
                    <TH>Tot.Servizi</TH>
                    <TH>Tot Media Prezzi</TH>
                    <TH>Tot.Imponibile</TH>
                    <TH>Tot.City Tax</TH>
                    <TH>Tot.Iva</TH>
                    <TH>Tot.Fatturato</TH>
                    <TH>Tot.Pagato</TH>
                    <TH>Tot.Fee</TH>
                     
</thead><tr>
<td><?php echo $tqta;?></td>
<td><?php echo formatEuro($tprz??0);?></td>
<td><?php echo '€' .formatEuro($timp??0);?></td>
<td><?php echo '€' .formatEuro($tcity??0);?></td>
<td><?php echo '€' .formatEuro($tiva??0);?></td>
<td><?php echo '€' .formatEuro($tfat??0);?></td>
<td><?php echo '€' .formatEuro($tpag??0);?></td>
<td><?php echo '€' .formatEuro($tfee??0);?></td>
</tr>
</table>
        </div>
    </div>




</div>

<br>







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
        'attribute' => 'qta',
        'label' => 'Quantità',
        'format' => ['integer'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'prezzo',
        'label' => 'Prezzo per Notte',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'tax_unit',
        'label' => 'Tassa per Unità',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'totale',
        'label' => 'Totale',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'tax',
        'label' => 'Tassa',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'fee',
        'label' => 'Fee',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'iva',
        'label' => 'IVA',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
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
 <div id="<?php echo uniqid();?>">
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
        'label' => 'Esporta Dati',]
    ]);
 
 

?>

</div>


<?php 
/*
 echo $this->render('_modal_ddetail',['xttmp'=>$tmp['dettaglio'],
'value'=>$value,
'xddataProvider'=>$ddataProvider,
'struttura'=>$row['struttura'],
//'servizio'=>'pippo'
] );
*/
//tot_hotel

if (count($tot_hotel)>0){
    $txid=$model->th_id;
$hoteldt= new ArrayDataProvider([
    'allModels' => $tot_hotel,  // Usa l'array come sorgente dati
    'pagination' => [
        'pageSize' => 10,  // Imposta il numero di righe per pagina
    ],
    
]);
$value=$value??'';
$gridhotel=[
    [
    'class' => 'kartik\grid\ExpandRowColumn',
    'width' => '50px',
    'value' => function ($model, $key, $index, $column) {
        return GridView::ROW_COLLAPSED;
    },
    // uncomment below and comment detail if you need to render via ajax
    // 'detailUrl' => Url::to(['/site/book-details']),
    'detail' => function ($model, $key, $index, $column)
    use ($tmp, $value,$txid,$r) {
       // return Yii::$app->controller->renderPartial
       // ('_expand-row-details', ['model' => $model]);
       
               $tmp=xLoadtappa2h($txid, 
               //$cliente
               $r['cli'] ?? ($r['dettaglio'][0]['cd_cf_ft'] ?? null)
               , $value['citta']);

               $ddataProvider = new ArrayDataProvider([
    'allModels' => $tmp['dettaglio'],
    'pagination' => [
        'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
    ],
    'sort' => [
        'attributes' => ['x_scdesc', 'descli', 'struttura', 'guest'], // Attributi ordinabili
    ],
]);


 return   $this->render('_ddetail_grid',['xttmp'=>$tmp['dettaglio'],
'value'=>$value,
'xddataProvider'=>$ddataProvider,
'struttura'=>$model['struttura'],
'txid'=>$txid] );
    
    },
    'headerOptions' => ['class' => 'kartik-sheet-style'] ,
    'expandOneOnly' => true
],
 
[
    'attribute' => 'struttura',
    'label' => 'Struttura',
    'group' => true,
    'subGroupOf' => 1,
],
[
    'attribute' => 'notti',
    'label' => 'Notti',
    'format' => ['integer'],
    'pageSummary' => true,
],
[
    'attribute' => 'prezzo',
    'label' => 'Prezzo medio per Notte',
    'format' => ['currency', 'EUR'],
    'pageSummary' => true,
    'pageSummaryFunc' => GridView::F_AVG,
],
[
    'attribute' => 'iimponibile',
    'label' => 'Imp. Hotel',
    'format' => ['currency', 'EUR'],
    'pageSummary' => true,
],
[
    'attribute' => 'city_tax',
    'label' => 'City Tax',
    'format' => ['currency', 'EUR'],
    'pageSummary' => true,
],
[
    'attribute' => 'iva',
    'label' => 'Iva Hotel',
    'format' => ['currency', 'EUR'],
    'pageSummary' => true,
],
[
    'attribute' => 'fattura',
    'label' => 'Totale Hotel',
    'format' => ['currency', 'EUR'],
    'pageSummary' => true,
],
/*[
    'class' => 'kartik\grid\EditableColumn',
    'attribute' => 'notti',
    'label' => 'Modifica Notti',
    'editableOptions' => function ($model, $key, $index) {
        return [
            'header' => 'Notti',
            'inputType' => \kartik\editable\Editable::INPUT_SPIN,
            'model' => $model,
            'name' => 'notti',
            'options' => [
                'pluginOptions' => [
                    'min' => 1,
                    'max' => 30
                ]
            ],
            'formOptions' => [
                'action' => ['/controller/update-notti'] // Sostituisci con la tua action
            ],
        ];
    },
    'refreshGrid' => true
]*/



];

echo  
GridView::widget([
    'dataProvider' => $hoteldt,
   // 'filterModel' => $searchModel,
   'id'=>'gridhotel'.$model->th_id.preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']).uniqid(),
   // 'resizableColumnsOptions' => ['resizeFromBody' => true],
    'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
    'striped' => true,
    'condensed' => true,
    'columns' => $gridhotel,
    'persistResize'=>true,
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
    'panel' => [
        'type' => $ris['grid_color']??'',
        'heading' => '<i class="fas  fa-book"> Hotel</i>',
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
}




if (count($tot_mezzi)>0){
 $txid=$model->th_id;
$mezzidt= new ArrayDataProvider([
    'allModels' => $tot_mezzi,  // Usa l'array come sorgente dati
    'pagination' => [
        'pageSize' => 10,  // Imposta il numero di righe per pagina
    ],
    
]);
$value=$value??'';
$gridmezzi=[

    [
    'class' => 'kartik\grid\ExpandRowColumn',
    'width' => '50px',
    'value' => function ($model, $key, $index, $column) {
        return GridView::ROW_COLLAPSED;
    },
    // uncomment below and comment detail if you need to render via ajax
    // 'detailUrl' => Url::to(['/site/book-details']),
    'detail' => function ($model, $key, $index, $column)
    use ($tmp, $value, $ddataProvider,$txid) {
       // return Yii::$app->controller->renderPartial
       // ('_expand-row-details', ['model' => $model]);
 return   $this->render('_ddetail_grid',['xttmp'=>$tmp['dettaglio'],
'value'=>$value,
'xddataProvider'=>$ddataProvider,
'servizio'=>$model['servizio'],
'txid'=>$txid] );
    
    },
    'headerOptions' => ['class' => 'kartik-sheet-style'] ,
    'expandOneOnly' => true
],
 
    ['attribute'=>'servizio'],
        ['attribute'=>'notti',
        'label'=>'Qta',
    'format' => ['integer'], 
    'pageSummary' => true,],
    ['label'=>'prezzo Medio',
'attribute'=>'prezzo',
    'format' => ['currency', 'EUR'],
    'pageSummary' => true,
  'pageSummaryFunc' => GridView::F_AVG,
],
    ['attribute'=>'iimponibile',
'format' => ['currency', 'EUR'],
    'pageSummary' => true,],

    ['attribute'=>'pagato',
'format' => ['currency', 'EUR'],
    'pageSummary' => true,],
    ['attribute'=>'fee',
'format' => ['currency', 'EUR'],
    'pageSummary' => true,],
    ['attribute'=>'fattura',
'format' => ['currency', 'EUR'],
    'pageSummary' => true,],
  /*
[
    'class' => 'kartik\grid\EditableColumn',
    'attribute' => 'notti',
    'label' => 'Modifica Notti',
    'editableOptions' => function ($model, $key, $index) {
        return [
            'header' => 'Notti',
            'inputType' => \kartik\editable\Editable::INPUT_SPIN,
            'model' => $model,
            'name' => 'notti',
            'options' => [
                'pluginOptions' => [
                    'min' => 1,
                    'max' => 30
                ]
            ],
            'formOptions' => [
                'action' => ['/controller/update-notti'] // Sostituisci con la tua action
            ],
        ];
    },
    'refreshGrid' => true
]
*/



];













/* echo  $row['notti'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo  '%%bdg';
                    echo '</td>';
                    
                    echo '<td scope="col">€ ';
                    echo  formatEuro($row['prezzo'],2);
                    echo '</td>';
                    echo '<td scope="col">';
                    echo '€' .  formatEuro($row['iimponibile']);
                    echo '</td>';
                    echo '<td scope="col">';
                    echo '€' .  formatEuro($row['pagato']);
                    echo '</td>';
                      echo '<td scope="col">';
                    echo '€' .  formatEuro($row['fee']);
                    echo '</td>';                  
                                      echo '<td scope="col">';
                    echo '€' .  formatEuro($row['fattura']);*/

echo  
GridView::widget([
    'dataProvider' => $mezzidt,
    'filterModel' => null,
    'id' => 'gridmezzi' . $model->th_id .uniqid(),
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
    //'pjax' => true, // Se usi PJAX, prova a disabilitarlo per testare il resize
]);


}

?>
 



















<?php
// ... (previous code remains the same until the hotel section)
/*
$thml=<<<EOF
<br>

<br>
  <h5 class="card-title"> Hotel</h5>
<table class="table table-sm table-hover table-responsive-sm table-fit">
     <thead class="thead-dark">
                    <TH>Struttura</TH>
                    <TH>Camere</TH>
                    <TH>% Bgd</TH>
                    <TH>Costo Medio</TH>
                    <TH>Imp. Hotel</TH>
                    <TH>City Tax</TH>
                    <TH>Iva Hotel</TH>
                    <TH>Totale Hotel</TH> 
EOF;

if (count($tot_hotel)>0) {
    echo $thml;
    
    foreach ($tot_hotel as $row) {
        $tableId = 'detail_' . str_replace(['|', ' '], '_', $row['struttura']) . uniqid();
        
        echo '<TR>';
        echo '<td scope="col">';
        echo '<a href="#" class="detail-trigger" data-target="#' . $tableId . '">';
        echo $row['struttura'];
        echo '</a>';
        echo '</td>';
        echo '<td scope="col">';
                    echo  $row['notti'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo  '%%bdg';
                    echo '</td>';
                    
                    echo '<td scope="col">€ ';
                    echo  formatEuro($row['prezzo'],2);
                    echo '</td>';
                    echo '<td scope="col">';
                    echo '€' .  formatEuro($row['iimponibile']);
                    echo '</td>';
                    echo '<td scope="col">';
                    echo '€' .  formatEuro($row['city_tax']);
                    echo '</td>';
                      echo '<td scope="col">';
                    echo '€' .  formatEuro($row['iva']);
                    echo '</td>';                  
                                      echo '<td scope="col">';
                    echo '€' .  formatEuro($row['fattura']);
                    echo '</td>';     
                    echo '</tr>';
   
        
        // Detail table (initially hidden)
        echo '<tr class="detail-row" style="display: none;">';
        echo '<td colspan="8">';
        echo '<div id="' . $tableId . '" class="detail-content" style="display: none;">';
        echo $this->render('_modal_ddetail', [
            'xttmp' => $tmp['dettaglio'],
            'value' => $value,
            'xddataProvider' => $ddataProvider,
            'struttura' => $row['struttura']
        ]);
        echo '</div>';
        echo '</td>';
        echo '</tr>';
    }
    echo "</table>";
}

// Similar modification for the tickets section
if (count($tot_mezzi)>0) {
    // ... (similar modifications for the tickets section)
}

// Add JavaScript to handle the toggling
$js = <<<JS
$(document).ready(function() {
    $('.detail-trigger').click(function(e) {
        e.preventDefault();
        var targetId = $(this).data('target');
        var detailRow = $(this).closest('tr').next('.detail-row');
        
        // Toggle the detail row
        detailRow.toggle();
        
        // Toggle the content
        $(targetId).slideToggle();
        
        // Optional: Collapse other open details
        $('.detail-row').not(detailRow).hide();
        $('.detail-content').not(targetId).hide();
    });
});
JS;

// Register the JavaScript
$this->registerJs($js);
*/
?>

<style>
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

 

 

<?php
$ter=preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']);
/*$this->registerJs("
    $('#citta_$ter').on('submit', 'form', function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function(response) {
                // Gestisci il download
            }
        });
        return false;
    });
");*/
?>