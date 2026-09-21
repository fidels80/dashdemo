<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\assets\DataTablesAsset;
use kartik\select2\Select2;
use yii\bootstrap5\Modal;
use kartik\mpdf\Pdf;

//$this->title = 'null';
$this->title = '';
$this->params['breadcrumbs'] = [];
// 1. Registriamo l'AssetBundle
DataTablesAsset::register($this);

// --- PREPARAZIONE DATI ---

// Filtro Veicoli: Solo disponibili + Visualizzazione "Targa - Marca/Modello"
$veicoliList = ArrayHelper::map(
    \app\models\Veicoli::find()
        // Usiamo UPPER per sicurezza contro differenze tra 'Disponibile', 'DISPONIBILE', 'disponibile'
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
        /* --- ANIMAZIONE RIGA DUPLICATA (Vince su DataTables) --- */
    @keyframes fadeHighlight {
        0% { background-color: #ffe680 !important; } /* Giallo acceso */
        100% { background-color: transparent !important; } /* Torna normale */
    }
    tr.highlight-row > td {
        animation: fadeHighlight 3s ease-out forwards !important;
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
                    <th></th> </tr>
            </thead>
            <tbody>
                <?php 
    // Controlliamo se nell'URL c'è l'ID appena duplicato
    $highlightId = Yii::$app->request->get('highlight_id'); 
?>
                <?php foreach ($models as $m): ?>
                    <?php 
                        $selectedPersonale = ArrayHelper::getColumn($m->personali, 'id');
                        $lockDipendenti = !empty($m->ditta_esterna);
                        $lockDitta = !empty($selectedPersonale);
                     // Mettiamo SOLO la classe highlight-row (i colori li gestisce il CSS ora)
        $rowClass = ($m->id == $highlightId) ? 'highlight-row' : ''; ?>
           <tr data-id="<?= $m->id ?>" class="<?= $rowClass ?>">
                        <td><?= \kartik\select2\Select2::widget([
    'name' => 'giro_' . $m->id,
    'value' => $m->giro,
    'data' => array_combine(range(1, 24), range(1, 24)),
    'options' => [
        'id' => 'giro_' . $m->id, 
        'placeholder' => '-',
        'class' => 'inline-edit select2-giro', // Classe specifica per il JS
        'data-id' => $m->id,
        'data-field' => 'giro'
    ],
    'pluginOptions' => [
        'allowClear' => true, 
        'width' => '85px',
        'dropdownAutoWidth' => true,
    ]
]) ?></td>
                        <td data-sort="<?= $m->data_attivita ?>" 
                        data-search="<?= Yii::$app->formatter->asDate($m->data_attivita,
                         'php:d/m/Y') ?>">
                            <input type="date" class="form-control form-control-sm inline-edit mb-1 w-100" 
                                   data-id="<?= $m->id ?>" data-field="data_attivita" value="<?= $m->data_attivita ?>">
                            <div class="d-flex gap-1">
                                <input type="time" class="form-control form-control-sm inline-edit w-50 px-1" 
                                       data-id="<?= $m->id ?>" data-field="ora_inizio" value="<?= substr($m->ora_inizio, 0, 5) ?>">
                                <input type="time" class="form-control form-control-sm inline-edit w-50 px-1" 
                                       data-id="<?= $m->id ?>" data-field="ora_fine" value="<?= substr($m->ora_fine, 0, 5) ?>">
                            </div>
                        </td>

                        <td>
                            <?= Select2::widget([
                                'name' => 'veicolo_id_' . $m->id,
                                'value' => $m->veicolo_id,
                                'data' => $veicoliList,
                                'options' => [
                                    'id' => 'veicolo_' . $m->id, 
                                    'placeholder' => 'No Mezzo',
                                    'class' => 'inline-edit select2-veicolo',
                                    'data-id' => $m->id,
                                    'data-field' => 'veicolo_id'
                                ],
                                'pluginOptions' => ['allowClear' => true, 'width' => '100%']
                            ]) ?>
                        </td>

                        <td>
                            <?= Select2::widget([
                                'name' => 'cd_cf_' . $m->id,
                                'value' => $m->cd_cf,
                                'data' => $clientiList,
                                'options' => [
                                    'id' => 'cliente_' . $m->id,
                                    'placeholder' => 'Nessun Cliente',
                                    'class' => 'inline-edit select2-cliente',
                                    'data-id' => $m->id,
                                    'data-field' => 'cd_cf'
                                ],
                                'pluginOptions' => ['allowClear' => true, 'width' => '100%']
                            ]) ?>
                        </td>

                        <td>
                            <?= Select2::widget([
                                'name' => 'personale_ids_' . $m->id,
                                'value' => $selectedPersonale,
                                'data' => $personaleList,
                                'options' => [
                                    'id' => 'personale_' . $m->id,
                                    'placeholder' => 'Seleziona...',
                                    'class' => 'inline-edit select2-personale',
                                    'multiple' => true,
                                    'data-id' => $m->id,
                                    'data-field' => 'personale_ids',
                                    'disabled' => $lockDipendenti
                                ],
                                'pluginOptions' => ['allowClear' => true, 'width' => '100%']
                            ]) ?>
                        </td>

                        <td>
                            <?= Select2::widget([
                                'name' => 'ditta_esterna_' . $m->id,
                                'value' => $m->ditta_esterna,
                                'data' => $ditteList,
                                'options' => [
                                    'id' => 'ditta_' . $m->id,
                                    'placeholder' => 'Nessuna Ditta',
                                    'class' => 'inline-edit select2-ditta',
                                    'data-id' => $m->id,
                                    'data-field' => 'ditta_esterna',
                                    'disabled' => $lockDitta
                                ],
                                'pluginOptions' => ['allowClear' => true, 'width' => '100%']
                            ]) ?>
                        </td>

                        <td class="text-center">
                            <input type="number" class="form-control form-control-sm inline-edit text-center p-1" 
                                   style="width: 100%; min-width: 40px;" min="0" 
                                   data-id="<?= $m->id ?>" data-field="qta_operai" value="<?= $m->qta_operai ?>">
                        </td>

                        <td>
                            <input type="text" class="form-control form-control-sm inline-edit w-100" 
                                   data-id="<?= $m->id ?>" data-field="indirizzo" value="<?= Html::encode($m->indirizzo) ?>" placeholder="...">
                        </td>

                        <td>
                            <?= Select2::widget([
                                'name' => 'stato_completamento_' . $m->id,
                                'value' => $m->stato_completamento,
                                'data' => ['In Corso' => 'In Corso', 'Completato' => 'Completato', 'Da Iniziare' => 'Da Iniziare', 'Annullato' => 'Annullato'],
                                'options' => [
                                    'id' => 'stato_' . $m->id,
                                    'class' => 'inline-edit fw-bold',
                                    'data-id' => $m->id,
                                    'data-field' => 'stato_completamento'
                                ],
                                'pluginOptions' => ['minimumResultsForSearch' => -1, 'width' => '100%']
                            ]) ?>
                        </td>
<td class="text-center p-1 text-nowrap">
<?= Html::a('<i class="fa fa-copy"></i>', ['duplicate', 'id' => $m->id], [
    'class' => 'btn btn-sm btn-outline-info border-0 p-1 me-1 btn-duplicate-ajax', 
    'title' => 'Duplica',
]) ?>
    
    <?= Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $m->id], [
        'class' => 'btn btn-sm btn-outline-danger border-0 p-1', 
        'title' => 'Elimina',
        'data-method' => 'post', 
        'data-confirm' => 'Sei sicuro di voler eliminare questa attività?'
    ]) ?>
