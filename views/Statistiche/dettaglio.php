<style>
    .xdivclass_venue {
        width: 185% !important;
        /* Larghezza piena */
        margin: 0 auto !important;
        overflow-x: hidden !important;
        padding: 10px !important;
    }

    .xdivclass_venue table {
        width: 100% !important;
        /* Piena larghezza */
        min-width: unset !important;
        /* Nessun limite minimo */
        table-layout: auto;
        /* Le colonne si adattano */
        position: relative;
    }

    .dataTables_wrapper {
        width: 100% !important;
        overflow-x: auto !important;
    }

    /* Colonna azioni sticky */
    .fixed-actions-column {
        position: sticky !important;
        right: 0 !important;
        background-color: #fff !important;
        border-left: 2px solid #dee2e6 !important;
        z-index: 30 !important;
    }

    .dataTables_scrollHead th.fixed-actions-column {
        position: sticky !important;
        right: 0 !important;
        background-color: #f8f9fa !important;
        border-left: 2px solid #dee2e6 !important;
        z-index: 31 !important;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1) !important;
        min-width: 120px !important;
    }

    /* Bottoni nella colonna fissa */
    .fixed-actions-column .btn {
        margin: 1px 2px !important;
        font-size: 11px !important;
        padding: 3px 6px !important;
    }

    /* Celle senza wrapping */
    .dataTables_wrapper table td,
    .dataTables_wrapper table th {
        white-space: nowrap !important;
        min-width: 80px !important;
    }

    #calendar {
        width: 100%;
        min-width: 100%;
    }

    .fc {
        font-size: 0.9rem;
        /* testo più leggibile */
    }

    .dt-buttons {
        margin-bottom: 0.75rem;
    }

    .content {
        width: 100% !important;
    }

    #tab-ftv_wrapper {
        width: auto !important;
    }

    .dataTables_scrollHeadInner,
    .dataTables_scrollHeadInner table {
        width: 100% !important;
    }

    .dataTables_scrollBody table {
        width: 100% !important;
    }

    .azioni-col {
        text-align: center !important;
        white-space: nowrap !important;
    }

    .dataTables_wrapper td,
    .dataTables_wrapper th {
        vertical-align: middle !important;
    }
</style>


<style>
    #rotate-notice {
        display: none;
        position: fixed;
        inset: 0;
        background: #0009;
        color: white;
        font-size: 1.5em;
        text-align: center;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    @media screen and (orientation: portrait) {
        #rotate-notice {
            display: flex;
        }
    }
</style>

<div id="rotate-notice">
    Ruota il dispositivo in orizzontale per continuare 🔄
</div>

<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

$downloadBaseUrl = Url::to(['statistiche/download'], true);

