<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\assets\DataTablesAsset;
use kartik\select2\Select2;
use yii\bootstrap5\Modal;
use kartik\mpdf\Pdf;

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

$personaleList = ArrayHelper::map(\app\models\Personale::find()->all(), 'id', function($model) {
    return $model->cognome . ' ' . $model->nome;
});
$clientiList = ArrayHelper::map(\app\models\CF::find()->all(), 'Cd_CF', 'Descrizione');
$ditteList = ArrayHelper::map(\app\models\DittaEsterna::find()->all(), 'codice', 'descrizione');

// 2. CSS Custom per forzare il layout
$this->registerCss("
    .dataTables_filter { text-align: left !important; float: left !important; }
    .dataTables_filter label { display: flex; align-items: center; gap: 10px; }
    .dt-buttons { float: right !important; margin-bottom: 15px; }
    
    /* Intestazione fissa */
    #planning-table thead th { 
        background-color: #f8f9fa !important; 
        color: #212529 !important; 
        vertical-align: middle; 
        white-space: nowrap; 
    }
    
    /* Stile per gli input in linea */
    .inline-edit { border: 1px solid #ced4da; background: #fff; transition: all 0.3s ease; border-radius: 4px; }
    .inline-edit:hover, .inline-edit:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); }

    /* Evidenziazione al salvataggio */
    .save-pending { border-color: #ffc107 !important; box-shadow: 0 0 5px #ffc107 !important; }
    .save-success { border-color: #198754 !important; box-shadow: 0 0 5px #198754 !important; }
    .save-pending + .select2-container .select2-selection { border-color: #ffc107 !important; box-shadow: 0 0 5px #ffc107 !important; }
    .save-success + .select2-container .select2-selection { border-color: #198754 !important; box-shadow: 0 0 5px #198754 !important; }

    /* FIX INCOLONNAMENTO SELECT2 MULTIPLO (Dipendenti) */
    .select2-selection--multiple .select2-selection__rendered {
        display: flex !important; flex-direction: column !important; padding: 4px !important; gap: 4px;
    }
    .select2-selection--multiple .select2-selection__choice {
        width: 100% !important; margin: 0 !important; white-space: normal !important; text-align: left;
    }
    .select2-selection--multiple .select2-search--inline { display: block !important; width: 100% !important; margin: 0 !important; }
    .select2-selection--multiple .select2-search__field {
        width: 100% !important; min-height: 28px !important; margin: 2px 0 0 0 !important; padding: 0 4px !important; color: #212529 !important; cursor: text !important;
    }
    
/* Sovrascrive qualsiasi stile di riga alterna o hover */
#planning-table tbody tr.highlight-row td {
    background-color: #fff3cd !important; /* Giallo chiaro */
    border-top: 2px solid #ffecb5 !important;
    border-bottom: 2px solid #ffecb5 !important;
    transition: background-color 0.5s ease;
}

/* Opzionale: un piccolo flash per attirare l'occhio */
.highlight-row {
    animation: pulse-yellow 2s infinite;
}

@keyframes pulse-yellow {
    0% { box-shadow: inset 0 0 0 1000px rgba(255, 243, 205, 1); }
    50% { box-shadow: inset 0 0 0 1000px rgba(255, 243, 205, 0.5); }
    100% { box-shadow: inset 0 0 0 1000px rgba(255, 243, 205, 1); }
}
    /* Impedisce agli input di uscire dalla cella */
#planning-table input[type=\"date\"],
#planning-table input[type=\"time\"] {
    width: 100% !important;
    padding: 2px 5px !important; /* Riduce il padding interno */
    font-size: 0.85rem !important; /* Testo leggermente più piccolo per farli stare */
}

/* Gestione dei due campi time affiancati */
.d-flex.gap-1 input[type=\"time\"] {
    min-width: 0; /* Permette la contrazione degli input in flexbox */
    flex: 1;      /* Divide lo spazio equamente */
}

/* Evita che la cella vada a capo */
#planning-table td:nth-child(2) {
    min-width: 160px !important;
    white-space: nowrap;
}
    #planning-table {
    width: 100% !important;
    min-width: 1100px; /* Impedisce alla tabella di rimpicciolirsi oltre il limite di leggibilità */
}

