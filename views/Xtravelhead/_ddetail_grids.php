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

$table_tmpid = uniqid();
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


<style>
    .euro-column {
        min-width: 190px;
        /* Puoi aumentare il valore se serve più spazio */
        text-align: right;
        /* Allinea a destra per una migliore leggibilità */
    }

    .euro-total {
        min-width: 190px;
        /* Puoi aumentare il valore se serve più spazio */
        text-align: right;
        /* Allinea a destra per una migliore leggibilità */
        white-space: nowrap;
    }
</style>


<?php

$filteredData = $xttmp; // Parte dall'intero array

// Se è settato $struttura, filtriamo subito
if (isset($struttura)) {
    $struttura_desiderata = $struttura;
    $filteredData = array_filter($filteredData, function ($row) use ($struttura_desiderata) {
        return isset($row['struttura']) && $row['struttura'] === $struttura_desiderata;
    });
    $filteredData = array_values($filteredData); // Reindicizza l'array
}

// Se è settato $servizio, aggiorniamo 'struttura' con SottoClasse e filtriamo
if (isset($servizio)) {
    // Precarica tutte le SottoClasse per tutti i cd_Ar dell'array corrente
    $cdArList = array_unique(array_column($filteredData, 'cd_Ar'));

    if (!empty($cdArList)) {
        $classeData = (new \yii\db\Query())
            ->select(['ar.cd_ar', 'ARClasse123.SottoClasse'])
            ->from('adb_auxcoop.dbo.ar')
            ->leftJoin('adb_auxcoop.dbo.ARClasse123', 'ar.Cd_ARClasse123 = ARClasse123.Cd_ARClasse123')
            ->where(['ar.cd_ar' => $cdArList])
            ->all();

        // Crea una cache per accesso rapido
        $classeCache = [];
        foreach ($classeData as $item) {
            $classeCache[$item['cd_ar']] = $item['SottoClasse'];
        }

        // Aggiorna 'struttura' in ogni riga
        foreach ($filteredData as &$row) {
            $row['struttura'] = $classeCache[$row['cd_Ar']] ?? null;
        }
        unset($row); // Evita riferimenti indesiderati

        // Filtra per servizio
        $filteredData = array_filter($filteredData, function ($row) use ($servizio) {
            return isset($row['struttura']) && $row['struttura'] === $servizio;
        });
        $filteredData = array_values($filteredData); // Reindicizza
    }
}

// Ora $filteredData contiene l'array filtrato pronto da usare
?>


<?php


$this->registerJsFile("https://code.jquery.com/jquery-3.6.0.min.js", [
    'position' => \yii\web\View::POS_HEAD
]);