$this->registerJs("
    const downloadBaseUrl = '{$downloadBaseUrl}';
");

$this->title = "Dettaglio mese $mese";
$tableId = 'tab-ftv';
$filteredDataJson = json_encode($dati['ftv']);
$filteredDataJsonprv = json_encode($dati['prv']);
$Clidata = (new \yii\db\Query())
    ->select(['cd_Cf as id', 'descrizione'])
    ->from('cf')
    ->where(['TipoCf' => 'C'])
    ->createCommand(Yii::$app->db5)
    ->queryAll();
$ClidataOptions = ArrayHelper::map($Clidata, 'id', 'descrizione');
$ClidataOptionsJson = Json::encode($ClidataOptions);



$cd_agente = Yii::$app->user->identity->cd_agente ?? null;

$anno = substr($mese, 0, 4);   // 2025
$meseNum = substr($mese, 5, 2); // 09
$pattern = "_{$anno}{$meseNum}_";

$query = (new \yii\db\Query())
    ->select([
        'id',
        'cd_agente',
        'nome_file',
        'data_scadenza',
        'estenzione',
        'descrizione',
        'nota',
        'cartella',
        'cartella_padre'

    ])
    ->from('agenti_files')
    ->where(['cd_agente' => $cd_agente])
    ->andWhere([
        'or',
        ['like', 'nome_file', $pattern],
        [
            'and',
            ["MONTH(data_scadenza)" => $meseNum],
            ["YEAR(data_scadenza)" => $anno]
        ]
    ]);

$sql = $query->createCommand()->getRawSql();

Yii::warning($sql, 'sql-dettaglio-mese');

$files = $query->all();
$dati['files'] = $files;

$filesJson = json_encode($dati['files']);
yii::warning($filesJson, 'dettaglio-mese');

?>
<br>
<nav>

    <?php
    $url = Url::to(['statistiche/index']);

    echo Html::button(
        '<i class="fa fa-arrow-left"></i> Torna all Calendario',
        [
            'class' => 'button-base button-card-action button-lift',
            'onclick' => 'window.location.href = "' . $url . '"',
            'encode' => false // importante per non scappare l'HTML dell'icona
        ]
    );

    ?>
    <?php
    $url = Url::to(['site/contatti', 'periodo' => $mese]);

    echo Html::button(
        '<i class="fa fa-envelope"></i> Invia Comunicazione',
        [
            'class' => 'button-base-support button-lift',
            'onclick' => 'window.location.href = "' . $url . '"',
            'encode' => false // importante per non scappare l'HTML dell'icona
        ]
    );

    ?>
</nav>
<div class="container-fluid mt-4">
    <h3>Dettaglio mese
        <?php
        $fmt = new \IntlDateFormatter(
            'it_IT',
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::NONE,
            'Europe/Rome',
            null,
            'MMMM yyyy'
        );
        $data = $fmt->format(strtotime($mese . '-01'));

        // Prima lettera maiuscola (gestisce anche parole accentate)
        echo mb_convert_case($data, MB_CASE_TITLE, "UTF-8");
        ?>
    </h3>
    <!-- CALENDARIO -->
    <div class="row mt-4">
        <div class="col-12">
            <div id="calendar" class="border rounded p-3 bg-white shadow-sm"
                style="min-height: 600px; width: 100% !important;"></div>
        </div>
    </div>

    <!-- DATATABLE -->
    <?php if (!empty($dati['ftv'])) : ?>
    <div class="row mt-4">
        <h4>Fatturato e Provvigioni</h4>
        <div class="col-12">
            <div class="card shadow-sm w-100">
                <div class="card-body p-0">
                    <table id="<?= Html::encode($tableId) ?>"
                        class="table table-striped table-sm"
                        style="width:100%"></table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- FILES AGENTE -->
    <?php if(!empty($dati['files'])): ?>
    <div class="row mt-4">
        <h4>File Agente</h4>
        <div class="col-12">
            <div class="card shadow-sm w-100">
                <div class="card-body p-0">
                    <table id="tab-files"
                        class="table table-striped table-sm"
                        style="width:100%"></table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>


<!-- Modal Dettaglio Giorno -->
<div class=" modal fade" id="giornoModal"
    tabindex="-1" aria-labelledby="giornoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="giornoModalLabel">
                    Dettaglio giornata</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-striped align-middle" id="tabellaDettaglio">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Venue</th>
                            <th>Arti.</th>
                            <th>Descrizione</th>
                            <th>Citta</th>
                            <th>Data Prestazione</th>
                            <th>Ore</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!--           dotes.Cd_Do,Cd_Agente_1,dotes.DataDoc,DOTes.NumeroDoc,
DORig.Cd_AR,DORig.Descrizione,dorig.x_ore,dorig.x_cd_cf,dorig.x_citta,
dorig.x_data -->
<?php

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

// Include Select2 for better dropdowns
$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.full.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);
$this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css");

