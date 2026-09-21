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
/*$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
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

    .table-info,
    .table-info>td,
    .table-info>th {
        background-color: #fdfdfd;
    }
</style>


<?php

if (isset($struttura)) {
    echo $struttura;
    $struttura_desiderata = $struttura; // Valore da mantenere
    $filteredData = array_filter($xttmp, function ($row) use ($struttura_desiderata) {
        return isset($row['struttura']) && $row['struttura'] === $struttura_desiderata;
    });
    // Reindicizza l'array per evitare chiavi sparse
    $filteredData = array_values($filteredData);
    //print_r($filteredData);
}
if (isset($servizio)) {
    echo $servizio;
    $struttura_desiderata = $servizio;
    $filteredData = $xttmp;
    $db2 = Yii::$app->db2; // Connessione a DB2
    /*foreach ($filteredData as &$row) {
        $cd_ar = $row['cd_Ar']; // Prendi il valore di cd_ar corrente

        $result = (new \yii\db\Query())
            ->select(['ARClasse12.Classe'])
            ->from('adb_auxcoop.dbo.ar')
            ->leftJoin('adb_auxcoop.dbo.ARClasse12', 'ar.Cd_ARClasse1 = ARClasse12.Cd_ARClasse1 
        AND ar.Cd_ARClasse2 = ARClasse12.Cd_ARClasse2')
            ->where(['ar.cd_ar' => $cd_ar])
            ->one();
        $row['struttura'] = $result['Classe'] ?? null;
    }*/
    // Cache locale per evitare query duplicate
    $classeCache = [];

    foreach ($filteredData as &$row) {
        $cd_ar = $row['cd_Ar'];

        // Se non l'abbiamo ancora in cache, eseguiamo la query
        if (!isset($classeCache[$cd_ar])) {
            $result = (new \yii\db\Query())
                ->select(['ARClasse12.Classe'])
                ->from('adb_auxcoop.dbo.ar')
                ->leftJoin(
                    'adb_auxcoop.dbo.ARClasse12',
                    'ar.Cd_ARClasse1 = ARClasse12.Cd_ARClasse1 
                 AND ar.Cd_ARClasse2 = ARClasse12.Cd_ARClasse2'
                )
                ->where(['ar.cd_ar' => $cd_ar])
                ->one();

            $classeCache[$cd_ar] = $result['Classe'] ?? null;
        }

        // Usa il valore già memorizzato
        $row['struttura'] = $classeCache[$cd_ar];
    }

    unset($row); // Buona pratica per evitare riferimenti indesiderati
    $filteredData = array_filter($filteredData, function ($row) use ($servizio) {
        return isset($row['struttura']) && $row['struttura'] === $servizio;
    });
    $filteredData = array_values($filteredData);
}



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
        background-color: rgb(250, 250, 250) !important;
    }
</style>
<style>
    .modal-body {
        max-height: 75vh;
        overflow-y: auto;
        background-color: rgb(250, 250, 250) !important;
    }

    #<?= Html::encode($tableId) ?> {
        width: 100% !important;
        white-space: nowrap;
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

    .dataTable {
        background-color: rgb(255, 255, 255);

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

    html,
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
<table id="<?= Html::encode($tableId) ?>" class="display" style="width:100%">
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
                    //  colReorder: true,
                    stateSave: true,
                    fixedHeader: {
                        header: true,
                        headerOffset: $('.modal-header').outerHeight() || 0 // Account for modal header
                    },
                    // scrollX: true,
                    // autoWidth: true,

                    scrollY: "300px", // Altezza fissa per il contenuto
                    scrollCollapse: true,


                    destroy: true,
                    paging: false,
                    searching: true,
                    ordering: true,

                    //   responsive: true,
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
                            title: "Struttura"
                        },
                        {
                            data: "check_in",
                            title: "Check-in",
                            render: function(data, type, row) {
                                if (!data) return ""; // Se il valore è vuoto, non mostra nulla
                                let date = new Date(data);
                                return date.toLocaleDateString('it-IT'); // Converte in formato gg/mm/aaaa
                            }
                        },

                        {
                            data: "check_out",
                            title: "Check-Out",
                            render: function(data, type, row) {
                                if (!data) return "";
                                let date = new Date(data);
                                return date.toLocaleDateString('it-IT');
                            }
                        },
                        {
                            data: "qta",
                            title: "Notti"
                        },
                        {
                            data: "qta",
                            title: "Notti Tax"
                        },
                        {
                            data: "stato",
                            title: "Conferma"
                        },
                        {
                            data: "cd_Ar",
                            title: "Room"
                        },
                        {
                            data: "prezzo",
                            title: "Costo Notte"
                        },
                        {
                            data: "tax_unit",
                            title: "City Tax"
                        },
                        {
                            data: null,
                            title: "Tot Costo",
                            render: function(data, type, row) {
                                let totale = parseFloat(row.prezzo) * parseFloat(row.qta);
                                return totale ? totale.toFixed(2) : "0.00";
                            }
                        },
                        {
                            data: null,
                            title: "Tot City Tax",
                            render: function(data, type, row) {
                                let totale = parseFloat(row.tax_unit) * parseFloat(row.qta);
                                return totale ? totale.toFixed(2) : "0.00";
                            }
                        },
                        {
                            data: null,
                            title: "Totale",
                            render: function(data, type, row) {
                                let totale = (parseFloat(row.tax_unit) * parseFloat(row.qta)) + (parseFloat(row.prezzo) * parseFloat(row.qta));
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
                    columnDefs: [{
                            width: "10%",
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
                            width: "9%",
                            targets: 4
                        },
                        {
                            width: "5%",
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
                            width: "6%",
                            targets: 9
                        },
                        {
                            width: "5%",
                            targets: 10
                        },
                        {
                            width: "5%",
                            targets: 11
                        },
                        {
                            width: "5%",
                            targets: 12
                        },
                        {
                            width: "5%",
                            targets: 13
                        },
                        {
                            width: "5%",
                            targets: 14
                        },
                        {
                            width: "4%",
                            targets: 15
                        },
                        {
                            width: "4%",
                            targets: 16
                        },
                        {
                            width: "4%",
                            targets: 17
                        },
                        {
                            targets: [11, 12, 13, 17], // Indici delle colonne da formattare
                            render: function(data, type, row) {
                                if (type === 'display' || type === 'filter') {
                                    return parseFloat(data).toFixed(2); // Formatta a 2 decimali
                                }
                                return data;
                            }
                        },
                        {
                            target: [0, 19],
                            visible: false,
                            searchable: false
                        },
                        {
                            className: "wrap-text",
                            targets: [1, 2, 4, 10]
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
                        const colTotNotti = 7;
                        const colTotCosto = 13;
                        const colTotCityTax = 14;
                        const colTotale = 15;
                        const colTotFee = 17;
                        const coltot = 1

                        // Calcolo dei totali
                        const totalNotti = api.column(colTotNotti).data().reduce((a, b) =>
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
                        $(api.column(colTotCosto).footer()).html(totaleCosto.toFixed(2));
                        $(api.column(colTotCityTax).footer()).html(totaleCityTax.toFixed(2));
                        $(api.column(colTotale).footer()).html(totaleGenerale.toFixed(2));
                        $(api.column(colTotFee).footer()).html(totalfee.toFixed(2));
                        $(api.column(coltot).footer()).html('TOTALI');

                    },






                });



            }

        });
    });
</script>