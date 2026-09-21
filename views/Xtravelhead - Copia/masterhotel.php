 <?php

    $this->registerCssFile('@web/css/custom-styles.css', ['depends' => [\yii\web\YiiAsset::class]]);


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
 
use yii\web\View;
    /* @var $this yii\web\View */
    /* @var $model app\models\Xtravelhead */
 
    $this->title = $model->descrizione;
   // $this->params['breadcrumbs'][] = ['label' => 'Xtravelheads', 'url' => ['index']];
    //$this->params['breadcrumbs'][] = $this->title;
    \yii\web\YiiAsset::register($this);

function formatEuro($number) {
    return number_format($number, 3, ',', '.'); // 2 decimali, ',' come separatore decimali, '.' come separatore migliaia
}


    $this->registerCss('
    .full-screen-container {
        width: 100%;
        max-width: 600px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #fff;
        box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        margin: 10px; /* Aggiungiamo margine per separare i blocchi */
    }

    @media (min-width: 768px) {
        .full-screen-container {
            width: calc(33.33% - 20px); /* Calcoliamo la larghezza per fare 3 colonne in una riga */
        }
    }

    .AGE-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .AGE-info {
        margin-bottom: 15px;
    }

    .AGE-info-label {
        font-weight: bold;
    }

    .AGE-info-value {
        margin-left: 10px;
    }

    .AGE-actions {
        margin-top: 20px;
    }

    .AGE-actions .btn {
        margin-right: 10px;
    }

    .full-width {
        width: 100% !important;
        max-width: none !important;
    }
        .chart-container {
    width: 300px;
    height: 200px;
}
    .card-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}

