<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;

/* ===========================
 * ASSET JS / CSS - CARICAMENTO CORRETTO
 * =========================== */

// 1. JSZip e PDFMake (Librerie di supporto per Excel e PDF)
// Devono essere caricate prima dei moduli di DataTables che le usano
$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js", [
    'position' => View::POS_HEAD
]);
$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js", [
    'position' => View::POS_HEAD
]);
$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js", [
    'position' => View::POS_HEAD
]);

// 2. DataTables Core
$this->registerJsFile("https://cdn.datatables.net/2.2.2/js/dataTables.min.js", [
    'depends' => [JqueryAsset::class]
]);

// 3. DataTables Buttons Core (Dipende da DataTables)
$this->registerJsFile("https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js", [
    'depends' => ["https://cdn.datatables.net/2.2.2/js/dataTables.min.js"]
]);

// 4. Moduli Bottoni (HTML5, Print, ecc.) - DIPENDONO DAL BUTTONS CORE
// Questo era il problema: senza 'depends', Yii2 rischia di caricarli prima del core
$this->registerJsFile("https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js", [
    'depends' => ["https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js"]
]);
$this->registerJsFile("https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js", [
    'depends' => ["https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js"]
]);

// Altri plugin
$this->registerJsFile("https://cdn.datatables.net/fixedheader/4.0.1/js/fixedHeader.dataTables.js", [
    'depends' => ["https://cdn.datatables.net/2.2.2/js/dataTables.min.js"]
]);
$this->registerJsFile("https://cdn.datatables.net/colreorder/2.0.4/js/dataTables.colReorder.min.js", [
    'depends' => ["https://cdn.datatables.net/2.2.2/js/dataTables.min.js"]
]);
$this->registerJsFile("https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js", [
    'depends' => ["https://cdn.datatables.net/2.2.2/js/dataTables.min.js"]
]);

/* CSS */
$this->registerCssFile("https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css");
$this->registerCssFile("https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.min.css");
$this->registerCssFile("https://cdn.datatables.net/fixedheader/4.0.1/css/fixedHeader.dataTables.min.css");
$this->registerCssFile("https://cdn.datatables.net/colreorder/2.0.4/css/colReorder.dataTables.min.css");
$this->registerCssFile("https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css");

/* CUSTOM CSS */
$this->registerCss("
    div.dt-container { width: 100% !important; max-width: 100% !important; }
    table.dataTable { width: 100% !important; }
    .filters select { width: 100%; }
    
    /* Forza la visibilità dei bottoni se qualche tema li nasconde */
    div.dt-buttons { 
        margin-bottom: 15px; 
        display: inline-flex !important;
        gap: 5px;
    }
    
    /* Stile per i totali */
    tfoot th {
        background-color: #f8f9fa;
        font-weight: bold;
        border-top: 2px solid #aaa !important;
    }
        .content{
       width: 90% !important; 
        
        }
");
?>

<div class="xtravelhead-index">
    <h3>Prenotazioni</h3>
    <div class="divclass">
        <table id="xtravel" class="display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Img</th>
                    <th>Cliente</th>
                    <th>Descrizione</th>
                    <th>Imponibile</th>
                    <th>Tax</th>
                    <th>IVA</th>
                    <th>Totale</th>
                    <th>Fat</th>
                    <th>Blc</th>
                </tr>
                <tr class="filters">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>
                        <select class="filter">
                            <option value=""></option>
                            <option value="SI">SI</option>
                            <option value="NO">NO</option>
                        </select>
                    </th>
                    <th>
                        <select class="filter">
                            <option value=""></option>
                            <option value="SI">SI</option>
                            <option value="NO">NO</option>
                        </select>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align:right">Totali Generali:</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<?php
$url = Url::to(['xtravelhead/datatable']);

$js = <<<JS
$(function () {
    let table = $('#xtravel').DataTable({
        ajax: '$url',
        processing: true,
        
        // Configurazione SCROLL
        scrollY: '500px',
        scrollX: true,
        scrollCollapse: true,
        
        // Importante per i filtri su seconda riga
        orderCellsTop: true, 
        paging: false,
        autoWidth: false,

        // === CONFIGURAZIONE BOTTONI (DataTables 2.x) ===
        layout: {
            topStart: {
                buttons: [
                    { extend: 'copyHtml5', text: 'Copia', className: 'btn btn-default' },
                    { extend: 'excelHtml5', text: 'Excel', title: 'Export Booking', className: 'btn btn-success' },
                    { extend: 'csvHtml5', text: 'CSV', className: 'btn btn-info' },
                    { 
                        extend: 'pdfHtml5', 
                        text: 'PDF', 
                        orientation: 'landscape', 
                        pageSize: 'A4',
                        className: 'btn btn-danger'
                    }
                ]
            }
        },

        columns: [
            { data: 'img', orderable: false, render: d => '<img src="' + d + '" style="max-width:80px">' },
            { data: 'cliente' },
            { data: 'descrizione', render: (d, t, r) => '<a href="' + r.link + '">' + d + '</a>' },
            { data: 'imponibile', className:'euro-column', render: $.fn.dataTable.render.number('.', ',', 2, '€ ') },
            { data: 'tax', className:'euro-column', render: $.fn.dataTable.render.number('.', ',', 2, '€ ') },
            { data: 'iva', className:'euro-column', render: $.fn.dataTable.render.number('.', ',', 2, '€ ') },
            { data: 'totale', className:'euro-column', render: $.fn.dataTable.render.number('.', ',', 2, '€ ') },
            {
                data: 'fatturato',
                render: function (d, type) {
                    if (type === 'filter' || type === 'sort') return d == 1 ? 'SI' : 'NO';
                    return d == 1 ? '<span class="badge bg-success">SI</span>' : 'NO';
                }
            },
            {
                data: 'bloccato',
                render: d => d == 1 ? '<span class="badge bg-danger">SI</span>' : 'NO'
            }
        ],

        language: {
            search: "Cerca:",
            zeroRecords: "Nessun risultato",
            info: "Mostra _TOTAL_ elementi"
        },

        // === CALCOLO TOTALI ===
        footerCallback: function (row, data, start, end, display) {
            let api = this.api();

            let intVal = function (i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };

            let formatMoney = $.fn.dataTable.render.number('.', ',', 2, '€ ').display;

            // Indici colonne: Imponibile(3), Tax(4), Iva(5), Totale(6)
            [3, 4, 5, 6].forEach(function(index) {
                let total = api
                    .column(index, { search: 'applied' }) 
                    .data()
                    .reduce((a, b) => intVal(a) + intVal(b), 0);
                
                // Aggiorniamo il footer corretto
                $(api.column(index).footer()).html(formatMoney(total));
            });
        },

        initComplete: function() {
            var api = this.api();
            $('.filters th').each(function(i) {
                var element = $('select, input', this);
                if (element.length > 0) {
                    element.on('change keyup', function() {
                        if (api.column(i).search() !== this.value) {
                            api.column(i).search(this.value).draw();
                        }
                    });
                }
            });
            setTimeout(() => table.columns.adjust(), 500);
        }
    });
});
JS;

$this->registerJs($js);
?>