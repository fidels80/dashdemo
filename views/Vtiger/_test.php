<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\grid\GridView;
use onmotion\apexcharts\ApexchartsWidget;
//yii::error($t);
yii::error($ticketstat_res);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $users array */
/* @var $dayFrom string */
/* @var $dayTo string */
/* @var $utente string */
?>

<!-- Div per la griglia Tabulator -->
<div id="example-table" style="height:500px;"></div>

<!-- Inserisci Tabulator e Select2 tramite CDN -->

<link href="https://unpkg.com/tabulator-tables/dist/css/tabulator.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>
<link href="/dist/css/tabulator_modern.min.css" rel="stylesheet">

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Inizializzo Tabulator');
        console.log(<?= json_encode($ticketstat_res) ?>);
        var clientiUnici = <?= json_encode(array_values(array_unique(array_column($ticketstat_res, 'soggetto')))) ?>;
        // Configurazione di Tabulator
        var table = new Tabulator("#example-table", {
            data: <?= json_encode($ticketstat_res) ?>, // Dati per la tabella
            layout: "fitColumns", // Adatta le colonne alla larghezza della tabella
            movableColumns: true, // Colonne trascinabili
            groupBy: ["settimana_mese", "cf_909",'soggetto','cf_905','cf_907'],
            groupHeader: function(value, count, data, group){
                return value + " - Totale: " + count + " elementi";
            },
            groupClosedShowCalcs: true, // Mostra i calcoli anche quando i gruppi sono chiusi
            columns: [
                {
                    title: "Settimana del mese",
                    field: "settimana_mese",
                    sorter: "number",
                    headerFilter: "input" // Filtro input
                },
                {
                    title: "Tipo Evento",
                    field: "cf_909",
                    sorter: "string",
                    headerFilter: "input" // Filtro input
                },
                 { 
                    title: "Cliente", 
                    field: "soggetto", 
                    sorter: "string",
                    headerFilter: "list", // Usa una lista a tendina come filtro
                    headerFilterParams: {
                        values: clientiUnici, // Passa la lista dinamica di clienti
                        clearable: true // Opzione per permettere di resettare il filtro
                    }
                },
                {
                    title: "Tipologia Evento",
                    field: "cf_879",
                    sorter: "string",
                    headerFilter: "input" // Filtro input
                },
                {
                    title: "Prodotto Principale",
                    field: "productname",
                    sorter: "string",
                    headerFilter: "input" // Filtro input
                },
                {
                    title: "Prodotto Correlato",
                    field: "cf_887",
                    sorter: "string",
                    headerFilter: "input" // Filtro input
                },
                {
                    title: "A Pagamento",
                    field: "cf_907",
                    sorter: "number",
                    headerFilter: "input" // Filtro input
                },
                {
                    title: "Prepagato",
                    field: "cf_905",
                    sorter: "number",
                    headerFilter: "input" // Filtro input
                },
                {
                    title: "Interventi",
                    field: "soggetto",
                    bottomCalc: "count" // Conta gli interventi
                },
                {
                    title: "Ore",
                    field: "oredelta",
                    bottomCalc: "sum" // Somma delle ore
                },
            ],
            // Callback per eseguire una volta che i filtri sono stati renderizzati
            renderComplete: function(){
                // Inizializza Select2 per i filtri di select
                var selectFilters = document.querySelectorAll('.tabulator-header-filter select');
                selectFilters.forEach(function(select) {
                    $(select).select2({
                        width: 'resolve' // Gestione del layout con Select2
                    });
                });
            }
        });
        
        // Esporta in CSV
        document.getElementById('download-csv').addEventListener('click', function() {
            table.download("csv", "dati.csv");
        });
        // Esporta in Excel (XLSX)
        document.getElementById('download-xlsx').addEventListener('click', function() {
            table.download("xlsx", "dati.xlsx", {
                sheetName: "Dati"
            });
        });
    });
</script>

<!-- Bottoni per esportare -->
<button id="download-csv">Esporta in CSV</button>
<button id="download-xlsx">Esporta in Excel</button>

<!-- Tabulator div -->
<div id="example-table" style="height: 500px;"></div>

<!-- Inclusione della libreria XLSX per esportazione Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.1/xlsx.core.min.js"></script>