.custom-card {
    width: 50%;
min-height: 500px;
    margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .custom-card {
        width: 100%;
    }
table.table-fit {
  width: auto !important;
  table-layout: auto !important;
}
table.table-fit thead th,
table.table-fit tbody td,
table.table-fit tfoot th,
table.table-fit tfoot td {
  width: auto !important;
}
  table td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.hidden {
    display: none !important;
    visibility: hidden !important;
}



');
    $roomlist2 = $roomlist;
    ?>
 <div class="xtravelhead-view">
     <p></p>
     <table>
         <tr>
             <td width='50%'>
                 <?php /*echo DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'th_id',

                            [                      // the owner name of the model
                                'label' => 'Data',
                                'value' => date('d/m/Y', strtotime($model->datath))
                            ],
                            'numero',
                            'descrizione',

                            [                      // the owner name of the model
                                'label' => 'Totale Tasse',
                                'value' => '€' . round($model->tax, 2),
                            ],
                            [                      // the owner name of the model
                                'label' => 'Totale Fee',
                                'value' => '€' . round($model->fee, 2),
                            ],
                            [                      // the owner name of the model
                                'label' => 'Totale Servizi',
                                'value' => '€' . round($model->totaleservizi, 2),
                            ],
                            [                      // the owner name of the model
                                'label' => 'Totale Fatturato',
                                'value' => '€' . round($model->totft, 2),
                            ],


                        ],
                    ])*/ ?>
             </td>
             <td>
             














             </td>
         </tr>
     </table>
 </div>



 <?php
    /*<!-div class="form-group"->
    <!-input id="search-input" type="text" class="form-control" placeholder="Cerca...">
</div>
*/
    $tappe2 = array_map(function ($tappe) {
        unset($tappe['check_in']);
        return $tappe;
    }, $tappe);

    $uniqueTappe = [];
    foreach ($tappe2 as $tappa) {
        $key = $tappa['citta'] . '_' . $tappa['cd_cf_ft']; // Combina citta e cd_cf_ft per creare una chiave unica
        if (!isset($uniqueTappe[$key])) {
            $uniqueTappe[$key] = $tappa;
        }
    }

  //  yii::warning($recap);
    $items = [];
     $items[] = [
        'label' => ' ',
        'id' => 's',
        'content' => '',
        
         'active' => true,
    ];
    $items[] = [
        'label' => '<i class="fas fa-toolbox"> Tools</i>',
        'id' => 'tools',
        'content' =>
        $this->render('_tools', [
            'dettaglio' => $dettaglio,
            'th_id' => $model->th_id,
            'pivot' => $pivot,
            'labels' => $labels,
            'series' => $series,
            'tappe' => $uniqueTappe,
            'roomlist' => $roomlist,
            'analisitappe' => $analisitappe,
            'listatappetool' => $listatappetool,
            'listaart' => $listaart
        ])
        // 'active' => true,
    ];

$dettaglio_=$dettaglio;
$pivot_=$pivot;
$labels_=$labels;
$series_=$series;
$uniqueTappe_=$uniqueTappe;
$roomlist_=$roomlist;
$analisitappe_=$analisitappe;
$listatappetool_=$listatappetool;
$listaart_=$listaart;

    $items[] = [
        'label' => '<i class="fas fa-user"> Cost Analisys</i>',
        'id' => 'costanalsys',
        'content' =>
        $this->render('_analisi', [
            'dettaglio' => $dettaglio,

            'pivot' => $pivot,
            'labels' => $labels,
            'series' => $series,

            'analisitappe' => $analisitappe,
        ])
        // 'active' => true,
    ];
   /* $items[] = [
        'label' => '<i class="fas fa-user"> Room List</i>',
        'id' => 'roomlist',
        'content' =>
        $this->render('_roomlist', [
            'roomlist' => $roomlist,
            'tipo' => 1
        ])
        // 'active' => true,
    ];*/
   /* $items[] = [
        'label' => '<i class="fas fa-user"> Riepilogo</i>',
        'id' => 'roomlist2',
        'content' =>
        $this->render('_roomlist', [
            'roomlist' => $recap,
            'tipo' => 2
        ]),
        'active' => false
        // 'active' => true,
    ];*/
    $i = 1;
    $recap2= $recap;
    $roomlist = $recap;
    foreach ($uniqueTappe as $key => $value) {
        /* old_ rimosso il 29/08/24
   $items[] = [
        'label' => '<i class="fas fa-home">' . $value['citta'] . '</i>',
 
        'linkOptions' => ['data-url' => Url::to([
            '/xtravelhead/loadtappa',
            'th_id' => $model->th_id,
            'cliente' => $value['cd_cf_ft'],
            'citta' => $value['citta']
        ])],
        //' - ' . $value['citta'] . ' - ' . $value['cd_cf_ft'],
        // 'active' => true,
    ];
*/
        /*$this->renderajax('_detail', ['dettaglio'=> $results,
'th_id'=>$th_id,
'cli'=>$cliente,
'citta'=>$citta,
'totale'=>$totale,
'pagamenti'=>$pagamenti]);*/

        $r = xxLoadtappa2($model->th_id, $value['cd_cf_ft'], $value['citta']);
       /* $items[] = [
            'label' =>
            '<i class="fas fa-home">' . $value['citta'] . '</i>',
            'id' => 'dettaglio_' . $i,
            'content' =>
            $this->render('_detail2', [
                'dettaglio' => $r['dettaglio'],
                'th_id' => $r['th_id'],
                'cli' => $r['cli'],
                'citta' => $r['citta'],
                'totale' => $r['totale'],
                'pagamenti' => $r['pagamenti']

            ]),

        ];*/
        $i++;
    }

 
  
    ?>











 <?php

    function xxLoadtappa2($th_id, $cliente=null, $citta)
    {
        $params = [];
        $whereClauses = [];
        $whereClauses[] = "th_id=" . $th_id;
        $whereClauses[] = "AR.x_isacconto is null";
       if (isset($cliente)){
        $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
       }
       
       
        $whereClauses[] = "citta='" . str_replace("'", "''", $citta) . "'";
        $where = 'WHERE ' . implode(' AND ', $whereClauses);
        $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";
        // Esegui la query
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $results = $command->queryAll();
        if (count($results) == 0) {
            $params = [];
            $whereClauses = [];
            $whereClauses[] = "th_id=" . $th_id;
            $whereClauses[] = "AR.x_isacconto is null";
                  if (isset($cliente)){
            $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                  }
            $whereClauses[] = "citta_da='" . str_replace("'", "''", $citta) . "'";
            $where = 'WHERE ' . implode(' AND ', $whereClauses);
            $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";



            // Esegui la query
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $results = $command->queryAll();
        }
        $sql = "select x_scdesc,descli,min(check_in) as startdate, max(check_out) as enddate ,CASE WHEN citta IS NULL THEN citta_da ELSE citta END AS citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax) as  tassa,SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
$where   
group by x_scdesc,descli,CASE WHEN citta IS NULL THEN citta_da ELSE citta END 
order by min(check_in) asc ";
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $totale = $command->queryAll();


        $whereClauses2 = [];
        $whereClauses2[] = "th_id=" . $th_id;

        $whereClauses2[] = "cd_cf_ft='" . $cliente . "'";
        $whereClauses2[] = "citta='" . str_replace("'", "''", $citta) . "'";
        $where2 = 'WHERE ' . implode(' AND ', $whereClauses2);
        $sql = "select case when x_pagato<0 then x_pagato*-1 else x_pagato end as xpagato,
data_pg,descontab,pg.Descrizione,xtravelrow.cd_pg from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar
left join pg on pg.Cd_PG=xtravelrow.cd_pg
$where2  
and (ar.x_isacconto is not null or contabile is not  null)
order by data_pg asc ";
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $pagamenti = $command->queryAll();
        // Yii::debug("SQL Query: $sql");
        //Yii::debug("Params: " . json_encode($params));
        //   var_dump(empty($pagamenti));
        if (1 == 2) {
            //(empty($pagamenti)==true ){
            var_dump($pagamenti);
            $whereClauses3 = [];
            $whereClauses3[] = "th_id=" . $th_id;
      if (isset($cliente)){
            $whereClauses3[] = "cd_cf_ft='" . $cliente . "'";

      }
      $whereClauses3[] = "citta_da='" . str_replace("'", "''", $citta) . "'";
            $where3 = 'WHERE ' . implode(' AND ', $whereClauses2);


            $sql = "select case when x_pagato<0 then x_pagato*-1 else x_pagato end as xpagato,
data_pg,descontab,pg.Descrizione,xtravelrow.cd_pg from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar
left join pg on pg.Cd_PG=xtravelrow.cd_pg
$where3  
and (ar.x_isacconto is not null or contabile is not  null)
order by data_pg asc ";
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $pagamenti = $command->queryAll();
        }
        $dettatappa = [
            'dettaglio' => $results,
            'th_id' => $th_id,
            'cli' => $cliente,
            'citta' => $citta,
            'totale' => $totale,
            'pagamenti' => $pagamenti
        ];
        return $dettatappa;
    }
    // Funzione per chiudere la tabella e mostrare i totali
    function closeTable($totals)
    {
        echo '<tr  class="table-total bg-primary">';
        echo '<td colspan=2  style="font-size: 16px;"> <strong>Totali</strong> </td>';
        echo '<td  style="font-size: 16px;"> <strong>€ ' . formatEuro($totals['mprezzo'] ) . '</strong></td>';
        echo '<td style="font-size: 16px;"> <strong>€ ' . formatEuro($totals['imponibile']) . '</strong></td>';
        echo '<td style="font-size: 16px;"> <strong>€ ' . formatEuro($totals['ctax'] ) . '</strong></td>';
        echo '<td style="font-size: 16px;"> <strong>€ ' . formatEuro($totals['iva'] ) . '</strong></td>';
        echo '<td style="font-size: 16px;"> <strong>€ ' . formatEuro($totals['totale'] ) . '</strong></td>';
       // echo '<td style="font-size: 16px;"> <strong>€ ' . formatEuro($totals['fee'] ) . '</strong></td>';
       // echo '<td style="font-size: 16px;"> <strong>€ ' . formatEuro($totals['totaleimpfatt'] ) . '</strong></td>';
        echo '<td style="font-size: 16px;"> <strong>€ ' 
        . formatEuro($totals['totaleimpfatt']+ $totals['fee'] )  . '</strong></td>';
        echo '</tr>';
        echo '</table>';
    }
    ?>







 <!-- Pulsante per aprire la dialog -->
 <?php   echo Html::button('Elenco Ospiti', [
    'class' => 'btn btn-primary',
    'data-toggle' => 'modal',
    'data-target' => '#ospiti-modal'
]) ?>
 <?php  /* echo Html::button('Scheda Hotel', [
    'class' => 'btn btn-primary btn-hotel',
    //'data-toggle' => 'modal',
    //'data-target' => '#ospiti-modal'
]) */?>
 <?php  /* echo Html::button('Scheda Ticketing', [
    'class' => 'btn btn-primary btn-ticketing',
    //'data-toggle' => 'modal',
    //'data-target' => '#ospiti-modal'
]) */?>
<?php
$script = <<<JS
$('.btn-hotel, .btn-ticketing').on('click', function() {
    alert('Procedura in costruzione');
});
JS;

$this->registerJs($script);
?>

 <!-- Pulsante per aprire la dialog -->
 <!--button id="open-dialog2" class="btn btn-primary">Tappe</button>-->
<?php   echo Html::button('Analisi Dati', [
    'class' => 'btn btn-primary',
    'data-toggle' => 'modal',
    'data-target' => '#chart-modal'
]) ?>

<?php   echo Html::button('Elenco Clienti', [
    'class' => 'btn btn-primary',
    'data-toggle' => 'modal',
    'data-target' => '#elecli-modal'
]) ?>

<?php   echo Html::button('File', [
    'class' => 'btn btn-primary',
    'data-toggle' => 'modal',
    'data-target' => '#file-modal'
]) ?>

<?php   echo Html::button('Tools', [
    'class' => 'btn btn-primary',
    'data-toggle' => 'modal',
    'data-target' => '#tool-modal'
]) ?>
<?php 

 /*echo Dialog::widget([
        'libName' => 'krajeeDialog_tools', // Nome della libreria
        'options' => [
            'size' => Dialog::SIZE_WIDE, // large dialog text
            'type' => Dialog::TYPE_INFO, // bootstrap contextual color
            'title' => 'Tools',
            'nl2br' => false,
      ],
    ]);*/

 //$jrecap2 = Json::encode($recap2);

            $mdettaglio = ($dettaglio_);
            $mth_id =($model->th_id);
            $mpivot =($pivot_);
            $mlabels = ($labels_);
            $mseries = ($series_);
            $mtappe = ($uniqueTappe_);
            $mroomlist =($roomlist_);
            $manalisitappe =($analisitappe_);
            $mlistatappetool =($listatappetool_);
            $mlistaart = ($listaart_);


?>


<?php   echo Html::button('Carica locandina ', [
    'class' => 'btn btn-primary',
    'data-toggle' => 'modal',
    'data-target' => '#upl-modal'
]) ?>


<?php echo Html::a('Torna indietro', Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-secondary']);?>

 <?php






    echo '<br> 
    <table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>DATA SHOW</th>
        <th> CITTA\'</th>
        <th>VENUE</th>
        <th> Media costo Camera</th>
        <th>Imp. Hotel</th>
        <th>City Tax</th>
        <th>IVA Pagata</th>
        <th>Totale Pagato</th>
        <th>Totale FEE</th>
        <th>Torale Gen. FT</th>
    </thead>

';
$xtmptappe=$roomlist['tappetour'] ;
    foreach ($roomlist['tappetour'] as $value) {


        echo '<tr>';
        echo '<td>';
        echo 'Dal ' . date('d/m/Y', strtotime($value['datainizio'])) . ' al ' . date('d/m/Y', strtotime($value['datafine']));
        echo '</td>';
        echo '<td>';
    /*   echo Html::button($value['citta'] ?? 'N.d.', [
    'class' => 'btn btn-primary',
    'id' => 'openDialog'. preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']),
    //'data-dettaglio' => $dettaglio, // Passa i dati come attributi data-* o usa JavaScript per ottenerli
    //'data-pivot' => $pivot,
    //'data-labels' => json_encode($labels),
    //'data-series' => json_encode($series),
   // 'data-analisitappe' => json_encode($analisitappe),
]);*/
 echo Html::button($value['citta'] ?? 'N.d.', [
    'class' => 'btn btn-primary',
    'data-toggle' => 'modal',
    'data-target' => '#citta_'.preg_replace('/[^a-zA-Z0-9]/', '', $value['citta'])
 ]);
 

        echo '</td>';
        echo '<td>';
        echo $value['citta'];
        echo '</td>';
        echo '<td>€ ';
        echo formatEuro($value['mprezzo'], 2);
        echo '</td>';
        echo '<td>€ ';
        echo formatEuro($value['tfatt']-$value['ctax']-$value['fee'], 2);
        echo '</td>';
        echo '<td>€ ';
        echo formatEuro($value['ctax'], 2);
        echo '</td>';
        echo '<td>€ ';
        echo formatEuro($value['iva'], 2);
        echo '</td>';
        echo '<td>€ ';
        echo formatEuro($value['totale'], 2);
        echo '</td>';
        echo '<td>€ ';
        echo formatEuro($value['fee'], 2);
        echo '</td>';
        echo '<td>€ ';
        //echo formatEuro($value['ttotalegenerale'], 2);
        echo formatEuro($value['imponibile']+$value['ctax']+$value['fee'], 2);
        
        echo '</td>';
 


        $r = xxLoadtappa2($model->th_id, null , $value['citta']);
        $xc= preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']);





        echo '</tr>';
    }
    echo '<tr  class="table-total bg-primary">';
    echo '<td colspan=3s style="font-size: 16px;"> <StRONG> Totali </td>';
    echo '<td style="font-size: 16px;"> <StRONG >€ ';
    $eta = array_column($roomlist['tappetour'], 'mprezzo');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);

    // Calcola il numero di elementi
    $numeroElementi = count($eta);

    // Calcola la media
    if ($numeroElementi > 0) {
        $mediaEta = $sommaEta / $numeroElementi;
    } else {
        $mediaEta = 0; // O un altro valore di default
    }
    echo formatEuro($mediaEta, 2) . '</td>';
    $tfatt = array_column($roomlist['tappetour'], 'tfatt');
    // Calcola la somma degli anni
    $sommatfatt = array_sum($tfatt)??0;
    $xttax = array_column($roomlist['tappetour'], 'ctax');
     // Calcola la somma degli anni
    $sommaxttax = array_sum($xttax)??0;
    $Tfee_ = array_column($roomlist['tappetour'], 'fee');

    // Calcola la somma degli anni
    $sommaTfee = array_sum($Tfee_)??0;
   
   
   
   
    echo '<td style="font-size: 16px;"> <StRONG>€ ';
    echo formatEuro($sommatfatt-$sommaxttax-$sommaTfee, 2) . '</td>';
    $timp=$sommaEta;
    $eta = array_column($roomlist['tappetour'], 'ctax');
    
    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);
    echo '<td style="font-size: 16px;"> <StRONG>€ ';
    echo formatEuro($sommaEta, 2) . '</td>';
    $ttax=$sommaEta;
    $eta = array_column($roomlist['tappetour'], 'iva');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);
    echo '<td style="font-size: 16px;"> <StRONG>€ ';
    echo formatEuro($sommaEta, 2) . '</td>';
    $eta = array_column($roomlist['tappetour'], 'totale');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);
    echo '<td style="font-size: 16px;"> <StRONG>€ ';
    echo formatEuro($sommaEta, 2) . '</td>';
    $eta = array_column($roomlist['tappetour'], 'fee');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);
    $tofee=$sommaEta;
    echo '<td style="font-size: 16px;"> <StRONG>€ ';
    echo formatEuro($sommaEta, 2) . '</td>';
    $eta = array_column($roomlist['tappetour'], 'ttotalegenerale');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);
    echo '<td style="font-size: 16px;"> <StRONG>€ ';
    echo formatEuro($tofee+$ttax+$timp, 2) . '</td>';



    ?>

 </table>
 <br>
 <br>
 <?php
 $modaltappetour=$roomlist['tappetourcli'];
   








    echo '<h3>Elenco Documenti Emessi</h3>
 <table class="table table-sm table-hover table-responsive-sm table-fit">
     <thead class="thead-dark">
         <th>Codice Documento</th>
         <th>Numero</th>
         <th>Data</th>
         <th>Imponibile</th>
         <th>Totale a pagare</th>
     </thead>
     ';
    foreach ($roomlist['dotes'] as $value) {
        echo '<tr>';
        echo '<td>' . $value['cd_do'] . '</td>';
        echo '<td>' . $value['NumeroDoc'] . '</td>';
        echo '<td>' . date('d/m/Y', strtotime($value['DataDoc'])) . '</td>';
        
        echo '<td>€ ' . formatEuro($value['totimponibilev'], 2) . '</td>';
        echo '<td>€ ' . formatEuro($value['TotDocumentoV'], 2) . '</td>';
        //echo '<td>€ ' . formatEuro($value['TotaPagareV'], 2) . '</td>';
        echo '</tr>';
    }

    echo '
 </table>';
    ?>


