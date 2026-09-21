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
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
$table_tmpid=uniqid();
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

    <!-- Carica jQuery (PRIMA di DataTables) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Carica DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- Carica DataTables JS (DOPO jQuery) -->
   <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
  
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<?php //echo $struttura;

if (isset($struttura)) {
   
$struttura_desiderata = $struttura; // Valore da mantenere

$filteredData = array_filter($xttmp, function($row) use ($struttura_desiderata) {
    return isset($row['struttura']) && $row['struttura'] === $struttura_desiderata;
});

// Reindicizza l'array per evitare chiavi sparse
$filteredData = array_values($filteredData);

//print_r($filteredData);

}
if (isset($servizio)) {

//echo $servizio;
$struttura_desiderata = $servizio;
$filteredData =$xttmp;
//$filteredData = array_map(function($row) use ($servizio) {
//    if (isset($row['cd_Ar'])) { // Controlla che la chiave 'cd_Ar' esista
//        $row['struttura'] =$servizio; //$row['cd_Ar']; // Aggiorna 'struttura' con 'cd_Ar'
//    }
//    return $row;
//}, $xttmp);
 

$db2 = Yii::$app->db2; // Connessione a DB2

foreach ($filteredData as &$row) {
    $cd_ar = $row['cd_Ar']; // Prendi il valore di cd_ar corrente
  
$result = (new \yii\db\Query())
        ->select(['ARClasse12.Classe'])
        ->from('adb_auxcoop.dbo.ar')
        ->leftJoin('adb_auxcoop.dbo.ARClasse12', 'ar.Cd_ARClasse1 = ARClasse12.Cd_ARClasse1 
        AND ar.Cd_ARClasse2 = ARClasse12.Cd_ARClasse2')
        ->where(['ar.cd_ar' => $cd_ar])
        ->one();

    // Se trovi un risultato, aggiorna il valore di struttura
    //yii::error($sql);
    
    //yii::error($result);
    $row['struttura'] = $result['Classe'] ?? null;
}
unset($row); // Buona pratica per evitare riferimenti indesiderati

$filteredData = array_filter($filteredData, function($row) use ($servizio) {
    return isset($row['struttura']) && $row['struttura'] === $servizio;
});

$filteredData = array_values($filteredData);

}
?>


<?php
 

 

$tddataProvider = new ArrayDataProvider([
    'allModels' => $filteredData,  // Passa i tuoi dati qui
    'pagination'=>false,
    'sort' => [
        'attributes' => [
            'guest',
            'ruolo',
            'party',
            'struttura',
            'check_in',
            'check_out',
            'qta',
            'Fee_perc',
            'fee',
            'iva',
            'citta_da',
            'citta_a',
            // Aggiungi altri attributi se necessario
        ],  // Definisce gli attributi che l'utente può ordinare
    ],
]);


if (isset($struttura)){

     
$columns = 
[
   // ['class'=>'kartik\grid\SerialColumn', 'order'=>DynaGrid::ORDER_FIX_LEFT],
    
[
        'attribute'=>'guest',
   //     'pageSummary'=>'Page Total',
        'vAlign'=>'middle',
    ],
   [
        'attribute'=>'ruolo',
        'vAlign'=>'middle',
    ],
       [
        'attribute'=>'party',
        'vAlign'=>'middle',
    ],
   [
        'attribute'=>'struttura',
        'vAlign'=>'middle',
    ],
       [
        'attribute'=>'check_in',
        'vAlign'=>'middle',
    ],
           [
        'attribute'=>'check_out',
        'vAlign'=>'middle',
    ],
               [
        'attribute'=>'qta',
        'vAlign'=>'middle',
    ],
                   [
        'attribute'=>'qta',
        'vAlign'=>'middle',
        'label'=>'Notti Tax',
    ],
      [
       // 'attribute'=>'qta',
        'vAlign'=>'middle',
        'label'=>'Conferma',
        'value'=>function ($model, $key, $index, $widget) {
            return 'si';
        },
    ],
      [
       // 'attribute'=>'qta',
        'vAlign'=>'middle',
        'label'=>'Tipologia',
        'value'=>function ($model, $key, $index, $widget) {
            return $model['cd_Ar'];
        },
    ],
    [
       // 'attribute'=>'qta',
        'vAlign'=>'middle',
        'label'=>'Costo Notte',
        'value'=>function ($model, $key, $index, $widget) {
            return formatEuro($model['prezzo'], 2) ;
        },
        
    ],
    [
       // 'attribute'=>'qta',
        'vAlign'=>'middle',
        'label'=>'City Tax',
        'value'=>function ($model, $key, $index, $widget) {
            return formatEuro($model['tax_unit'], 2) ;
        },
    ],
        [
       // 'attribute'=>'qta',
        'vAlign'=>'middle',
        'label'=>'Tot Costo',
        'value'=>function ($model, $key, $index, $widget) {
            return formatEuro($model['prezzo'] *  $model['qta'] );
        },
    ],
            [
        'vAlign'=>'middle',
        'label'=>'TotCity Tax',
        'value'=>function ($model, $key, $index, $widget) {
   return formatEuro($model['tax_unit'] *  $model['qta'] );
        },
    ],
         [
        'vAlign'=>'middle',
        'label'=>'Totale Complessivo',
        'value'=>function ($model, $key, $index, $widget) {
   return  (formatEuro($model['totale'] +  $model['tax'] ));
        },
    ],

                      [
        'attribute'=>'Fee_perc',
        'vAlign'=>'middle',
        'label'=>'Fee %',
    ],
                      [
        'attribute'=>'fee',
        'vAlign'=>'middle',
        'label'=>'fee',
    ],
                      [
        'attribute'=>'iva',
        'vAlign'=>'middle',
        'label'=>'iva',

]];
}
else {
  $columns = 
[
   // ['class'=>'kartik\grid\SerialColumn', 'order'=>DynaGrid::ORDER_FIX_LEFT],
   [ 'attribute'=>'tr_id'],
[
        'attribute'=>'guest',
       // 'pageSummary'=>'Page Total',
        'vAlign'=>'middle',
    ],
 [
        'attribute'=>'ruolo',
        'vAlign'=>'middle',
    ],
       [
        'attribute'=>'party',
        'vAlign'=>'middle',
    ],
     ['label'=>'Servizio',
        'attribute'=>'cd_Ar',
        'vAlign'=>'middle',
    ],
       [
        'label'=>'Tipo Servizio',
        'attribute'=>'struttura',
   
        'vAlign'=>'middle',
    ],
           [
        'label'=>'Data partenza',
        'attribute'=>'check_in',
        'vAlign'=>'middle',
    ],
   [
        'label'=>'Da ',
        'attribute'=>'citta_da',
        'vAlign'=>'middle',
    ],
   [
        'label'=>'A ',
        'attribute'=>'citta_a',
        'vAlign'=>'middle',
    ],
   [
        'label'=>'PNR',
        'attribute'=>'pnr',
        'vAlign'=>'middle',
    ],
   [
        'label'=>'Biglietto',
        'attribute'=>'nr_biglietto',
        'vAlign'=>'middle',
    ],
     [
        'label'=>'Qta',
        'attribute'=>'qta',
        'vAlign'=>'middle',
    ],
         [
        'label'=>'Data Pagamento',
        'attribute'=>'data_pg',
        'vAlign'=>'middle',
    ],
             [
        'label'=>'tipo Pagamento',
        'attribute'=>'cd_pg',
        'vAlign'=>'middle',
    ],
                 [
        'label'=>'Prezzo',
        'attribute'=>'prezzo',
        'vAlign'=>'middle',
        'format' => ['currency', 'EUR'],
    ],
    [
        'vAlign'=>'middle',
        'label'=>'Totale Complessivo',
        'value'=>function ($model, $key, $index, $widget) {
   return   ($model['totale'] +  $model['tax'] );
        },
    'format' => ['currency', 'EUR'],
    ],

                      [
        'attribute'=>'Fee_perc',
        'vAlign'=>'middle',
        'label'=>'Fee %',
    ],
                      [
        'attribute'=>'fee',
        'vAlign'=>'middle',
        'label'=>'fee',
        'format' => ['currency', 'EUR'],
        
    ],
                      [
        'attribute'=>'iva',
        'vAlign'=>'middle',
        'label'=>'iva',
        'format' => ['currency', 'EUR'],

]
];

 
}

/*
$dynagrid = DynaGrid::begin([
    'columns' => $columns,
    'theme' => 'panel-info',
    'showPersonalize' => true,  // Abilita la personalizzazione delle colonne
    'storage' => 'session',     // Memorizza le preferenze utente in sessione
    
    'gridOptions' => [
        'dataProvider' => $tddataProvider,
        'showPageSummary' => true,
        'responsive' => true,
        'responsiveWrap' => true,
        'floatHeader' => true,
        'headerContainer' => ['class' => 'kv-table-header', 'style' => 'top: 50px'],
        'pjax' => true,  // Abilita PJAX per il caricamento asincrono
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="fas fa-book"></i>  Dettagli ' .
             (isset($struttura) ? $struttura : $servizio) . '</h3>',
            'before' =>  '<div style="padding-top: 7px;"><em>* The table header sticks 
            to the top in this demo as you scroll</em></div>',
            'after' => false,
        ],
        'toolbar' =>  [
            ['content' => '{dynagridSort}{dynagrid}'],
            '{export}',
        ],
    ],
    'options' => ['id'=>'grid-sub-hotel__'.$txid.preg_replace('/[^a-zA-Z0-9]/', '', $struttura??$servizio),
  ] // Aggiungi un identificativo univoco
]);
 
DynaGrid::end();
*/
/*
echo  
GridView::widget([
    'dataProvider' => $tddataProvider,
   // 'filterModel' => $searchModel,
   'id'=>'grid-sub-hotel'.$txid.preg_replace('/[^a-zA-Z0-9]/', '', 
   $struttura??($servizio??'')).uniqid(),
   // 'resizableColumnsOptions' => ['resizeFromBody' => true],
    'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
    'striped' => true,
    'condensed' => true,
    'columns' => $columns,
    'persistResize'=>true,
    'panel' => [
        'type' => $ris['grid_color']??'',
        'heading' => '<i class="fas  fa-user">Ospiti</i>',
        'headingOptions' => ['language' => 'it-It'],
],
    'responsive' => true,
    'resizableColumns' => true,
    'showPageSummary' => true,
    'pjax' => true,

    ]);

*/

?>
<?php 
 $xfilteredData=$filteredData;

yii::error($xfilteredData);
$jsonData = json_encode($xfilteredData);
$tab= uniqid() . '_' . preg_replace('/[^a-zA-Z0-9]/', '', ($struttura ?? ($servizio ?? ''))) . '_' . rand(1000, 9999);
//uniqid().'_'.preg_replace('/[^a-zA-Z0-9]/', '',($struttura??($servizio??'')));
//yii::error($tab);
 
 
$jsonData = json_encode($filteredData, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);

if ($jsonData === false) {
    print_r("Errore nella conversione JSON: " . json_last_error_msg());
}
 

?>
<script>
// Passa i dati PHP a JavaScript
var xtab_<?php echo $tab ;?> = <?php echo $jsonData ?: '[]' ; ?>;
//console.log('pino');
// console.log("Dati ricevuti1:", xtab_<?php echo $tab ;?>); // D
</script>
 <?php

// Array di dati simulato
$dati = [
    ["ID" => 1, "Nome" => "Mario", "Cognome" => "Rossi", "Età" => 30],
    ["ID" => 2, "Nome" => "Luca", "Cognome" => "Bianchi", "Età" => 25],
    ["ID" => 3, "Nome" => "Giulia", "Cognome" => "Verdi", "Età" => 28],
];


$selectedColumns = [
    "tr_id",
    "guest",
    "ruolo",
    "party",
    "cd_Ar",
    "struttura",
    "check_in",
    "citta_da",
    "citta_a",
    "pnr",
    "nr_biglietto",
    "qta",
    "data_pg",
    "cd_pg",
    "prezzo",
    "fee_perc",
    "fee",
    "iva"
];

$filteredData = array_map(function($item) use ($selectedColumns) {
    return array_intersect_key($item, array_flip($selectedColumns));
}, $xfilteredData);
// Creazione della tabella
$tRE=uniqid();
echo '<table id="'.$tab.'" class="display">';
echo '<thead><tr>';
foreach (array_keys($filteredData[0]) as $colonna) {
    echo "<th>$colonna</th>";
}
echo '</tr></thead><tbody>';
 
foreach ($filteredData as $riga) {
    echo '<tr>';
    foreach ($riga as $valore) {
        echo "<td>$valore</td>";
    }
    echo '</tr>';
}
 
echo '</tbody></table>';

 $t= json_encode($filteredData, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
$js = <<<JS
$(document).ready(function() {
    var xdata=$t;
    var tabbata='$tab';
    console.log(xdata);

    new DataTable(tabbata, {
        data: xdata , // I dati passati da PHP
        colReorder: true, // Abilita il drag & drop delle colonne
        paging: false, // Abilita la paginazione
        searching: true, // Abilita la ricerca
        order: [[0, 'asc']], // Ordina inizialmente per la prima colonna
     columns: [
            { data: "tr_id" },
            { data: "guest" },
            { data: "ruolo" },
            { data: "party" },
            { data: "cd_Ar" },
            { data: "struttura" },
            { data: "check_in" },
            { data: "citta_da" },
            { data: "citta_a" },
            { data: "pnr" },
            { data: "nr_biglietto" },
            { data: "qta" },
            { data: "data_pg" },
            { data: "cd_pg" },
            { data: "prezzo"  },
            { data: null },
            { data: "fee_perc" },
            { data: "fee"   },
            { data: "iva"  }
        ]
    });// Ordina inizialmente per la prima colonna);
 
});
JS;

// Register the JavaScript
$this->registerJs($js);



?>
<table id="tab_<?php echo $tab?>" class="display">
    <thead>
        <tr>
            <th>ID</th>
            <th>Guest</th>
            <th>Ruolo</th>
            <th>Party</th>
            <th>Servizio</th>
            <th>Tipo Servizio</th>
            <th>Data Partenza</th>
            <th>Da</th>
            <th>A</th>
            <th>PNR</th>
            <th>Biglietto</th>
            <th>Qta</th>
            <th>Data Pagamento</th>
            <th>Tipo Pagamento</th>
            <th>Prezzo</th>
            <th>Totale Complessivo</th>
            <th>Fee %</th>
            <th>Fee</th>
            <th>IVA</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>



 
function loadDataTables(callback) {
    if ($.fn.DataTable) {
        console.log("DataTables già caricato");
        callback();
    } else {
        console.log("Caricamento DataTables...");
        var script = document.createElement("script");
        script.src = "https://cdn.datatables.net/2.2.2/js/dataTables.js";
        script.onload = function () {
            console.log("DataTables caricato dinamicamente!");
            callback();
        };
        document.body.appendChild(script);
    }
}



$(document).ready(function() {
    console.log("JavaScript eseguito  <?php echo $tab ;?>!");
    console.log("Dati ricevuti2:", xtab_<?php echo $tab ;?>); // Debug
    // Check if DataTable already exists and destroy it
 if ($.fn.dataTable.isDataTable('#tab_<?php echo $tab; ?>')) {
        console.log("DataTable già inizializzato, lo distruggo...");
        //$('#tab_<?php echo $tab; ?>').DataTable().destroy();
        console.log("DataTable distrutto");
    }else{


          new DataTable('#tab_<?php echo $tab ;?>', {
        data: xtab_<?php echo $tab ;?> , // I dati passati da PHP
        colReorder: true, // Abilita il drag & drop delle colonne
        paging: true, // Abilita la paginazione
        searching: true, // Abilita la ricerca
        order: [[0, 'asc']], // Ordina inizialmente per la prima colonna
       
        columns: [
            { data: "tr_id" },
            { data: "guest" },
            { data: "ruolo" },
            { data: "party" },
            { data: "cd_Ar" },
            { data: "struttura" },
            { data: "check_in" },
            { data: "citta_da" },
            { data: "citta_a" },
            { data: "pnr" },
            { data: "nr_biglietto" },
            { data: "qta" },
            { data: "data_pg" },
            { data: "cd_pg" },
            { data: "prezzo"  },
            { data: null },
            { data: "fee_perc" },
            { data: "fee"   },
            { data: "iva"  }
        ]
    });
}
});
 