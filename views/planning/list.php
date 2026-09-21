<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\assets\DataTablesAsset;

$this->title = '';
$this->params['breadcrumbs'] = [];

// 1. Registriamo l'AssetBundle
DataTablesAsset::register($this);

// --- PREPARAZIONE DATI ---
$veicoliList = ArrayHelper::map(
    \app\models\Veicoli::find()
        ->where("UPPER(stato_veicolo) = 'DISPONIBILE'") 
        ->all(), 
    'id', 
    function($model) {
        return $model->targa . ' - ' . $model->marca_modello;
    }
);

$personaleList = ArrayHelper::map(\app\models\Personale::find()->all(),
 'id', function($model) {
    return $model->cognome . ' ' . $model->nome;
});
$clientiList = ArrayHelper::map(\app\models\CF::find()->select(['Cd_CF', 'Descrizione'])->all(), 'Cd_CF', 'Descrizione');
$ditteList = ArrayHelper::map(\app\models\DittaEsterna::find()->all(), 'codice', 'descrizione');

// Lista completa veicoli per la modale "Varia Veicoli" (fallback sulla lista filtrata)
$veicoliListAll = $veicoliListAll ?? $veicoliList;

// 2. CSS Custom per forzare il layout
// 2. CSS Custom per forzare il layout
$this->registerCss("
    .dataTables_filter { text-align: left !important; float: left !important; }
    .dataTables_filter label { display: flex; align-items: center; gap: 10px; }
    .dt-buttons { float: right !important; margin-bottom: 15px; }
    
    /* Contenitore filtri avanzati */
    .filtri-avanzati-container {
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    /* --- NUOVO SISTEMA DI SCROLL: Scrollbar sempre visibile e Header fisso --- */
/* --- SISTEMA DI SCROLL AVANZATO --- */
    .table-responsive {
        /* calc(100vh - X) calcola l'altezza dello schermo meno lo spazio di intestazioni e filtri.
           Se la barra rimane ancora sotto lo schermo, aumenta il 380px (es. 400px o 450px).
           Se la tabella è troppo schiacciata, diminuiscilo (es. 320px). */
        max-height: calc(100vh - 380px); 
        overflow-x: scroll; /* 'scroll' forza la presenza della traccia orizzontale */
        overflow-y: scroll; /* 'scroll' forza la presenza della traccia verticale */
        border-bottom: 1px solid #dee2e6;
    }
    
    /* FORZA LA VISIBILITA' DELLE SCROLLBAR SU IPAD / MAC E CHROME/SAFARI */
/* 1. Forza la visibilità su tutti i browser moderni (Chrome, Safari, iPadOS, Edge) */
.table-responsive::-webkit-scrollbar {
    width: 40px !important;    /* Scrollbar verticale più larga */
    height: 40px !important;   /* Scrollbar orizzontale più alta */
}

/* 2. Il \"binario\" di scorrimento */
.table-responsive::-webkit-scrollbar-track {
    background: #e9ecef !important; 
    border: 1px solid #dee2e6;
}

/* 3. La \"maniglia\" che trascini (il thumb) */
.table-responsive::-webkit-scrollbar-thumb {
    background-color: #adb5bd !important; /* Grigio medio ben visibile */
    border: 4px solid #e9ecef;            /* Crea un effetto \"staccato\" dal binario */
    border-radius: 10px !important;       /* Angoli arrotondati (più moderno) */
}

/* 4. Effetto visivo quando ci passi sopra o trascini */
.table-responsive::-webkit-scrollbar-thumb:hover,
.table-responsive::-webkit-scrollbar-thumb:active {
    background-color: #495057 !important; /* Diventa scuro al tocco */
    border: 2px solid #e9ecef;            /* La maniglia si ingrandisce visivamente */
}

/* 5. Supporto per Firefox (che usa una sintassi diversa) */
.table-responsive {
    scrollbar-width: thick !important;
    scrollbar-color: #adb5bd #e9ecef !important;
}
    
    /* Intestazione fissa che rimane incollata in alto mentre scorri */
    #planning-table thead th { 
        background-color: #f8f9fa !important; 
        color: #212529 !important; 
        vertical-align: middle; 
        white-space: nowrap; 
        position: sticky; 
        top: 0; 
        z-index: 10; /* La tiene sopra le Select2 quando scorri in giù */
        box-shadow: 0 2px 2px -1px rgba(0,0,0,0.2); /* Sottile linea di separazione */
    }
    
    /* FORZATURA DELLE LARGHEZZE DELLE COLONNE */
    #planning-table { 
        width: 100% !important;   
        min-width: 1200px !important; /* Obbliga l'iPad a creare la scrollbar laterale invece di stritolare i campi */
        table-layout: fixed !important; 
    }
    
    #planning-table th:nth-child(1), #planning-table td:nth-child(1) { width: 4% !important; }  /* Giro */
    #planning-table th:nth-child(2), #planning-table td:nth-child(2) { width: 14% !important; min-width: 160px !important; } /* Data */
    #planning-table th:nth-child(3), #planning-table td:nth-child(3) { width: 12% !important; } /* Veicolo */
    #planning-table th:nth-child(4), #planning-table td:nth-child(4) { width: 13% !important; } /* Cliente */
    #planning-table th:nth-child(5), #planning-table td:nth-child(5) { width: 15% !important; } /* Dipendenti */
    #planning-table th:nth-child(6), #planning-table td:nth-child(6) { width: 11% !important; } /* Ditta */
    #planning-table th:nth-child(7), #planning-table td:nth-child(7) { width: 4% !important; }  /* Opr */
    #planning-table th:nth-child(8), #planning-table td:nth-child(8) { width: 14% !important; } /* Luogo */
    #planning-table th:nth-child(9), #planning-table td:nth-child(9) { width: 8% !important; }  /* Stato */
    #planning-table th:nth-child(10), #planning-table td:nth-child(10) { width: 5% !important; } /* Azioni */
    
    /* Stile per gli input in linea */
    .inline-edit { border: 1px solid #ced4da; background: #fff; transition: all 0.3s ease; border-radius: 4px; }
    .inline-edit:hover, .inline-edit:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); }

    /* Evidenziazione al salvataggio */
    .save-pending { border-color: #ffc107 !important; box-shadow: 0 0 5px #ffc107 !important; }
    .save-success { border-color: #198754 !important; box-shadow: 0 0 5px #198754 !important; }
    .save-pending + .select2-container .select2-selection { border-color: #ffc107 !important; box-shadow: 0 0 5px #ffc107 !important; }
    .save-success + .select2-container .select2-selection { border-color: #198754 !important; box-shadow: 0 0 5px #198754 !important; }

    /* FIX SCALABILITA' SELECT2 */
    .select2-container, .inline-edit { width: 100% !important; max-width: 100% !important; }
    .select2-selection--multiple .select2-selection__rendered { display: flex !important; flex-direction: column !important; padding: 4px !important; gap: 4px; }
    .select2-selection--multiple .select2-selection__choice { width: 100% !important; margin: 0 !important; white-space: normal !important; text-align: left; }
    .select2-selection--multiple .select2-search--inline { display: block !important; width: 100% !important; margin: 0 !important; }
    .select2-selection--multiple .select2-search__field { width: 100% !important; min-height: 28px !important; margin: 2px 0 0 0 !important; padding: 0 4px !important; color: #212529 !important; cursor: text !important; }
    
    #planning-table tbody tr.highlight-row td {
        background-color: #fff3cd !important; border-top: 2px solid #ffecb5 !important; border-bottom: 2px solid #ffecb5 !important; transition: background-color 0.5s ease;
    }
    .highlight-row { animation: pulse-yellow 2s infinite; }
    @keyframes pulse-yellow { 0% { box-shadow: inset 0 0 0 1000px rgba(255, 243, 205, 1); } 50% { box-shadow: inset 0 0 0 1000px rgba(255, 243, 205, 0.5); } 100% { box-shadow: inset 0 0 0 1000px rgba(255, 243, 205, 1); } }
    
    #planning-table input[type=\"date\"], #planning-table input[type=\"time\"] { width: 100% !important; padding: 2px 5px !important; font-size: 0.85rem !important; }
    .d-flex.gap-1 input[type=\"time\"] { min-width: 0; flex: 1; }
    #planning-table td { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding: 4px !important; }
");
?>

<div class="planning-list card p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0 text-primary">
            <i class="fa-solid fa-calendar-days"></i> Registro Attività Planning
        </h2>

        <div class="d-flex gap-2"> 
            <?= Html::a('<i class="fa fa-plus"></i> Nuova Attività', Url::to(['create']), [
                'class' => 'btn btn-success show-modal',
                'title' => 'Crea Nuova Attività'
            ]) ?>
  
<?= Html::button('<i class="fa fa-clone"></i> Copia Giornata', [
                'id' => 'btn-open-copy-modal', // <-- Aggiunto ID
                'class' => 'btn btn-warning text-dark fw-bold',
                'title' => 'Copia un intero giorno in un\'altra data'
            ]) ?>
            <?= Html::button('<i class="fa fa-exchange-alt"></i> Varia Veicoli', [
                'id' => 'btn-open-varia-modal',
                'class' => 'btn btn-info text-white fw-bold',
                'title' => 'Sostituisci un veicolo in tutte le attività di una data'
            ]) ?>
            <?= Html::button('<i class="fa-solid fa-file-pdf"></i> Export PDF', [
                'id' => 'btn-export-pdf',
                'class' => 'btn btn-danger',
                'title' => 'Esporta in PDF la lista filtrata'
            ]) ?>
            <?= Html::a('<i class="fa fa-arrow-left"></i> Torna al Calendario', ['index'], [
                'class' => 'btn btn-outline-secondary'
            ]) ?>
        </div>
    </div>

    <div class="filtri-avanzati-container p-3 mb-4 shadow-sm">
        <h6 class="text-secondary mb-3"><i class="fa fa-filter"></i> Filtri Avanzati</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Data Attività</label>
                <input type="date" id="filtro-data"
                 class="form-control form-control-sm trigger-filtro"
                  onkeydown="return false;" onpaste="return false;" style="cursor: default;"
                 >
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Cliente</label>
                <?= Html::dropDownList('filtro_cliente', null, $clientiList, [
                    'id' => 'filtro-cliente', 'class' => 'form-control form-select-sm select2-filtro trigger-filtro', 'prompt' => 'Tutti i Clienti'
                ]) ?>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Veicolo</label>
                <?= Html::dropDownList('filtro_veicolo', null, $veicoliList, [
                    'id' => 'filtro-veicolo', 'class' => 'form-control form-select-sm select2-filtro trigger-filtro', 'prompt' => 'Tutti i Veicoli'
                ]) ?>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Dipendente</label>
                <?= Html::dropDownList('filtro_dipendente', null, $personaleList, [
                    'id' => 'filtro-dipendente', 'class' => 'form-control form-select-sm select2-filtro trigger-filtro', 'prompt' => 'Tutti i Dipendenti'
                ]) ?>
            </div>
        </div>
        <div class="row g-3 mt-1 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Periodo - Da</label>
                <input type="date" id="filtro-data-da" class="form-control form-control-sm"
                       value="<?= Html::encode($dataDa ?? '') ?>" onkeydown="return false;" onpaste="return false;">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Periodo - A</label>
                <input type="date" id="filtro-data-a" class="form-control form-control-sm"
                       value="<?= Html::encode($dataA ?? '') ?>" onkeydown="return false;" onpaste="return false;">
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-sm btn-primary" id="btn-applica-periodo">
                    <i class="fa fa-search"></i> Applica periodo
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-azzera-periodo">
                    Azzera
                </button>
            </div>
        </div>
        <div class="mt-3 text-end">
            <button class="btn btn-sm btn-outline-danger" type="button" id="btn-reset-filtri">
                <i class="fa-solid fa-trash-can"></i> Svuota Filtri
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table id="planning-table" class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>Giro</th>
                    <th>Data/Orari</th>
                    <th>Veicolo</th>
                    <th>Cliente</th>
                    <th>Dipendenti</th>
                    <th>Ditta Esterna</th>
                    <th title="Quantità Operai">Opr.</th> 
                    <th>Luogo</th>
                    <th>Stato</th>
                    <th>Azioni</th> 
                </tr>
            </thead>
            <tbody>
                <?php foreach ($models as $m): ?>
                    <?= $this->render('_riga', [
                        'm' => $m,
                        'veicoliList' => $veicoliList,
                        'personaleList' => $personaleList,
                        'clientiList' => $clientiList,
                        'ditteList' => $ditteList,
                        'highlight' => false
                    ]) ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$updateUrl = Url::to(['planning/inline-update']);

$js = <<<JS
$(document).ready(function() {

    // 1. Inizializza i Select2 per i filtri esterni
    $('.select2-filtro').select2({ width: '100%' });

    // --- MOTORE DI RICERCA INFALLIBILE BASATO SUI FILTRI ESTERNI ---
    $.fn.dataTable.ext.search.push(
        function(settings, searchData, index, rowData, counter) {
            var rowNode = settings.aoData[index].nTr;
            var \$row = $(rowNode);
            
            // FILTRO DATA
            var targetData = $('#filtro-data').val();
            if (targetData) {
                var rowDataVal = \$row.find('input[type="date"]').val();
                if (rowDataVal !== targetData) return false;
            }

            // FILTRO CLIENTE
            var targetCliente = $('#filtro-cliente').val();
            if (targetCliente) {
                var rowCliente = \$row.find('td:eq(3) select').val(); 
                if (rowCliente != targetCliente) return false;
            }

            // FILTRO VEICOLO
            var targetVeicolo = $('#filtro-veicolo').val();
            if (targetVeicolo) {
                var rowVeicolo = \$row.find('td:eq(2) select').val();
                if (rowVeicolo != targetVeicolo) return false;
            }

            // FILTRO DIPENDENTE
            var targetDipendente = $('#filtro-dipendente').val();
            if (targetDipendente) {
                var rowDipendenti = \$row.find('td:eq(4) select').val(); 
                if (!rowDipendenti || rowDipendenti.indexOf(targetDipendente) === -1) {
                    return false; 
                }
            }

            // FILTRO BARRA DI RICERCA NATIVA
            var globalSearch = settings.oPreviousSearch.sSearch.toLowerCase().trim();
            if (globalSearch) {
                var matchTrovato = false;
                
                \$row.find('td').each(function() {
                    if ($(this).find('input[type="date"]').length > 0) return true; 
                    
                    var testoCella = $(this).text().toLowerCase();
                    var testoInput = "";
                    var inputs = $(this).find('input[type="text"], textarea, .inline-edit');
                    if(inputs.length > 0) { testoInput = inputs.val().toLowerCase(); }
                    
                    if ((testoCella + " " + testoInput).indexOf(globalSearch) !== -1) {
                        matchTrovato = true; return false;
                    }
                });
                return matchTrovato;
            }

            return true; 
        }
    );

    // Esegue la ricerca ad ogni cambio dei filtri esterni
    $('.trigger-filtro').on('change', function() {
        $('#planning-table').DataTable().draw();
    });

    // Tasto Svuota Filtri
    $('#btn-reset-filtri').on('click', function() {
        $('#filtro-data').val('');
        $('#filtro-cliente').val(null).trigger('change');
        $('#filtro-veicolo').val(null).trigger('change');
        $('#filtro-dipendente').val(null).trigger('change');
        $('#planning-table').DataTable().search('').draw(); 
    });

    // --- CONFIGURAZIONE ESPORTAZIONE SELETTIVA ---
    var customExportOptions1 = {
        columns: ':visible', 
        format: {
            header: function(text, index) {
                var headersMap = {
                    'Data/Orari': 'Orario Partenza / Fine',
                    'Veicolo': 'Mezzi',
                    'Dipendenti': 'Nominativi',
                    'Luogo': 'Indirizzo',
                    'Azioni': 'Note'
                };
                return headersMap[text] ? headersMap[text] : '__DROP__';
            },
            body: function(html, rowIdx, colIdx, node) {
                var cell = $(node);
                var row = cell.closest('tr');
                
                var tableHeaderNode = $('#planning-table thead th').eq(colIdx);
                var headerText = tableHeaderNode.text().trim();

                if (headerText === 'Data/Orari') {
                    var rawDate = cell.find('input[type="date"]').val() || '';
                    var dateFormatted = '';
                    if (rawDate) {
                        var parts = rawDate.split('-');
                        dateFormatted = parts[2] + '/' + parts[1] + '/' + parts[0];
                    }
                    var timeStart = cell.find('input[type="time"]').first().val() || cell.find('input[data-field="ora_inizio"]').val() || '';
                    var timeEnd = cell.find('input[type="time"]').last().val() || cell.find('input[data-field="ora_fine"]').val() || '';
                    return dateFormatted + ' (Partenza: ' + timeStart + ' - Fine: ' + timeEnd + ')';
                }
                
                if (headerText === 'Veicolo') {
                    var mezzoSelect = cell.find('select');
                    if (mezzoSelect.length > 0) return mezzoSelect.find('option:selected').text().trim();
                    return cell.find('input').val() || cell.text().trim();
                }
                
                if (headerText === 'Dipendenti') {
                    var selectedTexts = [];
                    cell.find('select option:selected').each(function() {
                        var txt = $(this).text().trim();
                        if(txt) selectedTexts.push(txt);
                    });
                    if (selectedTexts.length === 0) {
                        cell.find('.select2-selection__choice').each(function() {
                            var choiceText = $(this).text().replace('×', '').trim();
                            if(choiceText) selectedTexts.push(choiceText);
                        });
                    }
                    return selectedTexts.join(', ');
                }
                
                if (headerText === 'Luogo') {
                    var inputLuogo = cell.find('input[type="text"], input[data-field="luogo"], .inline-edit');
                    if (inputLuogo.length > 0) return inputLuogo.val().trim();
                    return cell.text().trim();
                }
                
                if (headerText === 'Azioni') {
                    var inputNota = row.find('input[data-field="nota"], textarea[data-field="nota"]');
                    if (inputNota.length === 0) {
                        inputNota = cell.find('input[type="text"], textarea');
                    }
                    if (inputNota.length > 0) return inputNota.val().trim();
                    return ''; 
                }
                
                return '__DROP__'; 
            }
        }
    };
var customExportOptions = {
    // Indici colonne reali: 7 (Indirizzo), 1 (Data), 4 (Dipendenti), 2 (Veicolo), 8 (Stato)
    columns: [7, 1, 4, 2, 8], 
format: {
  header: function(text, index) {
        // Mappiamo gli indici originali che hai usato in 'columns'
        var map = {
            7: 'Indirizzo',
            1: 'Orario Partenza / Fine',
            4: 'Nominativi',
            2: 'Mezzi',
            8: 'Stato'
        };
        return map[index] || text;
    },
        body: function(html, rowIdx, colIdx, node) {
            var row = $(node).closest('tr');
            
            // Usiamo gli indici (0, 1, 2, 3, 4) che corrispondono all'ordine 
            // definito nel tuo array 'columns: [7, 1, 4, 2, 8]'
            
            // 0: INDIRIZZO (corrisponde alla colonna 7 originale)
            if (colIdx === 0) return row.find('[data-field="indirizzo"]').val() || '';
            
            // 1: ORARIO (corrisponde alla colonna 1 originale)
            if (colIdx === 1) {
                var d = row.find('input[type="date"]').val() || '';
                var dF = d ? d.split('-').reverse().join('/') : '';
                var ini = row.find('input[data-field="ora_inizio"]').val() || '';
                var fine = row.find('input[data-field="ora_fine"]').val() || '';
                return dF + ' (P: ' + ini + ' - F: ' + fine + ')';
            }
            
            // 2: NOMINATIVI (corrisponde alla colonna 4 originale)
            if (colIdx === 2) {
                var nomi = [];
                row.find('td:eq(4) .select2-selection__choice').each(function() {
                    nomi.push($(this).text().replace('×', '').trim());
                });
                return nomi.join(', ');
            }
            
            // 3: MEZZI (corrisponde alla colonna 2 originale)
            if (colIdx === 3) {
                return row.find('td:eq(2) select option:selected').text().trim() || '';
            }
            
            // 4: STATO (corrisponde alla colonna 8 originale)
            if (colIdx === 4) {
                return row.find('td:eq(8) select option:selected').text().trim() || '';
            }
            
            return '';
        }
    }
};
var table = $('#planning-table').DataTable({
    dom: '<"row align-items-center"<"col-md-6"f><"col-md-6 text-end"B>>rt<"row align-items-center mt-3"<"col-md-6"i><"col-md-6"p>>',
    buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-secondary', 
            text: '<i class="fa-solid fa-copy"> Copia </i>'  },
            { extend: 'csv', className: 'btn btn-sm btn-info text-white', text: '<i class="fa-solid fa-file-csv"> CSV </i>',exportOptions: customExportOptions },
            { extend: 'excel', className: 'btn btn-sm btn-success', text: '<i class="fa-solid fa-file-excel"> Excel </i>',exportOptions: customExportOptions },
            { extend: 'pdfHtml5', className: 'btn btn-sm btn-danger', text: '<i class="fa-solid fa-file-pdf"> Pdf </i>', orientation: 'landscape', pageSize: 'A4' ,exportOptions: customExportOptions},
            { extend: 'print', className: 'btn btn-sm btn-primary', 
            text: '<i class="fa-solid fa-print"> Stampa </i>',
        exportOptions: customExportOptions,
                // Personalizziamo la finestra di stampa per farla bella e pulita
                customize: function (win) {
                    $(win.document.body)
                        .css('font-size', '10pt')
                        .prepend('<h3>Registro Attività Planning</h3>');
 
                    $(win.document.body).find('table')
                        .addClass('table table-bordered')
                        .css('font-size', 'inherit')
                        .css('width', '100%');
                        
                    // Rimuoviamo eventuali tag di input o select residui dalla stampa
                    $(win.document.body).find('input, select, textarea').remove();
                }
        
        }
    ],
        pageLength: 25,
        scrollX: false,
        autoWidth: false, 
        order: [[1, "desc"]]
    });

    // Ricalcolo dimensioni
    var tableContainer = document.querySelector('.table-responsive');
    if (tableContainer) {
        new ResizeObserver(function() { if($.fn.DataTable.isDataTable('#planning-table')) { table.columns.adjust(); } }).observe(tableContainer);
    }
    setTimeout(function() { if($.fn.DataTable.isDataTable('#planning-table')) { table.columns.adjust(); } }, 400);

    // --- LOGICA MUTUA ESCLUSIONE CON KARTIK SELECT2 ---
    $(document).on('change', '.inline-edit:not(.filtri-avanzati-container .select2-filtro)', function(e) {
        if($(this).closest('.filtri-avanzati-container').length > 0) return;

        var \$el = $(this);
        if(!\$el.data('field')) return; 

        var id = \$el.data('id');
        var field = \$el.data('field');
        var value = \$el.val();
 
        const Toast = Swal.mixin({
            toast: true, position: 'top', showConfirmButton: false, timer: 2000, timerProgressBar: true
        });

        var \$riga = \$el.closest('tr');
        var \$selectPersonale = \$riga.find('.select2-personale');
        var \$selectDitta = \$riga.find('.select2-ditta');

        if (field === 'ditta_esterna') {
            if (value && value !== '') {
               \$selectPersonale.val(null).trigger('change').prop('disabled', true);
            } else {
                \$selectPersonale.prop('disabled', false);
            }
        }
        
        if (field === 'personale_ids') {
            if (value && value.length > 0) {
                \$selectDitta.val(null).trigger('change').prop('disabled', true);
            } else {
                \$selectDitta.prop('disabled', false);
            }
        }
        \$el.removeClass('save-success').addClass('save-pending');

        $.ajax({
            url: '{$updateUrl}',
            type: 'POST',
            data: { id: id, field: field, value: value, _csrf: yii.getCsrfToken() },
            success: function(res) {
                if(res.success) {
                    Toast.fire({ icon: 'success', title: 'Dato salvato con successo' });
                    \$el.removeClass('save-pending').addClass('save-success');
                    setTimeout(function(){ \$el.removeClass('save-success'); }, 2000);
                } else {
                    \$el.removeClass('save-pending');
                    Swal.fire({ icon: 'error', title: 'Errore di Salvataggio', text: res.error });
                }
            },
            error: function() {
                \$el.removeClass('save-pending');
                Toast.fire({ icon: 'error', title: 'Errore di rete o sessione scaduta' });
            }
        });
    });
});

// --- APERTURA MODALE RISOLTA CON JQUERY ---
$(document).on('click', '.show-modal', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    
    // Mostriamo un loader visivo dentro la modale
    $('#modalContent').html('<div class="text-center my-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Caricamento in corso...</p></div>');
    
    // Apriamo la modale utilizzando il metodo jQuery classico e infallibile
    $('#modal-create').modal('show');
    
    // Carichiamo l'URL della vista di creazione
    $('#modalContent').load(url);
});
// --- APERTURA MODALE COPIA GIORNATA FORZATA ---
$(document).on('click', '#btn-open-copy-modal', function(e) {
    e.preventDefault();
    $('#modal-copy-day').modal('show');
});

$(document).on('submit', '#planning-form-dynamic', function(e) {
    e.preventDefault();
    var \$form = $(this); 
    $.ajax({
        url: \$form.attr('action'), type: 'post', data: \$form.serialize(), 
        success: function(res) {
            if(res.success) {
                // Chiudiamo la modale utilizzando jQuery
                $('#modal-create').modal('hide');
                location.reload(); 
            } else { Swal.fire({ icon: 'error', title: 'Attenzione', text: res.error }); }
        }
    });
    return false;
});

// --- DUPLICAZIONE RIGA ---
$(document).on('click', '.btn-duplicate-ajax', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    var \$clickedRow = $(this).closest('tr');

    Swal.fire({
        title: 'Duplicare questa attività?', text: "Verrà creata una copia identica nel registro.", icon: 'question',
        showCancelButton: true, confirmButtonColor: '#0dcaf0', cancelButtonColor: '#6c757d', confirmButtonText: '<i class="fa fa-copy"></i> Sì, duplica', cancelButtonText: 'Annulla'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(url, { _csrf: yii.getCsrfToken() }, function(res) {
                if (res.success) {
                    var dt = $('#planning-table').DataTable();
                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = '<table><tbody>' + res.html + '</tbody></table>';
                    var trNode = wrapper.querySelector('tr');
                    
                    if (trNode) {
                        var rowNode = dt.row.add(trNode).draw(false).node();
                        
                        $(rowNode).insertBefore(\$clickedRow);
                        $(rowNode).addClass('highlight-row');
                        
                        var scripts = wrapper.querySelectorAll('script');
                        scripts.forEach(function(s) {
                            if (!s.src) { 
                                var script = document.createElement('script');
                                script.textContent = s.textContent;
                                document.body.appendChild(script);
                            }
                        });
                        
                        Swal.fire({ toast: true, position: 'top', icon: 'success', title: 'Attività Duplicata!', showConfirmButton: false, timer: 1500 });
                    } else { Swal.fire('Errore', 'Impossibile inserire la riga.', 'error'); }
                } else { Swal.fire('Errore', res.error, 'error'); }
            }).fail(function() { Swal.fire('Errore di Rete', 'Server non risponde.', 'error'); });
        }
    });
});




 // --- LOGICA WIZARD COPIA GIORNATA ---
    $('#btn-esegui-copia').on('click', function() {
        var sourceDate = $('#copy-source-date').val();
        var targetDate = $('#copy-target-date').val();

        if (!sourceDate || !targetDate) {
            Swal.fire({ icon: 'warning', title: 'Attenzione', text: 'Seleziona entrambe le date.' });
            return;
        }

        if (sourceDate === targetDate) {
            Swal.fire({ icon: 'warning', title: 'Attenzione', text: 'Le date di origine e destinazione devono essere diverse.' });
            return;
        }

        Swal.fire({
            title: 'Sei sicuro?',
            text: "Vuoi davvero copiare tutte le attività dal " + sourceDate.split('-').reverse().join('/') + " al " + targetDate.split('-').reverse().join('/') + "?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-check"></i> Procedi',
            cancelButtonText: 'Annulla'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostra un loader durante il processo
                Swal.fire({
                    title: 'Copia in corso...',
                    text: 'Attendere prego',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: 'index.php?r=planning/copy-day', // Assicurati che l'URL punti alla tua nuova azione
                    type: 'POST',
                    data: {
                        source_date: sourceDate,
                        target_date: targetDate,
                        _csrf: yii.getCsrfToken()
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Fatto!', text: res.message }).then(() => {
                                // Chiude la modale e ricarica la pagina per mostrare i nuovi dati
                                $('#modal-copy-day').modal('hide');
                                location.reload();
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Errore', text: res.error });
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Errore di Rete', text: 'Impossibile contattare il server.' });
                    }
                });
            }
        });
    });

