<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\DataTablesAsset;

/** @var yii\web\View $this */

$this->title = 'Gestione Etichettatura ETH';

// Registriamo l'Asset che include jQuery e DataTables
DataTablesAsset::register($this);

// CSS specifico
$this->registerCss("
    .master-column { border-right: 1px solid #dee2e6; height: calc(100vh - 250px); overflow-y: auto; background-color: #fff; }
    .detail-column { height: calc(100vh - 250px); overflow-y: auto; background-color: #f8f9fa; border-radius: 0 5px 5px 0; }
    .selected-row { background-color: #e3f2fd !important; font-weight: bold; border-left: 4px solid #007bff; }
    .table-sm td, .table-sm th { font-size: 0.85rem; padding: 0.5rem; }
    /* Fix per dropdown che finiscono sotto altri elementi */
    .btn-group .dropdown-menu { z-index: 1050 !important; }
");
?>
<style>
/* Impedisce la visualizzazione contemporanea se il JS non ha ancora deciso */
.opt-reprint { display: none; }
.btn-warning .opt-generate { display: none !important; }
</style>
<div class="etichette-index">
    <div class="card shadow-sm border-primary mb-3">
        <div class="card-header bg-primary text-white">
            <h5 class="m-0"><i class="fas fa-filter"></i> Filtri Ricerca Documenti (DB4)</h5>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-2">
                    <label class="small font-weight-bold">Tipo Doc.</label>
                    <?= \kartik\select2\Select2::widget([
                        'name' => 'f-cd-do',
                        'id' => 'f-cd-do',
                        'data' => $tipiDoc,
                        'options' => ['placeholder' => 'Seleziona...', 'class' => 'form-control-sm'],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>
                </div>
                <div class="col-md-3">
                    <label class="small font-weight-bold">Cliente</label>
                    <?= \kartik\select2\Select2::widget([
                        'name' => 'f-cliente',
                        'id' => 'f-cliente',
                        'data' => $clienti,
                        'options' => ['placeholder' => 'Cerca cliente...', 'class' => 'form-control-sm'],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>
                </div>
                <div class="col-md-2">
                    <label class="small font-weight-bold">P.IVA / CF</label>
                    <input type="text" id="f-pivacf" class="form-control form-control-sm" placeholder="Inserisci...">
                </div>
                <div class="col-md-1">
                    <label class="small font-weight-bold">Data Doc</label>
                    <input type="date" id="f-data" class="form-control form-control-sm">
                </div>
                <div class="col-md-1">
                    <label class="small font-weight-bold">Num. Doc</label>
                    <input type="text" id="f-numero" class="form-control form-control-sm">
                </div>
                <div class="col-md-1">
                    <label class="small font-weight-bold">Num. Rif</label>
                    <input type="text" id="f-numerorif" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button id="btn-search" class="btn btn-primary btn-sm w-100 font-weight-bold">
                        <i class="fas fa-search mr-1"></i> CERCA
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-0 border rounded shadow-sm bg-white overflow-hidden">
        <div class="col-md-4 master-column p-2">
            <h6 class="text-muted border-bottom pb-2 font-weight-bold"><i class="fas fa-list mr-2"></i>Documenti</h6>
            <table id="table-testate" class="table table-sm table-hover w-100">
                <thead>
                    <tr>
                        <th>Doc</th>
                        <th>Data</th>
                        <th>Num.</th>
                        <th>Cliente</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="col-md-8 detail-column p-3">
            <div id="righe-placeholder" class="text-center mt-5">
                <i class="fas fa-hand-pointer fa-3x text-muted mb-3"></i>
                <p class="text-muted">Seleziona un documento a sinistra</p>
            </div>

            <div id="righe-content" style="display:none;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 id="detail-title" class="text-primary m-0">Dettaglio</h5>
                    
<div class="btn-group dropdown">
    <button id="btn-azioni-eth" type="button" class="btn btn-dark btn-sm dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown">
        <i class="fas fa-barcode mr-1"></i> AZIONI ETICHETTATURA
    </button>
<div id="dropdown-menu-etichette" class="dropdown-menu dropdown-menu-right shadow">
    
    <div class="opt-generate">
        <h6 class="dropdown-header font-weight-bold text-primary">Nuova Generazione GUID</h6>
        <a class="dropdown-item" href="#" onclick="processEtichette('doc'); return false;">
            <i class="fas fa-file-invoice text-primary mr-2"></i> 1 GUID per tutto il Documento
        </a>
        <a class="dropdown-item" href="#" onclick="processEtichette('art'); return false;">
            <i class="fas fa-tag text-success mr-2"></i> 1 GUID per ogni Articolo (1 etichetta)
        </a>
        <a class="dropdown-item" href="#" onclick="processEtichette('qty'); return false;">
            <i class="fas fa-copy text-info mr-2"></i> 1 GUID per Articolo (Etichetta x Pezzo)
        </a>
    </div>

    <div class="opt-reprint" style="display:none;">
        <h6 class="dropdown-header font-weight-bold text-warning">Ristampa Esistenti</h6>
        <a class="dropdown-item" href="#" onclick="processEtichette('reprint_doc'); return false;">
            <i class="fas fa-file-alt text-warning mr-2"></i> Ristampa TUTTO il Documento
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="#" onclick="processEtichette('reprint'); return false;">
            <i class="fas fa-print text-warning mr-2"></i> Ristampa solo righe selezionate
        </a>
    </div>
</div>
</div>
                </div>

                <div class="table-responsive bg-white rounded border">
                    <table id="table-righe" class="table table-sm table-striped mb-0 w-100">
                        <thead class="bg-light">
                            <tr>
                                <th width="30"><input type="checkbox" id="check-all-righe"></th>
                                <th>Cod. Articolo</th>
                                <th>Descrizione</th>
                                <th class="text-center">Q.tà</th>
                                <th>ETH Attuale</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$fetchTestateUrl = Url::to(['etichette/fetch-testate']);
$fetchRigheUrl = Url::to(['etichette/fetch-righe']);
$processUrl = Url::to(['etichette/process']);

$js = <<<JS
var currentIdTes = null;
var tableTestate;

// Inizializzazione Tabella Testate
tableTestate = $('#table-testate').DataTable({
    paging: true,
    pageLength: 25,
    dom: 'tp',
    columns: [
        { data: 'cd_do' },
        { data: 'datadoc', render: function(d){ return d ? d.split('-').reverse().join('/') : ''; } },
        { data: 'numerodoc' },
        { data: 'cf_desc' }
    ],
    language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json' }
});

// Cerca Documenti
$('#btn-search').on('click', function() {
    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    
    $.get('$fetchTestateUrl', {
        cd_do: $('#f-cd-do').val(),
        cd_cf: $('#f-cliente').val(),
        pivacf: $('#f-pivacf').val(),
        data: $('#f-data').val(),
        numero: $('#f-numero').val(),
        numerorif: $('#f-numerorif').val()
    }, function(res) {
        tableTestate.clear().rows.add(res).draw();
        btn.prop('disabled', false).html('<i class="fas fa-search"></i> Cerca');
    }).fail(function() {
        alert('Errore nel caricamento dei dati.');
        btn.prop('disabled', false).html('<i class="fas fa-search"></i> Cerca');
    });
});

// Click su una riga della Testata
var docHasEth = false; 
$('#table-testate tbody').on('click', 'tr', function() {
    var data = tableTestate.row(this).data();
    if(!data) return;
    
    currentIdTes = data.id_dotes; 
    
    // Controlliamo bene il valore di x_eth che arriva dal server
    var ethValue = data.x_eth || ''; 
    docHasEth = (ethValue !== null && ethValue !== '' && ethValue !== undefined); 

    $('#table-testate tr').removeClass('selected-row');
    $(this).addClass('selected-row');
    
    $('#righe-placeholder').hide();
    $('#righe-content').show();
    $('#detail-title').html('<i class="fas fa-file-invoice"></i> Doc. ' + data.cd_do + ' n. ' + data.numerodoc);
    
    loadRighe(currentIdTes);
    // IMPORTANTE: chiamiamo updateActionMenu qui per aggiornare il tasto PRIMA di selezionare le righe
    updateActionMenu(); 
});

function loadRighe(id) {
    $('#table-righe tbody').html('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Caricamento...</td></tr>');
    $.get('$fetchRigheUrl', { id_dotes: id }, function(res) {
        var html = '';
        if(res.length === 0) {
            html = '<tr><td colspan="5" class="text-center text-muted">Nessuna riga valida (con Cod. Articolo) trovata</td></tr>';
        } else {
            res.forEach(function(r) {
                html += '<tr>' +
                    '<td><input type="checkbox" class="row-check" value="'+r.Id_DoRig+'"></td>' +
                    '<td>'+r.cd_ar+'</td>' +
                    '<td>'+r.descrizione+'</td>' +
                    '<td class="text-center font-weight-bold">'+r.qta+'</td>' +
                    '<td>'+(r.x_eth ? '<span class="badge badge-secondary">'+r.x_eth+'</span>' : '<small class="text-muted">-</small>')+'</td>' +
                '</tr>';
            });
        }
        $('#table-righe tbody').html(html);
    });
}

// Checkbox globale
$(document).on('change', '#check-all-righe', function() {
    $('.row-check').prop('checked', $(this).is(':checked'));
});

window.processEtichette = function(mode) {
    if (!currentIdTes) {
        Swal.fire('Attenzione', 'Devi prima selezionare un documento.', 'warning');
        return;
    }

    var selected = [];
    $('.row-check:checked').each(function() {
        selected.push($(this).val());
    });

    // Definizione dinamica di Titolo e Messaggio per lo SweetAlert
    var title = "Conferma Operazione";
    var text = "";
    var icon = "question";
    var confirmButtonText = "Sì, procedi";

    if (mode === 'doc') {
        text = "Verrà generato un NUOVO GUID unico per l'intero documento. Le etichette esistenti saranno sovrascritte.";
        icon = "warning";
    } else if (mode === 'art') {
        text = "Verrà generato un NUOVO GUID per ogni articolo selezionato (" + selected.length + " righe).";
    } else if (mode === 'qty') {
        text = "Verranno generati nuovi GUID e stampate etichette per ogni pezzo in riga (" + selected.length + " righe).";
    } else if (mode === 'reprint_doc') {
        text = "Verrà ristampata l'etichetta globale del documento senza cambiare i codici.";
        icon = "info";
        confirmButtonText = "Ristampa Tutto";
    } else if (mode === 'reprint') {
        text = "Verranno ristampate le etichette per le " + selected.length + " righe selezionate.";
        icon = "info";
        confirmButtonText = "Ristampa Selezione";
    }

    // Controllo righe per i modi che le richiedono
    if ((mode === 'art' || mode === 'qty' || mode === 'reprint') && selected.length === 0) {
        Swal.fire('Selezione mancante', 'Spunta almeno una riga per questa operazione.', 'error');
        return;
    }

    // Esecuzione con SweetAlert2
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Annulla'
    }).then((result) => {
        if (result.isConfirmed) {
            var params = $.param({
                id_dotes: currentIdTes,
                mode: mode,
                selected_righe: JSON.stringify(selected),
                // Parametri opzionali per le misure se vuoi testare la funzione PDF di prima
                w: 80,
                h: 50
            });

            var url = '$processUrl' + ( '$processUrl'.indexOf('?') !== -1 ? '&' : '?' ) + params;
            var win = window.open(url, '_blank');
            
            if (win) {
                win.focus();
                Swal.fire('Inviato!', 'Il PDF è in fase di generazione.', 'success');
                setTimeout(function(){ loadRighe(currentIdTes); }, 2500);
            } else {
                Swal.fire('Errore Popup', 'Il browser ha bloccato l\'apertura del PDF.', 'error');
            }
        }
    });
};

// Variabile globale che aggiorneremo ogni volta che si clicca su un documento a sinistra
var docHasEth = false; 

function updateActionMenu() {
    var rowHasExistingEth = false;

    // 1. Controlliamo se tra le righe SELEZIONATE ce n'è una con ETH
    $('.row-check:checked').each(function() {
        var rowData = $(this).closest('tr').data('info'); 
        // Verifichiamo sia il flag has_eth che la stringa x_eth
        if (rowData && (rowData.has_eth || (rowData.x_eth && rowData.x_eth !== ''))) {
            rowHasExistingEth = true;
        }
    });

    var btn = $('#btn-azioni-eth');
    
    // 2. LOGICA DI BLOCCO FERREA:
    // Se il documento ha l'ETH (testata già generata) 
    // OPPURE se almeno una riga selezionata ha già l'ETH
    if (docHasEth || rowHasExistingEth) {
        // TRASFORMA IN MODALITÀ RISTAMPA
        btn.removeClass('btn-dark').addClass('btn-warning text-dark font-weight-bold');
        btn.html('<i class="fas fa-print"></i> RISTAMPA ETICHETTE');
        
        $('.opt-generate').hide(); // Nasconde TOTALMENTE le 3 opzioni di generazione
        $('.opt-reprint').show();   // Mostra solo le opzioni di ristampa
    } else {
        // TORNA IN MODALITÀ GENERAZIONE
        btn.removeClass('btn-warning text-dark font-weight-bold').addClass('btn-dark');
        btn.html('<i class="fas fa-barcode"></i> AZIONI ETICHETTATURA');
        
        $('.opt-generate').show(); // Mostra le 3 opzioni di generazione
        $('.opt-reprint').hide();   // Nasconde le opzioni di ristampa
    }
}

// Modifica la funzione loadRighe per salvare i dati nella riga
function loadRighe(id) {
    $.get('$fetchRigheUrl', { id_dotes: id }, function(res) {
        var html = '';
        res.forEach(function(r) {
            // Salviamo l'oggetto riga come stringa JSON nell'attributo data per recuperarlo dopo
            var rowData = JSON.stringify(r);
            html += '<tr data-info=\'' + rowData + '\'>' +
                '<td><input type="checkbox" class="row-check" value="'+r.id_dorig+'"></td>' +
                '<td>'+r.cd_ar+'</td>' +
                '<td>'+r.descrizione+'</td>' +
                '<td class="text-center font-weight-bold">'+r.qta+'</td>' +
                '<td>'+(r.x_eth ? '<span class="badge badge-secondary">'+r.x_eth+'</span>' : '<small class="text-muted">-</small>')+'</td>' +
            '</tr>';
        });
        $('#table-righe tbody').html(html);
        updateActionMenu(); // Reset menu
    });
}

// Listener sui checkbox
$(document).on('change', '.row-check, #check-all-righe', function() {
    setTimeout(updateActionMenu, 50); // Piccolo delay per far aggiornare il DOM
});
JS;
$this->registerJs($js);
?>