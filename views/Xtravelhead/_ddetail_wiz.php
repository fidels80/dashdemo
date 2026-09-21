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
echo 'data:';


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

$filteredDataJson = Json::encode($dwroomlist->allModels, JSON_INVALID_UTF8_SUBSTITUTE);
$tableId = 'wzdetailTable_' . uniqid();
//print_r($filteredDataJson); 
$jsUserLevel = Yii::$app->user->identity->level??0;
$roomDataJson = Json::encode($dwroomlist->allModels, JSON_INVALID_UTF8_SUBSTITUTE);

$rawData = $dwroomlist->allModels;

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
</style>
<table id="<?= Html::encode($tableId) ?>" class="display" style="width:100%">
    <thead>
        <tr>
            <th>Ospite</th>
            <th>Articolo</th>
            <th>Ruolo</th>
            <th>Party</th>
            <th>Commessa</th>
            <th>Note</th>
            <th>Evaso</th>
        </tr>
    </thead>
</table>
<script>
    $(document).ready(function() {

        $(document).ready(function() {
            let tableId = "#<?= Html::encode($tableId) ?>";
            let tableData = <?= $filteredDataJson ?>;

            // console.log("Dati ricevuti per DataTable:", tableData);

            $(tableId).DataTable({
                data: tableData,
                columns: [{
                        data: "guest",
                        title: "Ospite"
                    },
                    {
                        data: "cd_Ar",
                        title: "Articolo"
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
                        data: "commessa",
                        title: "Commessa"
                    },
                    {
                        data: "note",
                        title: "Note"
                    },
                    {
                        data: "evaso",
                        title: "Evaso"
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
</script>