JS;
$this->registerJs($js);

// --- JS aggiuntivo: Varia Veicoli ed Export PDF ---
$this->registerJs(<<<'JS'
// --- VARIA VEICOLI ---
$(document).on('click', '#btn-open-varia-modal', function () {
    var d = $('#filtro-data').val();
    if (d) { $('#varia-data').val(d); }
    $('#modal-varia-veicoli').modal('show');
});

$(document).on('click', '#btn-esegui-varia', function () {
    var dataVal = $('#varia-data').val();
    var sourceId = $('#varia-source-veicolo').val();
    var targetId = $('#varia-target-veicolo').val();

    if (!dataVal || !sourceId || !targetId) {
        alert('Compila tutti i campi (Data, Veicolo da sostituire, Veicolo sostituto).');
        return;
    }
    if (sourceId === targetId) {
        alert('La targa di origine e quella di sostituzione devono essere diverse.');
        return;
    }

    var btn = $(this);
    btn.prop('disabled', true);

    $.ajax({
        url: 'index.php?r=planning/varia-veicoli',
        type: 'POST',
        data: {
            data: dataVal,
            source_veicolo_id: sourceId,
            target_veicolo_id: targetId,
            _csrf: yii.getCsrfToken()
        },
        dataType: 'json',
        success: function (res) {
            btn.prop('disabled', false);
            if (res.success) {
                $('#modal-varia-veicoli').modal('hide');
                alert(String(res.message || 'Operazione completata.').replace(/<[^>]*>/g, ''));
                location.reload();
            } else {
                alert(String(res.error || 'Errore').replace(/<[^>]*>/g, ''));
            }
        },
        error: function () {
            btn.prop('disabled', false);
            alert('Errore di rete durante la variazione veicoli.');
        }
    });
});

// --- EXPORT PDF ---
$(document).on('click', '#btn-export-pdf', function () {
    var params = {};
    var dataVal = $('#filtro-data').val();
    if (dataVal) { params.data = dataVal; }
    var cliente = $('#filtro-cliente').val();
    if (cliente) { params.cliente = cliente; }
    var veicolo = $('#filtro-veicolo').val();
    if (veicolo) { params.veicolo = veicolo; }
    var dipendente = $('#filtro-dipendente').val();
    if (dipendente) { params.dipendente = dipendente; }

    var qs = $.param(params);
    var url = 'index.php?r=planning/export-pdf' + (qs ? '&' + qs : '');
    window.open(url, '_blank');
});

// --- PERIODO (intervallo date server-side) ---
$(document).on('click', '#btn-applica-periodo', function () {
    var da = $('#filtro-data-da').val();
    var a = $('#filtro-data-a').val();
    var params = [];
    if (da) { params.push('data_da=' + encodeURIComponent(da)); }
    if (a) { params.push('data_a=' + encodeURIComponent(a)); }
    window.location.href = 'index.php?r=planning/list' + (params.length ? '&' + params.join('&') : '');
});

$(document).on('click', '#btn-azzera-periodo', function () {
    window.location.href = 'index.php?r=planning/list';
});
JS
);
?>