<?php
//echo 'asdadasda';

// Register ApexCharts library
$this->registerJsFile('https://cdn.jsdelivr.net/npm/apexcharts', [
    'position' => \yii\web\View::POS_HEAD
]);

// Register our custom JavaScript with proper escaping
$js = <<<JS
// First, let's create a more robust chart initialization function
const initializeChart = (containerId, options) => {
    // Use standard string concatenation instead of template literals for the error message
    const container = document.querySelector(containerId);
    if (!container) {
        console.error('Chart container ' + containerId + ' not found');
        return null;
    }

    // Create and render the chart
    const chart = new ApexCharts(container, options);
    return chart.render().then(() => chart).catch(err => {
        console.error("Chart rendering error:", err);
        return null;
    });
};

// Define chart options that we'll reuse
const getChartOptions = () => ({
    chart: {
        type: 'bar',
        height: 350,
        background: '#ffffff',
        animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 800
        }
    },
    series: [{
        name: 'Valori',
        data: [10, 15, 20, 25, 30]
    }],
    xaxis: {
        categories: ["Gen", "Feb", "Mar", "Apr", "Mag"]
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                height: 300
            }
        }
    }],
    colors: ['#008FFB']
});

// Add console logs for debugging
const debugChart = (containerId) => {
    const container = document.querySelector(containerId);
    console.log('Container:', container);
    if (container) {
        console.log('Container dimensions:', container.getBoundingClientRect());
    }
};

// Initialize modal chart when modal is fully shown
let modalChart = null;
$('#chart-modal').on('shown.bs.modal', function() {
    debugChart("#chart");
    setTimeout(() => {
        if (!modalChart) {
            console.log('Initializing modal chart...');
            modalChart = initializeChart("#chart", getChartOptions());
        }
    }, 300);
});

// Cleanup modal chart when modal is hidden
$('#chart-modal').on('hidden.bs.modal', function() {
    console.log('Modal hidden, cleaning up chart...');
    if (modalChart) {
        modalChart.then(chart => {
            if (chart) {
                chart.destroy();
            }
            modalChart = null;
        });
    }
});