</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>






<?php



$updateUrl = Url::to(['planning/inline-update']);

$js = <<<JS
$(document).ready(function() {
    
    // Inizializzazione DataTables con controllo rigoroso delle larghezze
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
        autoWidth: false, // IMPEDISCE ALLA TABELLA DI ALLARGARSI DA SOLA
    order: [[1, "desc"], [0, "asc"]], // Ordina prima per data_attivita desc, poi per giro asc
        
        // FORZIAMO LE LARGHEZZE FISSE DELLE COLONNE
        columnDefs: [
            { targets: 0, width: "40px" }, // giro
            { targets: 1, width: "70px" }, // date
            { targets: 2, width: "100px" }, // Veicolo
            { targets: 3, width: "100px" }, // Clienti
            { targets: 4, width: "100px" }, // Dipendenti
            { targets: 5, width: "100px" }, // D\itta esterna
            { targets: 6, width: "45px", className: "text-center" }, // Operatori
            { targets: 7, width: "120px" }, // luogo
            { targets: 8, width: "50px" }, // sato
            { targets: 9, width: "30px", orderable: false, 
            searchable: false, className: "text-center" } // Azioni (Strettissimo!)
       
        ]
    });

    // Fix Ricalcolo Dimensioni
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
        

// Configurazione del Toast di SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
       position: 'top', // <--- CAMBIATO DA 'top-end' A 'bottom-end'
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        // --- VA INSERITO QUI ---
            showClass: {
                popup: 'animate__animated animate__fadeInUp'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutDown'
            },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
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
 
        // Effetto grafico "Salvataggio"
        \$el.removeClass('save-success').addClass('save-pending');

        $.ajax({
            url: '{$updateUrl}',
            type: 'POST',
            data: { id: id, field: field, value: value, _csrf: yii.getCsrfToken() },
success: function(res) {
            if(res.success) {
                // Notifica di successo (Toast)
                Toast.fire({
                    icon: 'success',
                    title: 'Dato salvato con successo'
                });

                \$el.removeClass('save-pending').addClass('save-success');
                setTimeout(function(){ \$el.removeClass('save-success'); }, 2000);
            } else {
                \$el.removeClass('save-pending');
                Swal.fire({
                    icon: 'error',
                    title: 'Errore di Salvataggio',
                    text: res.error
                });
            }
        },
error: function() {
            \$el.removeClass('save-pending');
            Toast.fire({
                icon: 'error',
                title: 'Errore di rete o sessione scaduta'
            });
        }
        });
    });

});