<div class="modal fade" id="modal-create" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-primary"><i class="fa fa-calendar-plus"></i> Nuova Attività</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="modalContent">
            <div class="text-center my-4">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2">Caricamento in corso...</p>
                   <div class="btn btn-secondary" data-bs-dismiss="modal">Esc per Uscire</div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>




<div class="modal fade" id="modal-copy-day" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title text-dark"><i class="fa fa-clone"></i> Duplica Intera Giornata</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="form-copy-day">
            <div class="mb-3">
                <label class="form-label fw-bold text-primary">Da: (Data di Origine)</label>
                <input type="date" class="form-control" id="copy-source-date" required>
                <small class="text-muted">Seleziona il giorno da cui copiare le attività.</small>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold text-success">A: (Data di Destinazione)</label>
                <input type="date" class="form-control" id="copy-target-date" required>
                <small class="text-muted">I record verranno ricreati in questa data con stato "Da Iniziare".</small>
            </div>
        </form>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <div class="btn btn-secondary" data-bs-dismiss="modal">Premi Esc per Uscire</div>
        <button type="button" class="btn btn-warning text-dark fw-bold" id="btn-esegui-copia">
            <i class="fa fa-play"></i> Esegui Copia
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-varia-veicoli" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-info">
        <h5 class="modal-title text-white"><i class="fa fa-exchange-alt"></i> Varia Veicoli</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="form-varia-veicoli">
            <div class="mb-3">
                <label class="form-label fw-bold">Data</label>
                <input type="date" class="form-control" id="varia-data" required>
                <small class="text-muted">Sostituzione valida per tutte le attività di questa data.</small>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Veicolo da sostituire</label>
                <?= Html::dropDownList('varia_source_veicolo', null, $veicoliListAll, [
                    'id' => 'varia-source-veicolo', 'class' => 'form-control', 'prompt' => 'Seleziona veicolo...'
                ]) ?>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Veicolo sostituto</label>
                <?= Html::dropDownList('varia_target_veicolo', null, $veicoliListAll, [
                    'id' => 'varia-target-veicolo', 'class' => 'form-control', 'prompt' => 'Seleziona veicolo...'
                ]) ?>
            </div>
        </form>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <div class="btn btn-secondary" data-bs-dismiss="modal">Annulla</div>
        <button type="button" class="btn btn-info text-white fw-bold" id="btn-esegui-varia">
            <i class="fa fa-play"></i> Esegui
        </button>
      </div>
    </div>
  </div>
</div>

 