<?php

namespace app\components;

use Yii;
use app\assets\DataTablesAsset;

/**
 * Helper per inizializzare DataTables in modo uniforme:
 * - ricerca, paginazione, ordinamento
 * - esportazione (Copia, Excel, PDF, CSV, Stampa)
 * - layout responsive (nessuno scroll orizzontale, ok su mobile/tablet)
 *
 * Uso nella vista:
 *   \app\components\DataTables::render('mia-tabella');
 */
class DataTables
{
    public static function render($tableId, $orderCol = 0, $orderDir = 'asc', $pageLength = 25, $options = [])
    {
        DataTablesAsset::register(Yii::$app->view);

        $id = ltrim($tableId, '#');
        $pageLength = (int) $pageLength;
        $orderCol = (int) $orderCol;
        $orderDir = ($orderDir === 'desc') ? 'desc' : 'asc';
        $extra = $options['extraJs'] ?? '';

        // Evita che i pulsanti/tabella escano dal contenitore e sistema il responsive
        Yii::$app->view->registerCss("
            #{$id}_wrapper, .dataTables_wrapper { width: 100% !important; }
            .dataTables_wrapper .dt-buttons { display: flex; flex-wrap: wrap; gap: 4px; justify-content: flex-end; }
            .dataTables_wrapper .dt-buttons .btn { margin: 0; }
            .dataTables_wrapper .dataTables_filter { text-align: left; }
            .dataTables_wrapper .dataTables_filter input { max-width: 220px; display: inline-block; }
            @media (max-width: 767px) {
                .dataTables_wrapper .dt-buttons { justify-content: flex-start; }
                .dataTables_wrapper .dataTables_filter { text-align: left; width: 100%; }
                .dataTables_wrapper .dataTables_filter input { width: 100%; max-width: none; }
            }
            table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before { background-color: #0d6efd; }
        ");

        $js = <<<JS
$(document).ready(function () {
    var tbl = $('#{$id}');
    if ($.fn.DataTable.isDataTable(tbl)) {
        tbl.DataTable().destroy();
    }
    tbl.DataTable({
        dom: '<"row align-items-center mb-2"<"col-md-6"f><"col-md-6"B>>rt<"row align-items-center mt-2"<"col-md-6"i><"col-md-6"p>>',
        language: {
            search: 'Cerca:',
            lengthMenu: 'Mostra _MENU_ record per pagina',
            info: 'Visualizzati da _START_ a _END_ di _TOTAL_ record',
            infoEmpty: 'Nessun record',
            zeroRecords: 'Nessun risultato trovato',
            paginate: { first: 'Inizio', last: 'Fine', next: 'Successivo', previous: 'Precedente' }
        },
        buttons: [
            { extend: 'copy', className: 'btn btn-secondary btn-sm', text: '<i class="fa-solid fa-copy"></i> Copia',
              exportOptions: { columns: ':not(.no-export)' } },
            { extend: 'excel', className: 'btn btn-success btn-sm', text: '<i class="fa-solid fa-file-excel"></i> Excel',
              exportOptions: { columns: ':not(.no-export)' } },
            { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm', text: '<i class="fa-solid fa-file-pdf"></i> PDF',
              orientation: 'landscape', pageSize: 'A4',
              exportOptions: { columns: ':not(.no-export)' },
              customize: function (doc) { doc.defaultStyle.fontSize = 8; doc.styles.tableHeader.fontSize = 9; } },
            { extend: 'csv', className: 'btn btn-info btn-sm', text: '<i class="fa-solid fa-file-csv"></i> CSV',
              exportOptions: { columns: ':not(.no-export)' } },
            { extend: 'print', className: 'btn btn-primary btn-sm', text: '<i class="fa-solid fa-print"></i> Stampa',
              exportOptions: { columns: ':not(.no-export)' } }
        ],
        pageLength: {$pageLength},
        order: [[{$orderCol}, '{$orderDir}']],
        responsive: true,
        scrollX: false,
        autoWidth: false,
        columnDefs: [{ targets: 'no-export', orderable: false, searchable: false }]
        {$extra}
    });
});
JS;

        Yii::$app->view->registerJs($js);
    }
}
