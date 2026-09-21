<?php

use yii\helpers\Html;
use yii\helpers\Json;

// Genera un ID univoco per la tabella
$tableId = 'roomlist_' . uniqid();

// Converte i dati in JSON
$filteredDataJson = Json::encode($roomlist2, JSON_INVALID_UTF8_SUBSTITUTE);
?>

<!-- CSS DataTables -->
<?php
$this->registerCssFile("https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css");
$this->registerCssFile("https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.min.css");

// JS DataTables
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
?>

<div class="divclass">
    <table id="<?= Html::encode($tableId) ?>" class="display nowrap" style="width:100%"></table>
</div>

<?php
$this->registerJs("
    $(document).ready(function () {
        let rawData = $filteredDataJson;

        $('#$tableId').DataTable({
            data: rawData,
            responsive: true,
            scrollX: true,
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copyHtml5', text: 'Copia' },
                { extend: 'excelHtml5', text: 'Excel' },
                { extend: 'csvHtml5', text: 'CSV' },
                { extend: 'pdfHtml5', text: 'PDF' },
                { extend: 'print', text: 'Stampa' }
            ],
            columns: [
                { title: 'Ospite', data: 'guest' },
                { title: 'Tipo Camera', data: 'cd_Ar' },
                { title: 'Note', data: 'note' }
            ]
        });
    });
");
?>