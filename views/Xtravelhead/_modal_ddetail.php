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
<link href="https://unpkg.com/tabulator-tables@6.3.1/dist/css/tabulator.min.css" rel="stylesheet">
 <script type="text/javascript" src="https://unpkg.com/tabulator-tables@6.3.1/dist/js/tabulator.min.js"></script>






<?php //echo $struttura;
$filteredData=[];
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

echo $servizio;
$struttura_desiderata = $servizio;
$filteredData = array_map(function($row) {
    if (isset($row['cd_Ar'])) { // Controlla che la chiave 'cd_Ar' esista
        $row['struttura'] = $row['cd_Ar']; // Aggiorna 'struttura' con 'cd_Ar'
    }
    return $row;
}, $xttmp);
}
?>
<table  class="table table-sm table-hover table-responsive-sm table-fit">
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
        <th>Totale</th>
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

    foreach ($filteredData as $value) {

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

 

<?php
 
 

 

$tddataProvider = new ArrayDataProvider([
    'allModels' => $filteredData,  // Passa i tuoi dati qui
    'pagination' => [
        'pageSize' => 20, // Definisce la paginazione
    ],
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
        'label'=>'Totale',
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
    
[
        'attribute'=>'guest',
        'pageSummary'=>'Page Total',
        'vAlign'=>'middle',
    ],];
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
            'heading' => '<h3 class="panel-title"><i class="fas fa-book"></i>  Dettagli ' . (isset($struttura) ? $struttura : $servizio) . '</h3>',
            'before' =>  '<div style="padding-top: 7px;"><em>* The table header sticks to the top in this demo as you scroll</em></div>',
            'after' => false,
        ],
        'toolbar' =>  [
            ['content' => '{dynagridSort}{dynagrid}'],
            '{export}',
        ],
    ],
    'options' => ['id' => 'dynagrid-1'.uniqid()] // Aggiungi un identificativo univoco
]);
 
DynaGrid::end();*/
?>



 
<?php
use yii\web\View;

// Registra gli asset necessari
$this->registerCssFile('https://unpkg.com/tabulator-tables@5.5.0/dist/css/tabulator.min.css');
$this->registerJsFile('https://unpkg.com/luxon@3.4.3/build/global/luxon.min.js', ['position' => View::POS_HEAD]);
$this->registerJsFile('https://unpkg.com/tabulator-tables@5.5.0/dist/js/tabulator.min.js', ['position' => View::POS_HEAD]);

// Prepara i dati per Tabulator
$tabulatorData = [];
foreach ($filteredData as $row) {
    $tabulatorData[] = [
        'guest' => $row['guest'],
        'ruolo' => $row['ruolo'],
        'party' => $row['party'],
        'struttura' => $row['struttura'],
        'check_in' => date('Y-m-d', strtotime($row['check_in'])),
        'check_out' => date('Y-m-d', strtotime($row['check_out'])),
        'qta' => $row['qta'],
        'cd_Ar' => $row['cd_Ar'],
        'prezzo' => $row['prezzo'],
        'tax_unit' => $row['tax_unit'],
        'totale' => $row['totale'],
        'tax' => $row['tax'],
        'totale_completo' => $row['totale'] + $row['tax'],
        'fee_perc' => $row['fee_perc'],
        'fee' => $row['fee'],
        'iva' => $row['iva'],
        'x_scdesc' => $row['x_scdesc'],
        'descli' => $row['descli']
    ];
}
?>

<div id="example-table<?=$table_tmpid?>"></div>