// 1. Apertura Modale
    $(document).on('click', '.show-modal', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        $('#modal-create').modal('show').find('#modalContent').load(url);
    });

    // 2. INVIO FORM VIA AJAX (Gestione "Salva" dentro la modale)
// 2. INVIO FORM VIA AJAX (Gestione "Salva" dentro la modale)
    $(document).on('submit', '#planning-form-dynamic', function(e) {
        e.preventDefault();
        
        // AGGIUNTO IL BACKSLASH (\) QUI:
        var \$form = $(this); 
        
        $.ajax({
            // AGGIUNTO IL BACKSLASH QUI:
            url: \$form.attr('action'), 
            type: 'post',
            // AGGIUNTO IL BACKSLASH QUI:
            data: \$form.serialize(), 
            success: function(res) {
                if(res.success) {
                    $('#modal-create').modal('hide');
                    // Ricarichiamo la pagina per vedere le modifiche
                    location.reload(); 
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Attenzione',
                        text: res.error
                    });
                }
            }
        });
        return false;
    });


    // --- AJAX DUPLICAZIONE CON SWEETALERT ---
$(document).on('click', '.btn-duplicate-ajax', function(e) {
    e.preventDefault();
    var url = $(this).attr('href');

    Swal.fire({
        title: 'Duplicare questa attività?',
        text: "Verrà creata una copia nel registro in stato 'Da Iniziare'.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0dcaf0',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-copy"></i> Sì, duplica',
        cancelButtonText: 'Annulla'
    }).then((result) => {
        if (result.isConfirmed) {
            // Chiamata AJAX
            $.post(url, function(res) {
                if (res.success) {
                    // Ricarichiamo la pagina passando l'ID della nuova riga
                    var currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('highlight_id', res.id);
                    window.location.href = currentUrl.toString();
                } else {
                    Swal.fire('Errore', 'Impossibile duplicare il record.', 'error');
                }
            });
        }
    });
});

// Fai sfumare il colore della riga evidenziata dopo 3 secondi
//setTimeout(function() {
//    $('.highlight-row').css('transition', 'background-color 2s ease')
//                       .removeClass('bg-warning bg-opacity-25 highlight-row');
//}, 3000);
JS;
$this->registerJs($js);
?>


<?php
// Guscio della Modale Bootstrap 5
Modal::begin([
    'title' => '<h4 class="m-0 text-primary"><i class="fa fa-calendar-plus"></i> Nuova Attività</h4>',
    'id' => 'modal-create',
    'size' => 'modal-xl', // Extra large per far stare comode le 3 colonne
    'options' => ['tabindex' => false], // IMPORTANTE: Risolve il bug di Select2 dentro le modali
]);
echo '<div id="modalContent"><div class="text-center my-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Caricamento in corso...</p></div></div>';
Modal::end();
?>