$this->registerJsFile("https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerCssFile("https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css");



?>
<?php
$this->registerCss("
    /* Fix per scroll DataTable orizzontale */
    .dataTables_wrapper {
        overflow-x: auto !important;
    }

    .content,
    .xdivclass_venue {
        overflow-x: visible !important; /* permette lo scroll orizzontale */
    }

    /* Mantieni la larghezza totale solo dove serve */
    .xdivclass_venue {
        width: 185% !important;
        margin: 0 auto !important;
        padding: 10px !important;
    }

    /* Ottimizza le celle per lo scroll */
    table.dataTable td,
    table.dataTable th {
        white-space: nowrap !important;
    }
");
?>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const url = "<?= Url::to([
                            'statistiche/dati-dettaglioaj',
                            'mese' => $mese
                        ]) ?>";

        fetch(url)
            .then(r => r.json())
            .then(data => {
                const prvData = <?= $filteredDataJsonprv ?>;
                const ftvData = data.ftv || [];
                console.log(prvData);
                console.log(data.ftv);
                initCalendar(prvData);
                // initTabulator(data.ftv);
            });

        const ClidataOptions = <?= $ClidataOptionsJson ?>;

        function formatDateDDMMYYYY(dateString) {
            const d = new Date(dateString);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}/${month}/${year}`;
        }



        // === CALENDARIO PRV ===
        function initCalendar(prvData) {
            if (!prvData || prvData.length === 0) return; // niente dati
            const grouped = {};
            console.log(prvData);
            prvData.forEach(r => {
                const day = r.x_data.split('T')[0];
                if (!grouped[day]) grouped[day] = [];
                grouped[day].push(r);
            });

            const events = Object.keys(grouped).map(day => {
                const totalHours = grouped[day].reduce((sum, r) =>
                    sum + (parseFloat(r.x_ore) || 0), 0);
                return {
                    title: totalHours.toFixed(2) + " ore",
                    start: day,
                    allDay: true,
                    extendedProps: {
                        dettagli: grouped[day]
                    }
                };
            });
            /*                                   Tipo doc
                            DataDoc
                            NumeroDoc
                            Cd_AR
                            Descrizione
                            x_citta
                            x_data
                            x_ore
                                                        <th>Tipo doc</th>
                            <th>DataDoc</th>
                            <th>NumeroDoc</th>
                            <th>Arti.</th>
                            <th>Descrizione</th>
                            <th>Citta</th>
                            <th>Data Prestazione</th>
                            <th>Ore</th>
                            
                            
                            
                            */
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: '<?= $mese ?>-01', // mostra il mese passato
                events: events,
                eventColor: '#0d6efd',
                height: 650,
                locale: 'it',
                headerToolbar: false,
                eventClick: function(info) {
                    const dettagli = info.event.extendedProps.dettagli;
                    const tbody = document.querySelector('#tabellaDettaglio tbody');
                    tbody.innerHTML = '';
                    dettagli.forEach(r => {
                        const tr = document.createElement('tr');
                        console.log(r);
                        tr.innerHTML = `
                        <td>${r.descli|| ''}</td>
                        <td>${r.desc_sottocommessa|| ''}</td>
                        <td>${r.Cd_AR || ''}</td>
                        <td>${r.Descrizione || ''} 
                        ${r.noteriga || ''}</td>
                        
                        <td>${r.x_citta || ''}</td>
                         <td>${formatDateDDMMYYYY(r.x_data) }</td>
                         <td class="text-end">${r.x_ore || 0}</td>
                    `;
                        tbody.appendChild(tr);
                    });

                    document.getElementById('giornoModalLabel').textContent =
                        "Dettaglio giornata del " + new Date(info.event.start).toLocaleDateString('it-IT');

                    const modal = new bootstrap.Modal(document.getElementById('giornoModal'));
                    modal.show();
                },
            });

            calendar.render();
        }

        // --- DataTable FTV ---
        let rawData = <?= $filteredDataJson ?>;

        let table = $('#<?= $tableId ?>').DataTable({
            data: rawData,
            scrollX: true,
            paging: true,
            responsive: false, // Disable responsive for better horizontal scroll
            fixedHeader: true,
            colReorder: false,
            // scrollX: true, // Enable horizontal scrolling
            //scrollCollapse: true,

            autoWidth: false, // Disable auto width calculation
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copyHtml5',
                    text: 'Copia'
                },
                {
                    extend: 'excelHtml5',
                    text: 'Excel'
                },
                {
                    extend: 'csvHtml5',
                    text: 'CSV'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    orientation: 'landscape' // Better for wide tables
                },
                {
                    extend: 'print',
                    text: 'Stampa'
                }
            ],
            columns: [{
                    title: 'Data',
                    data: 'DataDoc',
                    render: function(d) {
                        return new Date(d).toLocaleDateString('it-IT');
                    }
                },
                {
                    title: 'Numero',
                    data: 'NumeroDoc',

                },
                {
                    title: 'Cliente',
                    data: 'cliente',
                    render: function(data, type, row) {
                        if (!data) return "";

                        const label = ClidataOptions[data] ? ClidataOptions[data] : data;

                        if (type === 'display' || type === 'type') {
                            //return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        if (type === 'filter' || type === 'sort') {
                            // return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        return data;
                    }
                },

                {
                    title: 'Tot. Documento',
                    data: 'TotDocumentoE',
                },
                {
                    title: 'Tot. Imponibile',
                    data: 'TotImponibileE'
                },
                {
                    title: 'Tot. Avere',
                    data: 'TotProvvigione_1E',


                },
                {
                    title: 'Liquidata',
                    data: 'Liquidata',
                },
                {
                    title: 'Dt Liq.',
                    data: 'DataLiquidazione',


                },
                {
                    title: 'PDF',
                    data: 'pdf_url',
                    orderable: false,
                    render: function(data) {
                        if (!data) return '';
                        return `<button class="btn btn-sm btn-outline-danger apri-pdf"
                         data-url="
            ${data}">
                        <i class="fa fa-file-pdf"></i>
                    </button>`;
                    }
                }

            ],

        });




        let filesData = <?= $filesJson ?>;

        let tabFiles = new DataTable("#tab-files", {
            data: filesData,

            scrollX: true,
            paging: true,
            responsive: false, // Disable responsive for better horizontal scroll
            fixedHeader: false,
            colReorder: false,
            // scrollX: true, // Enable horizontal scrolling
            //scrollCollapse: true,

            autoWidth: true, // Disable auto width calculation
            columns: [{
                    data: 'id',
                    title: 'ID',
                    width: "50px"
                },
                {
                    data: 'descrizione',
                    title: 'Descrizione',
                    width: "50px"
                },
                {
                    data: 'nota',
                    title: 'Nota',
                    width: "50px"
                },

                {
                    data: 'cartella_padre',
                    title: 'Cartella Padre',
                    width: "50px"
                },
                {
                    data: 'cartella',
                    title: 'Cartella',
                    width: "50px"
                },
                {
                    title: "Nome File",
                    data: "nome_file",
                    width: "50px"
                },
                {
                    title: "Data Scadenza",
                    data: "data_scadenza",
                    width: "50px"
                },
                {
                    title: "Agente",
                    data: "cd_agente",
                    width: "50px"
                },
                {
                    title: "Azioni",
                    data: "id",
                    width: "50px",
                    orderable: false,
                    //className: "azioni-col",

                    render: function(data, type, row) {
                        if (!data) return '';

                        // Costruisco la URL per il download
                        let url = "https://dashboard.planorys.it:4433/index.php?r=statistiche/download&id=" + data;

                        return `
            <button class="btn btn-sm btn-outline-danger apri-pdf"
                    data-url="${url}" 
                    title="Apri PDF">
                <i class="fa fa-file-pdf"></i>
            </button>
        `;
                    }
                }

            ],
            scrollX: true,
            scrollCollapse: true,
            paging: true,
            fixedColumns: {
                rightColumns: 1
            },
            layout: {
                topStart: {
                    buttons: ['copy', 'excel', 'csv', 'pdf', 'print']
                }
            }
        });



        $(window).on('resize', function() {
            table.columns.adjust().draw();
        });

        // Ricalcola anche dopo il rendering completo
        setTimeout(() => table.columns.adjust().draw(), 300);

        $('#<?= $tableId ?> tbody').on('click', '.apri-pdf', function(e) {
            e.stopPropagation(); // evita conflitto col click riga
            const url = $(this).data('url');
            window.open(url, '_blank');
        });
        $('#tab-file tbody').on('click', '.apri-pdf', function(e) {
            e.stopPropagation(); // evita conflitto col click riga
            const url = $(this).data('url');
            window.open(url, '_blank');
        });
        $(document).on("click", ".apri-pdf", function() {
            const url = $(this).data("url");
            if (url) window.open(url, "_blank");
        });


    });
</script>