$this->registerJsFile("https://cdn.datatables.net/2.2.2/js/dataTables.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);
$this->registerJsFile("https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdn.datatables.net/fixedheader/4.0.1/js/fixedHeader.dataTables.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdn.datatables.net/colreorder/2.0.4/js/dataTables.colReorder.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);



$this->registerCssFile("https://cdn.datatables.net/fixedheader/4.0.1/css/fixedHeader.dataTables.min.css");
$this->registerCssFile("https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css");
$this->registerCssFile("https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.min.css");
$this->registerCssFile("https://cdn.datatables.net/colreorder/2.0.4/css/colReorder.dataTables.min.css");

$filteredDataJson = Json::encode($filteredData, JSON_INVALID_UTF8_SUBSTITUTE);
$tableId = 'detailTable_' . uniqid();
//print_r($filteredDataJson); 
?>

<style>
    #<?= Html::encode($tableId) ?> {
        width: 100% !important;
        white-space: nowrap;
    }

    .modal-body {
        max-height: 75vh;
        /* Limita l'altezza al 75% dell'altezza della viewport */
        overflow-y: auto;
        background-color: rgb(255, 255, 255);
        /* Abilita lo scorrimento verticale */
    }

    /* Ensure header stays above other elements */
    .fixedHeader-floating {
        z-index: 1060 !important;
        /* Higher than modal's z-index */
    }

    .wrap-text {
        white-space: normal !important;
        word-wrap: break-word !important;
        max-width: 200px;
        /* Imposta un limite per evitare che la colonna si allarghi troppo */
    }

    .dataTable thead th {
        background-color: #f1eef6;
        color: #002c48;
        font-size: 13px;
    }

    .dataTable tr {
        background-color: rgb(255, 255, 255);
        color: #002c48;
        font-size: 13px;
    }

    body {
        background-color: rgb(250, 250, 250) !important;
    }

    .table-info,
    .table-info>td,
    .table-info>th {
        background-color: #fdfdfd;
    }

    div.dt-container {
        width: 100%;
        margin: 0;
    }
</style>

<table id="<?= Html::encode($tableId) ?>" class="display" style="width:100%;background-color: #f1eef6;">
    <tfoot>
        <tr>
            <th>Totali</th> <!-- Id -->
            <th></th> <!-- Ospite -->
            <th></th> <!-- Ruolo -->
            <th></th> <!-- Party -->
            <th></th> <!-- Struttura -->
            <th></th> <!-- Check-in -->
            <th></th> <!-- Check-out -->
            <th></th> <!-- Notti (totale qui) -->
            <th></th> <!-- Notti Tax -->
            <th></th> <!-- Conferma -->
            <th></th> <!-- Room -->
            <th></th> <!-- Costo Notte -->
            <th></th> <!-- City Tax -->
            <th></th> <!-- Tot Costo (totale qui) -->
            <th></th> <!-- Tot City Tax (totale qui) -->
            <th></th> <!-- Totale (totale qui) -->
            <th></th> <!-- Fee % -->
            <th></th> <!-- Fee -->
            <th></th> <!-- IVA -->
            <th></th> <!-- Tappa -->
        </tr>
    </tfoot>

</table>

<?php
$userId = Yii::$app->user->id ?? 0; // Id dell'utente loggato, default 0 se non loggato
$jsUserLevel = (int)(Yii::$app->user->identity->level ?? 0); // Level, default 0 se non impostato
?>


<script>
    $(document).ready(function() {

        const currentUserLevel = <?= $jsUserLevel ?>;
        let tableId = "#<?= Html::encode($tableId) ?>";

        // Assicura che la DataTable venga inizializzata solo dopo che la modale è completamente aperta
        $('.modal').on('shown.bs.modal', function() {
            if (!$.fn.DataTable.isDataTable(tableId)) {
                let tableData = <?= $filteredDataJson ?>;
                //console.log("Dati ricevuti per DataTable:", tableData);

                let table = $(tableId).DataTable({
                    data: tableData,
                    colReorder: true,
                    stateSave: true,
                    fixedHeader: {
                        header: true,
                        headerOffset: $('.modal-header').outerHeight() || 0 // Account for modal header
                    },
                    // scrollX: true,
                    // autoWidth: true,

                    scrollY: "300px", // Altezza fissa per il contenuto
                    scrollCollapse: true,
                    columns: [{
                            data: "tr_id",
                            title: "Id"
                        },
                        {
                            data: "guest",
                            title: "Ospite",
                            render: function(data, type, row) {
                                if (type === 'display') {
                                    if (typeof currentUserLevel !== 'undefined' && currentUserLevel === 80) {
                                        let url = 'index.php?r=xtravelrow%2Fupdate&id=' + encodeURIComponent(row.tr_id);
                                        return `<a href="${url}" target="_blank">${data}</a>`;
                                    } else {
                                        return data; // Mostra solo il testo, niente link
                                    }
                                }
                                return data;
                            }

                        },
                        {
                            data: "ruolo",
                            title: "Ruolo"
                        },
                        {
                            data: "party",
                            title: "Party"
                        },
                        {
                            data: "struttura",
                            title: "Servizio"
                        },
                        {
                            data: "desfor",
                            title: "Forn."
                        },
                        {
                            data: "check_in",
                            title: "Partenza",
                            render: function(data, type, row) {
                                if (!data) return "";
                                let date = new Date(data);
                                return date.toLocaleDateString('it-IT');
                            },
                        },
                        {
                            data: "citta_da",
                            title: "Da"
                        },
                        {
                            data: "citta_a",
                            title: "Per"
                        },
                        {
                            data: "qta",
                            title: "Qta"
                        },
                        {
                            data: "pnr",
                            title: "PNR"
                        },
                        {
                            data: "nr_biglietto",
                            title: "Biglietto"
                        },

                        {
                            data: "data_pg",
                            title: "Data Pag",
                            render: function(data, type, row) {
                                if (!data) return "";
                                let date = new Date(data);
                                return date.toLocaleDateString('it-IT');
                            }
                        },
                        {
                            data: "cd_pg",
                            title: "Strum. Pag"
                        },
                        {
                            data: "prezzo",
                            title: "Prezzo"
                        },
                        {
                            data: null,
                            title: "Tot Servizi",
                            render: function(data, type, row) {
                                let totale = parseFloat(row.prezzo) * parseFloat(row.qta);
                                return totale ? totale.toFixed(2) : "0.00";
                            }
                        },
                        {
                            data: "fee_perc",
                            title: "Fee %",
                            defaultContent: "N/D"
                        },
                        {
                            data: "fee",
                            title: "Fee"
                        },
                        {
                            data: "codiva",
                            title: "IVA",
                            defaultContent: "N/A"
                        },
                        {
                            data: 'citta',
                            title: "Tappa"
                        }
                    ],
                    paging: false,
                    searching: true,
                    ordering: true,

                    responsive: true,
                    dom: 'Bfrtip', // Attiva i bottoni sopra la tabella
                    buttons: [{
                            extend: 'copy',
                            text: 'Copia'
                        },
                        {
                            extend: 'csv',
                            text: 'CSV'
                        },
                        {
                            extend: 'excel',
                            text: 'Excel'
                        }
                    ],
                    columnDefs: [
                        // { width: "0px", targets: 0 },  
                        {
                            width: "11%",
                            targets: 1
                        },
                        {
                            width: "5%",
                            targets: 2
                        },
                        {
                            width: "5%",
                            targets: 3
                        },
                        {
                            width: "11%",
                            targets: 4
                        },
                        {
                            width: "11%",
                            targets: 5
                        },
                        {
                            width: "6%",
                            targets: 6
                        },
                        {
                            width: "5%",
                            targets: 7
                        },
                        {
                            width: "5%",
                            targets: 8
                        },
                        {
                            width: "4%",
                            targets: 9
                        },
                        {
                            width: "5%",
                            targets: 10
                        },
                        {
                            width: "6%",
                            targets: 11
                        },
                        {
                            width: "6%",
                            targets: 12
                        },
                        {
                            width: "6%",
                            targets: 13
                        },
                        {
                            width: "5%",
                            targets: 14
                        },
                        {
                            width: "5%",
                            targets: 15
                        },
                        {
                            width: "5%",
                            targets: 16
                        },
                        {
                            width: "5%",
                            targets: 17
                        },
                        {
                            className: "wrap-text",
                            targets: [4, 1, 5]
                        },

                        {
                            targets: [14, 15, 17], // Indici delle colonne da formattare
                            render: function(data, type, row) {
                                if (type === 'display' || type === 'filter') {
                                    return parseFloat(data).toFixed(2); // Formatta a 2 decimali
                                }
                                return data;
                            }
                        },

                        {
                            target: [0, 2, 3, 19],
                            visible: false,
                            searchable: false
                        },
                    ],
                    language: {
                        search: "Cerca:",
                        lengthMenu: "Mostra _MENU_ elementi",
                        info: "Mostra da _START_ a _END_ di _TOTAL_ elementi",
                        infoEmpty: "Nessun dato disponibile",
                        infoFiltered: "(filtrato da _MAX_ elementi totali)",
                        zeroRecords: "Nessun risultato trovato",
                        paginate: {
                            first: "Primo",
                            last: "Ultimo",
                            next: "Successivo",
                            previous: "Precedente"
                        }
                    },
                    // scrollX: true,
                    // autoWidth: true,
                    destroy: true,
                    footerCallback: function(row, data, start, end, display) {
                        const api = this.api();

                        // Funzione di utilità per sommare una colonna
                        const intVal = function(i) {
                            return typeof i === 'string' ?
                                parseFloat(i.replace(/[\€,]/g, '')) || 0 :
                                typeof i === 'number' ?
                                i : 0;
                        };

                        // Indici delle colonne da sommare (modifica se cambiano)
                        const colTotNotti = 9;
                        //const colTotCosto = 13;
                        const colprezzo = 14;
                        const colTotale = 15;
                        const colTotFee = 17;
                        const coltot = 1

                        // Calcolo dei totali
                        const totalNotti = api.column(colTotNotti).data().reduce((a, b) =>
                            intVal(a) + intVal(b), 0);
                        const totalprezzo = api.column(colprezzo).data().reduce((a, b) =>
                            intVal(a) + intVal(b), 0);

                        const totalfee = api.column(colTotFee).data().reduce((a, b) =>
                            intVal(a) + intVal(b), 0);
                        const calcolaTotale = (callback) => {
                            return data.reduce((sum, row) => {
                                return sum + callback(row);
                            }, 0);
                        };

                        // Calcola totali con logica identica ai render
                        const totaleCosto = calcolaTotale(row => parseFloat(row.qta) * parseFloat(row.prezzo));
                        const totaleCityTax = calcolaTotale(row => parseFloat(row.qta) * parseFloat(row.tax_unit));
                        const totaleGenerale = totaleCosto + totaleCityTax;
                        // Scrittura nel footer
                        $(api.column(colTotNotti).footer()).html(totalNotti);
                        //  $(api.column(colTotCosto).footer()).html(totaleCosto.toFixed(2));
                        $(api.column(colprezzo).footer()).html(totalprezzo.toFixed(2));
                        $(api.column(colTotale).footer()).html(totaleGenerale.toFixed(2));
                        $(api.column(colTotFee).footer()).html(totalfee.toFixed(2));
                        $(api.column(coltot).footer()).html('TOTALI');

                    },
                });


            }
        });
    });
</script>