/* Forza i Select2 e gli input a prendersi tutto lo spazio della cella % */
.select2-container, .inline-edit {
    width: 100% !important;
}
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
            
            <?= Html::a('<i class="fa fa-arrow-left"></i> Torna al Calendario', ['index'], [
                'class' => 'btn btn-outline-secondary'
            ]) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="planning-table" class="table table-striped table-bordered align-middle" style="width:100%; table-layout: fixed;">
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
                <?php 
   $highlightId = Yii::$app->request->get('highlight_id'); // PHP cattura il 150!              ?>
       
 
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
    
    var table = $('#planning-table').DataTable({
        dom: '<"row align-items-center"<"col-md-6"f><"col-md-6 text-end"B>>rt<"row align-items-center mt-3"<"col-md-6"i><"col-md-6"p>>',
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json' },
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-secondary', text: '<i class="fa-solid fa-copy"> Copia </i>' },
            { extend: 'csv', className: 'btn btn-sm btn-info text-white', text: '<i class="fa-solid fa-file-csv"> CSV </i>' },
            { extend: 'excel', className: 'btn btn-sm btn-success', text: '<i class="fa-solid fa-file-excel"> Excel </i>' },
            { extend: 'pdfHtml5', className: 'btn btn-sm btn-danger', text: '<i class="fa-solid fa-file-pdf"> Pdf </i>', orientation: 'landscape', pageSize: 'A4' },
            { extend: 'print', className: 'btn btn-sm btn-primary', text: '<i class="fa-solid fa-print"> Stampa </i>' }
        ],
        pageLength: 25,
        scrollX: true,
        autoWidth: false,
        order: [[1, "desc"], [0, "asc"]],
        
columnDefs: [
    { targets: 0, width: "4%" },   // Giro (Molto piccolo)
    { targets: 1, width: "13%" },  // Data/Orari (Deve contenere i 3 input)
    { targets: 2, width: "11%" },  // Veicolo
    { targets: 3, width: "13%" },  // Cliente
    { targets: 4, width: "14%" },  // Dipendenti (Select2 multiplo ha bisogno di spazio)
    { targets: 5, width: "11%" },  // Ditta Esterna
    { targets: 6, width: "4%" },   // Opr. (Solo un numero)
    { targets: 7, width: "15%" },  // Luogo (Testo lungo)
    { targets: 8, width: "10%" },  // Stato
    { targets: 9, width: "5%", orderable: false } // Azioni (Icone piccole)
]
    });

    var tableContainer = document.querySelector('.table-responsive');
    if (tableContainer) {
        new ResizeObserver(function() { table.columns.adjust(); }).observe(tableContainer);
    }
    setTimeout(function() { table.columns.adjust().draw(); }, 200);

    // --- LOGICA MUTUA ESCLUSIONE CON KARTIK SELECT2 ---
    $(document).on('change', '.inline-edit', function(e) {
        var \$el = $(this);
        if(!\$el.data('field')) return; 

        var id = \$el.data('id');
        var field = \$el.data('field');
        var value = \$el.val();
 
        const Toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            showClass: { popup: 'animate__animated animate__fadeInUp' },
            hideClass: { popup: 'animate__animated animate__fadeOutDown' },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        var \$riga = \$el.closest('tr');
        var \$selectPersonale = \$riga.find('.select2-personale');
        var \$selectDitta = \$riga.find('.select2-ditta');

if (field === 'ditta_esterna') {
        // Se il valore esiste e non è vuoto, blocca personale
        if (value && value !== '') {
           \$selectPersonale.val(null).trigger('change').prop('disabled', true);
        } else {
            // Se svuotato (tramite allowClear), sblocca personale
            \$selectPersonale.prop('disabled', false);
        }
    }
    
    if (field === 'personale_ids') {
        // Personale è multiplo, controlliamo la lunghezza dell'array
        if (value && value.length > 0) {
            \$selectDitta.val(null).trigger('change').prop('disabled', true);
        } else {
            // Se svuotato, sblocca ditta
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

// Apertura Modale
$(document).on('click', '.show-modal', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');
    $('#modal-create').modal('show').find('#modalContent').load(url);
});

// INVIO FORM VIA AJAX
$(document).on('submit', '#planning-form-dynamic', function(e) {
    e.preventDefault();
    var \$form = $(this); 
    $.ajax({
        url: \$form.attr('action'), 
        type: 'post',
        data: \$form.serialize(), 
        success: function(res) {
            if(res.success) {
                $('#modal-create').modal('hide');
                location.reload(); 
            } else {
                Swal.fire({ icon: 'error', title: 'Attenzione', text: res.error });
            }
        }
    });
    return false;
});

/// --- AJAX DUPLICAZIONE FLUIDA (CORRETTO PER DATATABLES) ---
$(document).on('click', '.btn-duplicate-ajax', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');

    Swal.fire({
        title: 'Duplicare questa attività?',
        text: "Verrà creata una copia identica nel registro.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0dcaf0',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-copy"></i> Sì, duplica',
        cancelButtonText: 'Annulla'
    }).then((result) => {
        if (result.isConfirmed) {
            
            $.post(url, { _csrf: yii.getCsrfToken() }, function(res) {
                if (res.success) {
                    
                    var dt = $('#planning-table').DataTable();
                    
                    // 1. Creiamo un contenitore "fantasma" per ingannare il browser 
                    // e impedirgli di distruggere il tag <tr>
                    var wrapper = document.createElement('div');
                    wrapper.innerHTML = '<table><tbody>' + res.html + '</tbody></table>';
                    
                    // 2. Estraiamo il TR intatto
                    var trNode = wrapper.querySelector('tr');
                    
                    if (trNode) {
                        // 3. Aggiungiamo la riga a DataTables (aggiornando la tabella)
                        var rowNode = dt.row.add(trNode).draw(false).node();
                        
                        // 4. Forziamo l'evidenziazione gialla sulla nuova riga
                        $(rowNode).addClass('highlight-row');
                       // setTimeout(function() {
                       //     $(rowNode).removeClass('highlight-row');
                       // }, 3000);

                        // 5. Estraiamo SOLO gli script "inline" per far funzionare le tendine Select2.
                        // (Ignoriamo quelli con il .src per evitare di ricaricare jQuery e rompere la pagina)
                        var scripts = wrapper.querySelectorAll('script');
                        scripts.forEach(function(s) {
                            if (!s.src) { 
                                var script = document.createElement('script');
                                script.textContent = s.textContent;
                                document.body.appendChild(script);
                            }
                        });

                        // 6. Messaggio di successo
                        Swal.fire({
                            toast: true, position: 'top', icon: 'success', 
                            title: 'Attività Duplicata!', showConfirmButton: false,
                             timer: 1500
                        });
                        
                    } else {
                        Swal.fire('Errore', 'Impossibile inserire la riga nella tabella.', 'error');
                    }

                } else {
                    Swal.fire('Errore', res.error || 'Impossibile duplicare il record.', 'error');
                }
            }).fail(function() {
                Swal.fire('Errore di Rete', 'La sessione potrebbe essere scaduta o il server non risponde.', 'error');
            });
        }
    });
});
JS;
$this->registerJs($js);
?>

<?php
// Guscio della Modale Bootstrap 5
Modal::begin([
    'title' => '<h4 class="m-0 text-primary"><i class="fa fa-calendar-plus"></i> Nuova Attività</h4>',
    'id' => 'modal-create',
    'size' => 'modal-xl', 
    'options' => ['tabindex' => false], 
]);
echo '<div id="modalContent"><div class="text-center my-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Caricamento in corso...</p></div></div>';
Modal::end();
?>