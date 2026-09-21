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

$filteredDataJson = Json::encode($dwroomlist->allModels, JSON_INVALID_UTF8_SUBSTITUTE);
$tableId = 'wzdetailTable_righe' . uniqid();
//print_r($filteredDataJson); 
$jsUserLevel = Yii::$app->user->identity->level ?? 0 ;
$roomDataJson = Json::encode($dwroomlist->allModels, JSON_INVALID_UTF8_SUBSTITUTE);

$rawData = $righe;
//$dwroomlist->allModels;

$flatData = [];
foreach ($rawData as $chunk) {
    if (is_array($chunk) && array_keys($chunk) === range(0, count($chunk) - 1)) {
        foreach ($chunk as $item) {
            $flatData[] = $item;
        }
    } else {
        $flatData[] = $chunk;
    }
}

$filteredDataJson = Json::encode($flatData, JSON_INVALID_UTF8_SUBSTITUTE);

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

</table>
<script>
    $(document).ready(function() {

        $(document).ready(function() {
            let tableId = "#<?= Html::encode($tableId) ?>";
            let tableData = <?= $filteredDataJson ?>;
            const currentUserLevel = <?= $jsUserLevel ?>;
            // console.log("Dati ricevuti per DataTable:", tableData);

            $(tableId).DataTable({
                data: tableData,
                columns: [

                    {
                        data: 'tr_id',
                        title: 'Riga',
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
                        data: "guest",
                        title: "Ospite",
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
                    }, {
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
                    }, {
                        data: "tr_id",
                        title: "Azioni",
                        orderable: false,
                        render: function(data, type, row) {
                            return `<button type="button" class="btn-delete btn btn-danger btn-sm" 
        data-id="${row.tr_id}">Elimina</button>`;
                        }
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
                paging: false,
                searching: true,
                ordering: true
            });
        });

    });


    $(document).off('click', '.btn-delete');

    // Aggiungi il nuovo gestore di eventi
    $(document).on('click', '.btn-delete', function(e) {
        // Ferma qualsiasi propagazione dell'evento
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();

        // Ottieni l'ID del record
        var trId = $(this).data('id');

        // Prima conferma
        var conferma1 = confirm("Sei sicuro di voler eliminare questo record?");
        if (conferma1 === false) {
            console.log("Prima conferma annullata");
            return false;
        }

        // Seconda conferma
        var conferma2 = confirm("Sei DAVVERO sicuro?");
        if (conferma2 === false) {
            console.log("Seconda conferma annullata");
            return false;
        }

        // Terza conferma
        var conferma3 = confirm("Eliminazione IRREVERSIBILE. Procedere?");
        if (conferma3 === false) {
            console.log("Terza conferma annullata");
            return false;
        }

        // Se arriviamo qui, tutte le conferme sono state accettate
        console.log("Tutte le conferme accettate, procedo con l'eliminazione");

        // Esegui la richiesta AJAX per l'eliminazione
        $.ajax({
            url: 'index.php?r=xtravelhead/eliminarecord',
            type: 'post',
            data: {
                id: trId
            },
            success: function(response) {
                console.log(response);
                alert("Record eliminato con successo");
                location.reload();
            },

            error: function(xhr, status, error) {
                alert("Errore durante l'eliminazione: " + error);
            }
        });




        // Assicurati che nulla venga eseguito dopo questa funzione
        return false;
    });
</script>