<?php
$jsonData = json_encode($tabulatorData);
$script = <<<JS
// Definizione delle colonne
var columns = [
    {title: "Cognome - Nome", field: "guest", headerFilter:"input" ,  },
    {title: "RUOLO", field: "ruolo", headerFilter:"input"},
    {title: "PARTY", field: "party", headerFilter:"input"},
    {title: "STRUTTURA", field: "struttura", headerFilter:"input"},
{title: "DATA IN", field: "check_in", formatter: function(cell) {
        const date = luxon.DateTime.fromISO(cell.getValue());
        return date.toFormat('dd/MM/yyyy');
    }},
    {title: "DATA OUT", field: "check_out", formatter: function(cell) {
        const date = luxon.DateTime.fromISO(cell.getValue());
        return date.toFormat('dd/MM/yyyy');
    }},
    {title: "TOT.NOTTI", field: "qta", hozAlign: "right"},
    {title: "Notti Tax", field: "qta", hozAlign: "right"},
    {title: "CONF.", field: "conf", formatter: "tickCross"},
    {title: "TIPOLOGIA", field: "cd_Ar"},
    {title: "COSTO Notte", field: "prezzo", formatter: "money", formatterParams: {
        symbol: "€",
        precision: 2
    }, hozAlign: "right"},
    {title: "CITY TAX", field: "tax_unit", formatter: "money", formatterParams: {
        symbol: "€",
        precision: 2
    }, hozAlign: "right"},
    {title: "TOT COST", field: "totale", formatter: "money", formatterParams: {
        symbol: "€",
        precision: 2
    }, hozAlign: "right"},
    {title: "TOT CITY", field: "tax", formatter: "money", formatterParams: {
        symbol: "€",
        precision: 2
    }, hozAlign: "right"},
    {title: "Totale", field: "totale_completo", formatter: "money", formatterParams: {
        symbol: "€",
        precision: 2
    }, hozAlign: "right", bottomCalc:"sum", bottomCalcFormatter:"money", bottomCalcFormatterParams:{
        symbol: "€",
        precision: 2
    }},
    {title: "7%", field: "fee_perc", formatter: "number", formatterParams: {
        precision: 2,
        suffix: "%"
    }, hozAlign: "right"},
    {title: "FEE", field: "fee", formatter: "money", formatterParams: {
        symbol: "€",
        precision: 2
    }, hozAlign: "right", bottomCalc:"sum", bottomCalcFormatter:"money", bottomCalcFormatterParams:{
        symbol: "€",
        precision: 2
    }},
    {title: "IVA", field: "iva", formatter: "money", formatterParams: {
        symbol: "€",
        precision: 2
    }, hozAlign: "right", bottomCalc:"sum", bottomCalcFormatter:"money", bottomCalcFormatterParams:{
        symbol: "€",
        precision: 2
    }}
];


// Funzione per salvare l'ordine delle colonne
function saveColumnOrder(columns) {
    localStorage.setItem("tabulatorColumns", JSON.stringify(columns));
}

// Funzione per caricare l'ordine delle colonne
function loadColumnOrder() {
    var savedColumns = localStorage.getItem("tabulatorColumns");
    return savedColumns ? JSON.parse(savedColumns) : null;
}


// Inizializzazione della tabella con i dati
var table = new Tabulator("#example-table$table_tmpid", {
 
    data: $jsonData,
    height: "500px",
    columns: loadColumnOrder() || columns,
    movableColumns: true,              // Abilita il movimento delle colonne
    columnMovableHandle: "handle",     // Mostra un'icona di trascinamento nell'header
    responsiveLayout: "scroll",
    layout: "fitDataFill",
     columnDefaults: {
        minWidth: 60
    },
    persistence: {
        sort: true,
        filter: true,
        group: true,
        columns: true
    },
    persistenceID: "prenotazioniTable",
    groupBy: [ "struttura"],
    groupHeader: function(value, count, data, group){
        let totale = data.reduce((acc, row) => acc + row.totale_completo, 0);
        return value + " <span style='color:red'>(" + count + " prenotazioni - Totale: € " + 
               totale.toLocaleString('it-IT', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 
               ")</span>";
    },
    pagination: "local",
    paginationSize: 25,
    paginationSizeSelector: [10, 25, 50, 100],
    groupToggleElement: "header",
    groupStartOpen: true,
    placeholder: "Nessun dato disponibile",
    langs: {
        "it-IT": {
            "pagination": {
                "page_size": "Righe per pagina",
                "first": "Prima",
                "first_title": "Prima Pagina",
                "last": "Ultima",
                "last_title": "Ultima Pagina",
                "prev": "Precedente",
                "prev_title": "Pagina Precedente",
                "next": "Successiva",
                "next_title": "Pagina Successiva",
            },
        },
    }
});

// Imposta la lingua italiana
//table.setLocale("it-IT");

// Salva l'ordine delle colonne quando vengono spostate
table.on("columnMoved", function(column) {
    var columnDefinitions = table.getColumnDefinitions();
    saveColumnOrder(columnDefinitions);
});

// Aggiungi pulsante per resettare l'ordine delle colonne
document.getElementById("reset-columns").addEventListener("click", function() {
    localStorage.removeItem("tabulatorColumns");
    table.setColumns(columns);
});
JS;

$this->registerJs($script, View::POS_END);
$this->registerCss("
    .tabulator {
        font-size: 14px;
        border: 1px solid #dee2e6;
    }
    .tabulator .tabulator-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    .tabulator-row.tabulator-group {
        background-color: #e9ecef;
        font-weight: bold;
    }
    .tabulator-row.tabulator-group span {
        color: #dc3545;
    }
    .tabulator-footer {
        background-color: #f8f9fa;
    }
 #example-table<?=$table_tmpid?> {
    max-width: 100%;
    overflow-x: auto;  /* Abilita la barra di scorrimento orizzontale */
    white-space: nowrap; /* Impedisce il ritorno a capo automatico */
}
 
");
// Aggiungi CSS personalizzato

?>


<!-- Aggiungi questo pulsante dove preferisci nella tua vista -->
<button id="reset-columns" class="btn btn-secondary mb-3">Ripristina ordine colonne</button>