// Initialize standalone chart
document.addEventListener("DOMContentLoaded", function() {
    debugChart("#chart1");
    initializeChart("#chart1", getChartOptions());
});
JS;

$this->registerJs($js, \yii\web\View::POS_READY);
?>

<?php


$this->registerCss(<<<CSS
    .modal-dialog {
        max-width: 98%;
    }
    
    .modal-content {
        background-color: #ffffff;
        height: auto;
        min-height: 500px;
    }
    
    #chart, #chart1 {
        width: 100% !important;
        height: 350px !important;
        position: relative;
    }
CSS);

Modal::begin([
    'id' => 'chart-modal',
    'title' => 'Grafico',
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
?>


<?php
$gridColumns = [
    [
        'attribute' => 'party',
        'label' => 'Party',
         
    ],
    [
        'attribute' => 'ttotale',
        'label' => 'Totale',
         
    ],
    [
        'attribute' => 'timponibile',
        'label' => 'Imponibile',
     ],
        [
        'attribute' => 'ttassa',
        'label' => 'Tassa',
     ],
        [
        'attribute' => 'tiva',
        'label' => 'Iva',
     ],
        [
        'attribute' => 'tfee',
        'label' => 'Fee',
     ],
  ];




$dataProvider = new ArrayDataProvider([
    'allModels' => $dettaglio,
    //'pagination' => [
    //    'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
    //],
 
]);
// Crea il menu di esportazione

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

echo '<br>';
echo '<br>';

echo ExportMenu::widget([
    'id' => 'exp_button_roomlist', // Imposta l'ID per il bottone
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'exportConfig' => $exportConfig,
    'filename' => 'Export_' . date('Y-m-d_H-i-s'),
    'target' => ExportMenu::TARGET_BLANK,
    'showColumnSelector' => true,
    'clearBuffers' => true,
 //   'class'=>'pino',
    'dropdownOptions' => [
        'label' => 'Esporta Dati',
        'class' => 'PErsonale', // Solo classi personalizzate
        'title' => 'Esporta i dati nel formato selezionato',
        'data-toggle' => 'dropdown',
        'aria-haspopup' => 'true',
        'aria-expanded' => 'false',
    ],
]);


?>




<h3>Costo Party</h3>
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>Party</th>
        <th>Totale</th>
        <th>Pagato Hotel</th>
        <th>Imponibile</th>
        <th>City Tax</th>
        <th>Iva</th>
        <th>Fee</th>
    </thead>
  <?php


    $tttotale = 0;
    $ttimponibile = 0;
    $ttimponibile = 0;
    $tttassa = 0;
    $ttiva = 0;
    $ttfee = 0;
if (isset($dettaglio)){
    foreach ($dettaglio as $value) {
        /*party,sum(tax) as ttassa,
sum(imponibile) as timponibile,sum(iva) as tiva,
sum(fee) as tfee,sum(totale) as ttotale*/
        echo '<tr>';
        echo '<td scope="col"> ' . $value['party'] . '</td>';
        echo '<td scope="col"> € ' . formatEuro($value['ttotale']  +  $value['ttassa'] ) . '</td>';
        echo '<td scope="col"> € ' . formatEuro($value['ttotale'], 2) . '</td>';
        echo '<td scope="col"> € ' . formatEuro($value['timponibile'], 2) . '</td>';
        echo '<td scope="col"> € ' . formatEuro($value['ttassa'], 2) . '</td>';
        echo '<td scope="col"> € ' . formatEuro($value['tiva'], 2) . '</td>';
        echo '<td scope="col"> € ' . formatEuro($value['tfee'], 2) . '</td>';
        echo '</tR>';
        $tttotale = $tttotale + round($value['ttotale'], 2);
        $ttimponibile =  $ttimponibile + round($value['timponibile'], 2);
        $tttassa = $tttassa + round($value['ttassa'], 2);
        $ttiva = $ttiva + round($value['tiva'], 2);
        $ttfee = $ttfee + round($value['tfee'], 2);
    }
}
    echo '<tr  class="table-total bg-primary">';
    echo '<td  style="font-size: 16px;"><strong>Totale  ' . '</strong></td>';
    echo '<td  style="font-size: 16px;"><strong>€ ' . formatEuro($tttotale + $tttassa, 2) . '</strong></td>';
    echo '<td  style="font-size: 16px;"><strong>€ ' . formatEuro($tttotale, 2) . '</strong></td>';
    echo '<td  style="font-size: 16px;"><strong>€ ' . formatEuro($ttimponibile, 2)  . '</strong></td>';
    echo '<td  style="font-size: 16px;"><strong>€ ' . formatEuro($tttassa, 2)  . '</strong></td>';
    echo '<td  style="font-size: 16px;"><strong>€ ' . formatEuro($ttiva, 2) . '</strong></td>';
    echo '<td  style="font-size: 16px;"><strong>€ ' . formatEuro($ttfee, 2) . '</strong></td>';
    ?>

</table>

<div class="d-flex flex-wrap justify-content-center align-items-start">
    <!--<div class="full-screen-container">-->

    <div class=" custom-card card">
        <div class="card-body">
            <h5 class="card-title">Party Group Cost</h5>
            <?= ApexchartsWidget::widget([
                'type' => 'pie',
 
                'height' => '400px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                            'id' => 'chart-pie',
                    ],
                    'labels' => $labels,
                ],
                'series' => $series,
            ]) ?>

        </div>
    </div>

    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title"> Prezzi Camere Medi</h5>


            <?php
            $categories = $analisitappe[0];

            // Array di serie (dati)
            $dataSeries = array_map(function ($item) {
                return (float)str_replace('.', ',', $item['data']);
            }, $analisitappe[1]);



            echo   \onmotion\apexcharts\ApexchartsWidget::widget([
                'type' => 'bar', // default area
                'id'=>'chart-bar',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true,
                            'autoSelected' => 'zoom',
                        ],
                        
                    ],
                    'xaxis' => [
                        'categories' => $categories,

                    ],
                    'plotOptions' => [
                        'bar' => [
                            'horizontal' => false,
                            'endingShape' => 'rounded',
                        ],
                    ],
                    'dataLabels' => [
                        'enabled' => false,
                    ],
                    'stroke' => [
                        'show' => true,
                        'colors' => ['transparent'],
                    ],
                    'legend' => [
                        'verticalAlign' => 'bottom',
                        'horizontalAlign' => 'left',
                        //'floating'=> 'true'
                    ],
                ],
                'series' =>
                [
                    [
                        'name' => 'Dati',
                        'data' => $dataSeries, // Assegna i dati trasformati
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
<h3>Costo Medio Party/Camere</h3>
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead>
        <tr>
            <th>Party</th>
            <?php foreach ($pivot['columns'] as $column): ?>
                <th><?= Html::encode($column) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pivot['results'] as $row): ?>
            <tr>
                <td><?= Html::encode($row['party']) ?></td>
                <?php foreach ($pivot['columns'] as $column): ?>
                    <td><?= isset($row[$column]) ? '€ ' . number_format($row[$column], 2) : '0' ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        <!-- Riga delle medie -->
        <tr class="table-total bg-primary">
            <td><strong>Media</strong></td>
            <?php foreach ($pivot['columnAverages'] as $average): ?>
                <td><strong><?= '€ ' . number_format($average, 2) ?></strong></td>
            <?php endforeach; ?>
        </tr>
    </tbody>
</table>

<?php Modal::end(); ?>

<!-- Standalone chart container -->
 
<!-- Button to open modal -->


<?php  TabsX::widget([
    'items' => $items,
    'position' => TabsX::POS_LEFT,
    'bordered' => true,
    'encodeLabels' => false,
    'options' => [
        'class' => 'hidden d-none' // Aggiungiamo classi CSS standard per nascondere
    ],
    'pluginOptions' => [
        'enableCache' => false,
        'visible' => false,
        'hidden' => true
    ]
]);  ?>





<?php 

Modal::begin([
    'id' => 'ospiti-modal',
    'title' => 'Elenco Ospiti',
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
?>
<?php
$gridColumns = [
    [
        'attribute' => 'guest',
        'label' => 'Ospite',
         
    ],
    [
        'attribute' => 'cd_Ar',
        'label' => 'Tipo Camera',
         
    ],
    [
        'attribute' => 'note',
        'label' => 'Note',
     ],
  ];




$dataProvider = new ArrayDataProvider([
    'allModels' => $roomlist2,
    //'pagination' => [
    //    'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
    //],
 
]);
// Crea il menu di esportazione

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

echo '<br>';
echo '<br>';

echo ExportMenu::widget([
    'id' => 'exp_button_roomlist', // Imposta l'ID per il bottone
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'exportConfig' => $exportConfig,
    'filename' => 'Export_' . date('Y-m-d_H-i-s'),
    'target' => ExportMenu::TARGET_BLANK,
    'showColumnSelector' => true,
    'clearBuffers' => true,
 //   'class'=>'pino',
    'dropdownOptions' => [
        'label' => 'Esporta Dati',
        'class' => 'PErsonale', // Solo classi personalizzate
        'title' => 'Esporta i dati nel formato selezionato',
        'data-toggle' => 'dropdown',
        'aria-haspopup' => 'true',
        'aria-expanded' => 'false',
    ],
]);
?>
<br>
<?php
    echo '
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>Ospite</th>
        <th>Tipo Camera</th>
        <th>Note</th>
    </thead>';

    foreach ($roomlist2 as $value) {
        echo '<tr>';
        echo '<td>';
        echo isset($value['guest']) ? str_replace(["'", '"', "’", "´"], '', $value['guest']) : '';


        echo '</td>';
        echo '<td>';
        echo isset($value['cd_Ar'])?$value['cd_Ar']:'';
        echo '</td>';
        echo '<td>';
        echo isset($value['note'])?$value['note']:'';
        echo '</td>';

        echo '</tr>';
    }
?>
</table>
<br>
<br>
<?php Modal::end(); ?>




<?php 

  function xLoadtappa2($th_id, $cliente=null, $citta)
    {
        $params = [];
        $whereClauses = [];
        $whereClauses[] = "th_id=" . $th_id;
        $whereClauses[] = "AR.x_isacconto is null";
        $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
        $whereClauses[] = "citta='" . $citta . "'";
        $where = 'WHERE ' . implode(' AND ', $whereClauses);
        $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";
        // Esegui la query
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $results = $command->queryAll();
        if (count($results) == 0) {
            $params = [];
            $whereClauses = [];
            $whereClauses[] = "th_id=" . $th_id;
            $whereClauses[] = "AR.x_isacconto is null";
            $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
            $whereClauses[] = "citta_da='" . $citta . "'";
            $where = 'WHERE ' . implode(' AND ', $whereClauses);
            $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";



            // Esegui la query
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $results = $command->queryAll();
        }
        $sql = "select x_scdesc,descli,min(check_in) as startdate, max(check_out) as enddate ,CASE WHEN citta IS NULL THEN citta_da ELSE citta END AS citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax) as  tassa,SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
$where   
group by x_scdesc,descli,CASE WHEN citta IS NULL THEN citta_da ELSE citta END 
order by min(check_in) asc ";
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $totale = $command->queryAll();


        $whereClauses2 = [];
        $whereClauses2[] = "th_id=" . $th_id;

        $whereClauses2[] = "cd_cf_ft='" . $cliente . "'";
        $whereClauses2[] = "citta='" . $citta . "'";
        $where2 = 'WHERE ' . implode(' AND ', $whereClauses2);
        $sql = "select case when x_pagato<0 then x_pagato*-1 else x_pagato end as xpagato,
data_pg,descontab,pg.Descrizione,xtravelrow.cd_pg from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar
left join pg on pg.Cd_PG=xtravelrow.cd_pg
$where2  
and (ar.x_isacconto is not null or contabile is not  null)
order by data_pg asc ";
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $pagamenti = $command->queryAll();
        // Yii::debug("SQL Query: $sql");
        //Yii::debug("Params: " . json_encode($params));
        //   var_dump(empty($pagamenti));
        if (1 == 2) {
            //(empty($pagamenti)==true ){
            var_dump($pagamenti);
            $whereClauses3 = [];
            $whereClauses3[] = "th_id=" . $th_id;

            $whereClauses3[] = "cd_cf_ft='" . $cliente . "'";
            $whereClauses3[] = "citta_da='" . $citta . "'";
            $where3 = 'WHERE ' . implode(' AND ', $whereClauses2);


            $sql = "select case when x_pagato<0 then x_pagato*-1 else x_pagato end as xpagato,
data_pg,descontab,pg.Descrizione,xtravelrow.cd_pg from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar
left join pg on pg.Cd_PG=xtravelrow.cd_pg
$where3  
and (ar.x_isacconto is not null or contabile is not  null)
order by data_pg asc ";
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $pagamenti = $command->queryAll();
        }
    /*    $html = $this->renderajax('_detail', [
            'dettaglio' => $results,
            'th_id' => $th_id,
            'cli' => $cliente,
            'citta' => $citta,
            'totale' => $totale,
            'pagamenti' => $pagamenti
        ]);*/
        //return Json::encode($html);
return [
            'dettaglio' => $results,
            'th_id' => $th_id,
            'cli' => $cliente,
            'citta' => $citta,
            'totale' => $totale,
            'pagamenti' => $pagamenti
];


    }


?>



 

<?php foreach ($xtmptappe as $value): ?>


 
 <?php
Modal::begin([
    'id' => 'citta_'.preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']),
    'title' => 'Dettaglio Citta',
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

?>

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
            <h5 class="card-title"> Pagamenti</h5>

            <table class="table  table-sm table-hover table-responsive-sm table-fit">
                <thead class="thead-dark">
                    <TH>Data</TH>
                    <TH>Importo</TH>
                    <TH>Residuo</TH>
 
                </thead>
                <?php
                $i=1;
                $tempdapagare=0;
                $tpag=0;
                foreach ($tmp['pagamenti'] as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    echo date('d/m/Y', strtotime($row['data_pg']));
                    echo '</td>';
                    echo '<td scope="col">€ ';
                    echo  formatEuro($row['xpagato'],2);
                    echo '</td>';
                    echo '<td scope="col">';
                    $tpag=$tpag+ round($row['xpagato'], 2);
                    if($i==1){
                    echo '€' .  formatEuro($totgen-($row['xpagato']));
                    $tempdapagare = $totgen - round($row['xpagato'], 2);
                    }else{
                        echo '€' .  formatEuro($tempdapagare -  $row['xpagato']);
                        $tempdapagare = $tempdapagare - round($row['xpagato'], 2);
                    }
                    echo '</td>';
                    echo '</tr>';
                    $i++;
                }
    echo '<TR bgcolor="#007bff">';
    echo '<td style="font-size: 16px;"><strong>Totali</strong>';
    echo '</td>';
    echo '<td style="font-size: 16px;">';
    echo '<strong>€ '.formatEuro($tpag);
    echo '</strong></td>';
    echo '<td style="font-size: 16px;"><strong>';
    echo '€ '. formatEuro($tempdapagare);
    echo '</strong></td>';
    echo '</tr>';

                                    ?>
            </table>
        </div>
    </div>




</div>
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




$ddataProvider = new ArrayDataProvider([
    'allModels' => $tmp['dettaglio'],
    'pagination' => [
        'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
    ],
    'sort' => [
        'attributes' => ['x_scdesc', 'descli', 'struttura', 'guest'], // Attributi ordinabili
    ],
]);
// Crea il menu di esportazione
yii::error($ddataProvider);
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

 

echo ExportMenu::widget([
    'id' => 'exp_button'.preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']), // Imposta l'ID per il bottone
    'dataProvider' =>  $ddataProvider,
    'columns' => $zgridColumns,
    'exportConfig' => $exportConfig,
    'filename' => 'Export_' . date('Y-m-d_H-i-s'),
    'target' => ExportMenu::TARGET_BLANK,
    'showColumnSelector' => true,
    'clearBuffers' => true,
 //   'class'=>'pino',
    'dropdownOptions' => [
        'label' => 'Esporta Dati',
        'class' => 'PErsonale', // Solo classi personalizzate
        'title' => 'Esporta i dati nel formato selezionato',
        'data-toggle' => 'dropdown',
        'aria-haspopup' => 'true',
        'aria-expanded' => 'false',
    ],
]);
?>


<br>Dettaglio<br>

<style>
    .euro-column {
    min-width: 190px; /* Puoi aumentare il valore se serve più spazio */
    text-align: right; /* Allinea a destra per una migliore leggibilità */
}
.euro-total{
    min-width: 190px; /* Puoi aumentare il valore se serve più spazio */
    text-align: right; /* Allinea a destra per una migliore leggibilità */
    white-space: nowrap;
}

 

    </style>

<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <!--th>Commessa</th-->
        <!--th>Cliente</th-->
        <th>Cognome - Nome</th>
        <th>RUOLO</th>
        <th>PARTY</th>
        <th>STRUTTURA</th>
        <th>DATA IN</th>
        <th>DATA OUT</th>
        <th>TOT.NOTTI</th>
        <th>Notti Tax</th>
        <th>CONF.</th>
        <th>TIPOLOGIA</th>
        <th>COSTO Notte</th>
        <th>CITY TAX</th>
        <th>TOT COST</th>
        <th>TOT CITY</th>
        <th>TAX PARK</th>
        <th>EXTRAS</th>
        <th>TOTALE Complessivo</th>
        <th>7%</th>
        <th>FEE</th>
        <th>IVA</th>

    </thead>
    <?php
    $tqta = 0;
    $ttotale = 0;
    $ttax = 0;
    $tfee = 0;
    $tiva = 0;
    $prev_scdesc = '';
    $prev_descli = '';
    $prev_struttura = '';

    foreach ($tmp['dettaglio'] as $value) {

  // Check if x_scdesc, descli, or struttura has changed
        if ($prev_scdesc != '' && ($prev_scdesc != $value['x_scdesc'] || $prev_descli != $value['descli'] || $prev_struttura != $value['struttura'])) {
            // Print the totals for the previous group
            echo '<tr class="table-total bg-primary">';
            echo '<td colspan="6"  style="font-size: 16px;"><strong>Totale per ' . $prev_scdesc . ' - ' . $prev_descli . ' - ' . $prev_struttura . '</strong></td>';
            echo '<td style="font-size: 16px;" ><strong>' . $tqta . '</strong></td>';
            echo '<td></td>'; // Colonna vuota per "Tipologia"
            echo '<td></td>'; // Colonna vuota per "Costo Notte"
            echo '<td></td>'; // Colonna vuota per "City Tax"
            echo '<td style="font-size: 16px;" class="euro-total text-end"><strong>€ ' . formatEuro($ttotale, 2) . '</strong></td>';
            echo '<td style="font-size: 16px;" class="euro-total text-end"><strong>€ ' . formatEuro($ttax, 2) . '</strong></td>';
            echo '<td></td>'; // Colonna vuota per "Tax Park"
            echo '<td></td>'; // Colonna vuota per "Extras"
            echo '<td></td>'; // Colonna vuota per "Tax Park"
            echo '<td></td>'; // Colonna vuota per "Extras
            echo '<td style="font-size: 16px;" class="euro-total text-end" ><strong>€ ' . formatEuro($ttotale + $ttax, 2) . '</strong></td>';
            echo '<td></td>'; // Colonna vuota per "7%"
            echo '<td style="font-size: 16px;" class="euro-total text-end" ><strong>€ ' . formatEuro($tfee, 2) . '</strong></td>';
            echo '<td style="font-size: 16px;" class="euro-total text-end" ><strong>€ ' . formatEuro($tiva, 2) . '</strong></td>';
            // echo '<td colspan="3"></td>'; // Colonne vuote per "Note di riga", "Note Hotel", "Cancellation Policy"
            echo '</tr>';

            // Reset totals
            $tqta = 0;
            $ttotale = 0;
            $ttax = 0;
            $tfee = 0;
            $tiva = 0;
        }

        // Print the current row
        echo '<tr>';
       // echo '<td scope="col">' . $value['x_scdesc'] . '</td>';
       // echo '<td scope="col">' . $value['descli'] . '</td>';
        echo '<td scope="col">' . $value['guest'] . '</td>';
        echo '<td scope="col">' . $value['ruolo'] . '</td>';
        echo '<td scope="col">' . $value['party'] . '</td>';
        echo '<td scope="col">' . $value['struttura'] . '</td>';
        echo '<td scope="col">' . date('d/m/Y', strtotime($value['check_in'])) . '</td>';
        echo '<td scope="col">' . date('d/m/Y', strtotime($value['check_out'])) . '</td>';
        echo '<td scope="col">' . $value['qta'] . '</td>';
        echo '<td scope="col">' . $value['qta'] . '</td>';
        echo '<td scope="col"> si</td>';
        echo '<td scope="col"> ' . $value['cd_Ar'] . '</td>';
        echo '<td scope="col" class="euro-column text-end"> € ' . formatEuro($value['prezzo'], 2) . '</td>';
        echo '<td scope="col" class="euro-column text-end"> € ' . formatEuro($value['tax_unit'], 2) . '</td>';
        echo '<td scope="col" class="euro-column text-end"> € ' . formatEuro($value['totale'], 2) . '</td>';
        echo '<td scope="col" class="euro-column text-end"> € ' . formatEuro($value['tax'], 2) . '</td>';
        echo '<td scope="col"> </td>';
        echo '<td scope="col"> </td>';
        echo '<td scope="col" class="euro-column text-end"> € ' .formatEuro($value['totale'] +  $value['tax'] ) . '</td>';
        echo '<td scope="col">' . formatEuro($value['fee_perc'], 2) . '% </td>';
        echo '<td scope="col" class="euro-column text-end"> € ' . formatEuro($value['fee'], 2) . ' </td>';
        echo '<td scope="col" class="euro-column text-end"> € ' . formatEuro($value['iva'], 2) . ' </td>';
        echo '</tr>';

        // Accumulate totals
        $tqta += $value['qta'];
        $ttotale += round($value['totale'], 2);
        $ttax += round($value['tax'], 2);
        $tfee += round($value['fee'], 2);
        $tiva += round($value['iva'], 2);

        // Update the previous values
        $prev_scdesc = $value['x_scdesc'];
        $prev_descli = $value['descli'];
        $prev_struttura = $value['struttura'];
    }

    // Print the totals for the last group
    if ($prev_scdesc != '') {
        echo '<tr class="table-total bg-primary">';
        echo '<td colspan="6" style="font-size: 16px;"><strong>Totale per ' . $prev_scdesc . ' - ' . $prev_descli . ' - ' . $prev_struttura . '</strong></td>';
        echo '<td  style="font-size: 16px;"><strong>' . $tqta . '</strong></td>';
         echo '<td></td>'; // Colonna vuota per "Tipologia"
        echo '<td></td>'; // Colonna vuota per "Costo Notte"
        echo '<td></td>'; // Colonna vuota per "City Tax"
        echo '<td></td>'; // Colonna vuota per "Costo Notte"
        echo '<td></td>'; // Colonna vuota per "City Tax"
        echo '<td style="font-size: 16px;" class="euro-total text-end"><strong>€ ' . formatEuro($ttotale, 2) . '</strong></td>';
        echo '<td style="font-size: 16px;" class="euro-total text-end" ><strong>€ ' . formatEuro($ttax, 2) . '</strong></td>';
        echo '<td></td>'; // Colonna vuota per "Tax Park"
        echo '<td></td>'; // Colonna vuota per "Extras"
        echo '<td style="font-size: 16px;" class="euro-total text-end" ><strong>€ ' . formatEuro($ttotale + $ttax, 2) . '</strong></td>';
        echo '<td></td>'; // Colonna vuota per "7%"
        echo '<td style="font-size: 16px;" class="euro-total text-end" ><strong>€ ' . formatEuro($tfee, 2) . '</strong></td>';
        echo '<td style="font-size: 16px;" class="euro-total text-end" ><strong>€ ' . formatEuro($tiva, 2) . '</strong></td>';
        //  echo '<td colspan="3"></td>'; // Colonne vuote per "Note di riga", "Note Hotel", "Cancellation Policy"
        echo '</tr>';
    }
    ?>
</table>
  <?php Modal::end(); ?>
<?php endforeach; ?>











<?php 

Modal::begin([
    'id' => 'file-modal',
    'title' => 'Elenco File',
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
?>
    <?php echo '<table id="product-files" class="table table-condensed table-bordered">';
                    echo '<thead>';
                    echo '<tr>';
                    //echo '<th>id_agenda</th>';
                    echo '<th width="70%">file</th>';
                    echo '<th width="30%">';
                    echo '</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';

                    //yii::error( ($model->xid_testa ));
                    //yii::warning($model->filesall);
                    foreach ($model->filesall as $value) {
                        // echo '<tr>';

                        if ($value['entita'] = 'Xtravelrow') {
                            echo '<tr>';
                            echo '<td>';

                            echo Html::a(
                                $value['nomefile'],
                                [
                                    'allfiles/genfile',
                                    'id' => $value['id'],
                                    'file' => str_replace(' ', '_', $value['nomefile'])
                                ]
                            );

                            echo '</td>';

                            echo '<td>';

                            if ($value['origine'] == 'S') {
                                //echo 'usrld';

                                echo //Yii::$app->fontawesome->name('github', 'brands')->fill->('#003865');
                                Yii::$app->fontawesome->name(
                                    'user',
                                    'solid'
                                )->fill('#003865');
                            } else {

                                echo //Yii::$app->fontawesome->name('github', 'brands')->fill->('#003865');
                                Yii::$app->fontawesome->name(
                                    'server',
                                    'solid'
                                )->fill('#003865');
                            }

                            echo '</td>';

                            echo '</tr>';
                        }
                    }
                    echo '<tr id="files-new-parcel-block" style="display: none;">';
                    echo '</tr>';
                    echo '</tbody>';
                    echo '</table>';
                    ?>


<?php Modal::end() ?>
<?php
Modal::begin([
    'id' => 'elecli-modal',
    'title' => 'Elenco Clienti',
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
?>
<?php
 $previousClient = null;
 $totals = [
     'mprezzo' => 0,
     'imponibile' => 0,
     'ctax' => 0,
     'iva' => 0,
     'totale' => 0,
     'fee' => 0,
     'totaleimpfatt' => 0
 ];

 // Funzione per chiudere la tabella e mostrare i totali

 foreach ($roomlist['tappetourcli'] as $value) {
     if ($previousClient !== $value['descli']) {
         // Se non è il primo cliente, chiudi la tabella precedente
         if ($previousClient !== null) {
             closeTable($totals);
         }

         // Inizia una nuova tabella per il nuovo cliente
         echo '<h3>Elenco Clienti</h3>
         <table class="table table-sm table-hover table-responsive-sm table-fit">
         <thead class="thead-dark">
         <th>Cliente</th>
         <th>TAPPA</th>    
         <th>Imponibile Hotel</th>
         <th>CityTax</th>
         <th>IVA Hotel</th>
         <th>Totale Pagato Hotel</th>
         <th>FEE Agenzia</th>
         <th>FT da Emettere Imponibile</th>
       
     </thead>';

         // Resetta i totali
         $totals = [
             'mprezzo' => 0,
             'imponibile' => 0,
             'ctax' => 0,
             'iva' => 0,
             'totale' => 0,
             'fee' => 0,
             'totaleimpfatt' => 0
         ];

         $previousClient = $value['descli'];
     }

     // Aggiungi i dati alla tabella
     echo '<tr>';
     echo '<td>' . $value['descli'] . '</td>';
     echo '<td>' . $value['citta'] . '</td>';
     echo '<td>€ ' . formatEuro($value['mprezzo'], 2) . '</td>';
     echo '<td>€ ' . formatEuro($value['imponibile'], 2) . '</td>';
     echo '<td>€ ' . formatEuro($value['ctax'], 2) . '</td>';
     echo '<td>€ ' . formatEuro($value['iva'], 2) . '</td>';
     echo '<td>€ ' . formatEuro($value['totale'], 2) . '</td>';
     echo '<td>€ ' . formatEuro($value['fee'], 2) . '</td>';
    // echo '<td>€ ' . formatEuro($value['totaleimpfatt'], 2) . '</td>';
    // echo '<td>€ ' . formatEuro($value['totaleimpfatt']  + $value['fee'] ) . '</td>';
     echo '</tr>';

     // Aggiorna i totali
     $totals['mprezzo'] += $value['mprezzo'];
     $totals['imponibile'] += $value['imponibile'];
     $totals['ctax'] += $value['ctax'];
     $totals['iva'] += $value['iva'];
     $totals['totale'] += $value['totale'];
     $totals['fee'] += $value['fee'];
     $totals['totaleimpfatt'] += $value['totaleimpfatt'];
 }

 // Chiudi l'ultima tabella
 closeTable($totals);




?>
<?php Modal::end() ?>


<?php
Modal::begin([
    'id' => 'tool-modal',
    'title' => 'Tools',
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

/*
            $mdettaglio = ($dettaglio_);
            $mth_id =($model->th_id);
            $mpivot =($pivot_);
            $mlabels = ($labels_);
            $mseries = ($series_);
            $mtappe = ($uniqueTappe_);
            $mroomlist =($roomlist_);
            $manalisitappe =($analisitappe_);
            $mlistatappetool =($listatappetool_);
            $mlistaart = ($listaart_);*/
?>


<?php
echo rand();
$db = Yii::$app->db5;

$command = $db->createCommand("select cd_citta as ID,descrizione as Desk from x_citta order by cd_citta asc ");
$lcittà = $command->queryAll();
$lcittà2 = ArrayHelper::map($lcittà, 'ID', 'Desk');



// Aggiungi qui il pulsante per aprire la modale
echo Html::button('Aggiungi Tappe', [
    'class' => 'btn btn-primary mt-3',
    'data-toggle' => 'modal',
    'data-target' => '#tappeModal',
]);

Modal::begin([
    'id' => 'tappeModal',
    'title' => '<h4>Scegli le Tappe del Tour</h4>', // Imposta il titolo della modale qui
]);

echo '<div id="modalContent">';
echo Html::beginForm(['xtravelhead/savetappe'], 'post', ['id' => 'tappe-form']);

echo '<div class="form-group">';
echo Html::label('Numero di Tappe', 'num-tappe');
echo Html::input('number', 'num-tappe', null, ['class' => 'form-control', 'id' => 'num-tappe']);
echo '</div>';

echo '<div id="tappe-fields">';
// I campi per le tappe verranno aggiunti qui via JavaScript
echo '</div>';

echo Html::submitButton('Salva', ['class' => 'btn btn-success']);
echo Html::endForm();

echo '</div>';

Modal::end();



?>


<br>Dettaglio tappe<br>
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>Citta</th>
        <th>data</th>
        <th>evaso</th>
        <th>id</th>

    </thead>
    <?php
    foreach ($mlistatappetool as $value) {
        echo '<tr>';
        echo '<td>';
        echo $value['citta'];
        echo '</td>';
        echo '<td>';
        echo $value['data'];
        echo '</td>';
        echo '<td>';
        echo $value['evaso'] == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-stop"></i>';
        echo '</td>';
        echo '<td>';
        echo $value['id_tappa'];
        echo '</td>';

        echo '</tr>';
    }

    ?>
</table>

<?php
// Pulsante per aprire la modale per l'inserimento dei nominativi
echo Html::button('Aggiungi Nominativo', [
    'class' => 'btn btn-primary mt-3',
    'data-toggle' => 'modal',
    'data-target' => '#nominativoModal',
]);

Modal::begin([
    'id' => 'nominativoModal',
    'title' => '<h4>Aggiungi Nominativo</h4>',
]);

echo '<div id="modalNominativoContent">';
echo Html::beginForm(['xtravelhead/savenominativo'], 'post', ['id' => 'nominativo-form']);

echo '<div class="form-group">';
echo Html::label('Nominativo', 'nominativo');
echo Html::input('text', 'nominativo', null, ['class' => 'form-control', 'id' => 'nominativo']);
echo '</div>';

echo '<div class="form-group">';
echo Html::label('cd_Ar', 'cd_ar');

$listaart2 = ArrayHelper::map($listaart, 'ID', 'Desk');
yii::error($listaart2);
echo Select2::widget([
    'name' => 'cd_Ar',
    'data' => $listaart2, // Array contenente le opzioni per il cd_Ar
    'options' => [
        'placeholder' => 'Seleziona cd_Ar...',
        'id' => 'cd_Ar',
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 0
    ],
]);



echo '</div>';
echo '<div class="form-group">';
echo Html::label('Nota', 'Nota');
echo Html::textarea('nota', null, ['class' => 'form-control', 'id' => 'nota', 'rows' => 5]); // Specifica il numero di righe
echo '</div>';
echo '<div class="form-group">';
echo Html::label('th_id', 'th_id');
echo Html::input('text', 'th_id', $mth_id, ['class' => 'form-control', 'id' => 'th_id', 'readonly' => true]); // Campo non modificabile
echo '</div>';
echo Html::submitButton('Salva', ['class' => 'btn btn-success']);
echo Html::endForm();

echo '</div>';

Modal::end();


?>


<br>Dettaglio RoomList<br>
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>Citta</th>
        <th>data</th>
        <th>nota</th>
        <th>evaso</th>

    </thead>
    <?php
    foreach ($mroomlist as $value) {
        echo '<tr>';
        echo '<td>';
        echo isset($value['guest'])? $value['guest']:'' ;
        echo '</td>';
        echo '<td>';
        echo isset($value['cd_Ar'])?$value['cd_Ar']:'';
        echo '</td>';
        echo '<td>';
        echo isset($value['note'])?$value['note']:'';
        echo '</td>';
        echo '<td>';
        echo(isset($value['evaso'])?$value['evaso']:'' )== 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-stop"></i>';
        echo '</td>';

        echo '</tr>';
    }

    ?>
</table>


<script>
    



    $('#num-tappe').on('change', function() {
        var numTappe = $(this).val();
        var tappeFields = $('#tappe-fields');
        tappeFields.empty();
        console.log("Generazione campi tappe...");

        // Serializza l'array delle città per utilizzarlo nel JavaScript
        var cittaOptions = <?php echo json_encode($lcittà2); ?>;
        var cittaOptionsUrl = "<?php echo \Yii::$app->urlManager->createUrl(['xtravelhead/xcaricacitta']); ?>";

        for (var i = 1; i <= numTappe; i++) {
            var select2Id = 'citta-tappa-' + i;

            tappeFields.append(
                '<div class="form-group">' +
                '<label for="data-tappa-' + i + '">Data Tappa ' + i + '</label>' +
                '<input type="date" name="Tappe[' + i + '][data]" class="form-control" id="data-tappa-' + i + '">' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="' + select2Id + '">Città Tappa ' + i + '</label>' +
                '<select name="Tappe[' + i + '][citta]" class="form-control select2" id="' + select2Id + '"></select>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="id-tappa-' + i + '">id Tappa ' + i + '</label>' +
                '<input type="text" name="Tappe[' + i + '][id]" class="form-control" id="id-tappa-' + i + '"  placeholder="<?php echo $mth_id; ?>" value="<?php echo $mth_id; ?>">' +
                '</div>'
            );
            console.log($.fn.select2);

            // Inizializza Select2 con dati statici
            /*    $('#' + select2Id).select2({
                    placeholder: 'Seleziona una città...',
                    allowClear: true,
                    minimumInputLength: 3,
                    data: $.map(cittaOptions, function(value, key) {
                        return {
                            id: key,
                            text: value
                        };
                    })
                });*/

            $('#' + select2Id).select2({
                placeholder: 'Seleziona una città...',
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: cittaOptionsUrl, // Questo è l'URL dell'AJAX
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // Invia il termine di ricerca al server
                        };
                    },
                    processResults: function(data) {
                        console.log(data);
                        return {

                            results: data.items
                        };
                    },
                    cache: true
                }
            });

        }
    });

    $(document).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = $.noop;
        $('#nominativoModal').removeAttr('tabindex');
        $('#tappeModal').removeAttr('tabindex');


        $('#tappe-form').on('submit', function(event) {
            event.preventDefault(); // Previene il normale submit del form

            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                data: $(this).serialize(),
                success: function(response) {
                    console.log(response);

                    if (response.success) {
                        $('#tappeModal').modal('hide');

                        // Ricarica solo il tab con l'ID 'tools'
                        $.ajax({
                            url: location.href, // Usa l'URL corrente per ricaricare il tab
                            type: 'get',
                            success: function(html) {
                                var newTabContent = $(html).find('#w3-tab0').html(); // Trova il contenuto del tab
                                console.log('dentro success');
                                console.log(location.href);
                                $('#tools').html(newTabContent); // Aggiorna il contenuto del tab
                            },
                            error: function() {
                                alert('Si è verificato un errore nella richiesta. ajax');
                            }
                        });
                    } else {
                        alert('Errore durante il salvataggio del nominativo.');
                    }
                },
                error: function() {
                    alert('Si è verificato un errore nella richiesta. post ');
                }
            });

            return false; // Impedisce il submit normale
        });
        console.log('JS Loaded2');
        // Collega nuovamente gli eventi
        $('#nominativo-form').on('submit', function(event) {
            event.preventDefault(); // Previene il normale submit del form

            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                data: $(this).serialize(),
                success: function(response) {
                    console.log(response);

                    if (response.success) {
                        $('#nominativoModal').modal('hide');

                        // Ricarica solo il tab con l'ID 'tools'
                        $.ajax({
                            url: location.href, // Usa l'URL corrente per ricaricare il tab
                            type: 'get',
                            success: function(html) {
                                var newTabContent = $(html).find('#tools').html(); // Trova il contenuto del tab
                                $('#tools').html(newTabContent); // Aggiorna il contenuto del tab
                            },
                            error: function() {
                                alert('Si è verificato un errore nella richiesta.');
                            }
                        });
                    } else {
                        alert('Errore durante il salvataggio del nominativo.');
                    }
                },
                error: function() {
                    alert('Si è verificato un errore nella richiesta.');
                }
            });

            return false; // Impedisce il submit normale
        });




    });
</script>




<?php Modal::end() ?>