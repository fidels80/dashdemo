<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Personale;

$this->title = '';
$today = date('Y-m-d');

// --- 1. ASSETS ---
use app\assets\DataTablesAsset;

DataTablesAsset::register($this);
// 3. CSS Custom per uniformare lo stile (Header chiaro, bottoni e allineamento)

// --- 2. CSS CUSTOM ---
$this->registerCss("
    .fc-daygrid-event, .fc-timegrid-event { display: block !important; padding: 3px 6px !important; border-radius: 4px !important; margin-top: 2px !important; color: white !important; border: none !important; box-shadow: 0 1px 3px rgba(0,0,0,0.15); }
    .fc-daygrid-event-dot { display: none !important; }
    .fc-event { cursor: pointer; font-weight: 500; font-size: 0.85rem; }
    
    .calendar-container { background: #ffffff; padding: 20px; border-radius: 0 0 12px 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #dee2e6; border-top: none; min-height: 700px; }
    
    .legenda-box { display: flex; flex-wrap: wrap; gap: 12px; background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #e9ecef; align-items: center;}
    .legend-item { display: flex; align-items: center; font-size: 0.9rem; font-weight: 600; color: #444; }
    .dot { height: 14px; width: 14px; border-radius: 4px; display: inline-block; margin-right: 6px; }
    
    .tippy-box { background-color: #2c3e50; color: white; padding: 10px; border-radius: 6px; font-size: 0.9rem; }
    
    /* Stile Tabs */
    .nav-tabs .nav-link { font-size: 1.1rem; font-weight: 600; padding: 12px 25px; color: #495057; border: 1px solid transparent; }
    .nav-tabs .nav-link.active { color: #0d6efd; border-color: #dee2e6 #dee2e6 #fff; border-top: 3px solid #0d6efd; background-color: #fff; }
    .tab-content { background: #fff; }


    /* Colora il tasto Agenda Oggi per renderlo più visibile */
    .fc-agendaOggi-button, .fc-agendaOggiPresenze-button { 
        background-color: #6c757d !important; 
        border-color: #6c757d !important; 
        color: white !important; 
        font-weight: bold !important;
    }
    .fc-agendaOggi-button:hover, .fc-agendaOggiPresenze-button:hover { 
        background-color: #5a6268 !important; 
    }
");

// Lista per le tendine
$listaPersonale = ArrayHelper::map(Personale::find()->where(['stato_attivo' => 1])->orderBy('cognome')->all(), 'id', function ($p) {
    return $p->cognome . ' ' . $p->nome;
});
?>

<div class="planning-dashboard">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="m-0 text-dark"><i class="fa fa-th-large text-primary"></i> <?= Html::encode('Cruscotto Operativo (Planning & Presenze)') ?></h2>
        </div>
        <div>
            <?php  
            /*Html::a('<i class="fa fa-list"></i> Vista Lista', ['list'], ['class' => 'btn btn-outline-primary'])
            */
             ?>
        </div>
    </div>

    <ul class="nav nav-tabs" id="calendarsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="planning-tab" data-toggle="tab" data-bs-toggle="tab" href="#tab-planning" role="tab">
                <i class="fa fa-truck text-primary"></i> Planning Flotta
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="presenze-tab" data-toggle="tab" data-bs-toggle="tab" href="#tab-presenze" role="tab">
                <i class="fa fa-user-clock text-success"></i> Calendario Presenze
            </a>
        </li>
    </ul>

    <div class="tab-content" id="calendarsTabContent">

        <div class="tab-pane fade show active" id="tab-planning" role="tabpanel">
            <div class="calendar-container">


             <div id="veicoli-legend" class="legenda-box">
<?= Html::button('<i class="fa fa-plus"></i> Nuova Attività', [
    'class' => 'btn btn-success text-white shadow-sm show-modal',
    'data-url' => Url::to(['create']), // L'URL è salvato qui
    'title' => 'Crea Nuova Attività'
]) ?>
  <?= Html::a('<i class="fa-solid fa-list"></i> 
  Lista  Attività', ['list'], ['class' => 'btn btn-info text-white shadow-sm']) ?>

  
<?php /*Html::button('<i class="fa fa-clone"></i> Copia Giornata', [
                'id' => 'btn-open-copy-modal', // <-- Aggiunto ID
                'class' => 'btn btn-warning text-dark fw-bold',
                'title' => 'Copia un intero giorno in un\'altra data'
            ])*/ ?>
<div class="d-flex align-items-center gap-3">
            <?php $form = \yii\widgets\ActiveForm::begin([
                'action' => ['stampa'],
                'method' => 'get',
                'options' => [
                    'target' => '_blank', // Apre in una nuova pagina
                    'class' => 'd-flex align-items-center gap-2'
                ]
            ]); ?>
            
            <span class="text-muted small">Data Stampa:</span>
                <input type="date" name="data" class="form-control form-control-sm" 
                       value="<?= date('d-m-Y') ?>" style="width: 150px;"
                       onkeydown="return false;" onpaste="return false;" style="cursor: default;">
                <button type="submit" class="btn btn-dark shadow-sm">
                    <i class="fa-solid fa-print"></i> Stampa
                </button>
            <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
                    <div class="w-100 mb-2"><small class="text-uppercase text-muted fw-bold"><i class="fa fa-car"></i> Legenda Colori Mezzi:</small></div>
                </div>
                <div id="calendar-planning"></div>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-presenze" role="tabpanel">
            <div class="calendar-container">
                <div class="row align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="fw-bold text-muted mb-1"><i class="fa fa-filter"></i> Filtra Presenze per Dipendente:</label>
                        <?= Html::dropDownList('filtro_presenze', null, $listaPersonale, ['id' => 'filtro-presenze', 'class' => 'form-control select2-init', 'prompt' => '']) ?>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-secondary w-100" id="btn-reset-presenze"><i class="fa fa-times"></i> Rimuovi</button>
                    </div>
                    <div class="col-md-6 text-end">
                        <?= Html::a('<i class="fa fa-external-link-alt"></i> Crea Presenza', ['presenze/create'], ['class' => 'btn btn-outline-success']) ?>
                    </div>
                </div>

                <div class="legenda-box mb-3">
                    <small class="text-uppercase text-muted fw-bold me-3"><i class="fa fa-info-circle"></i> Legenda Presenze:</small>
                    <span class="badge bg-success fs-6"><i class="fa fa-check"></i> Lavoro</span>
                    <span class="badge bg-warning text-dark fs-6"><i class="fa fa-umbrella-beach"></i> Ferie / Permesso</span>
                    <span class="badge bg-danger fs-6"><i class="fa fa-briefcase-medical"></i> Malattia / Infortunio</span>
                </div>
                <div id="calendar-presenze"></div>
            </div>
        </div>

    </div>
</div>
<?php
 
$urlPlanning = Url::to(['planning/eventsjson']);
$urlPresenze = Url::to(['planning/presenzejson']);
$urlCreate = Url::to(['planning/create']);
$urlCreatePresenze = Url::to(['presenze/create']);
$this->registerJs("
    // 1. Inizializza Select2 per i filtri
    $('.select2-init').select2({ theme: 'bootstrap-5', width: '100%', placeholder: 'Tutti i dipendenti...', allowClear: true });

    var calPlanningEl = document.getElementById('calendar-planning');
    var calPresenzeEl = document.getElementById('calendar-presenze');
    var calPlanning, calPresenze;

    // Timer per gestire il doppio click sugli spazi vuoti
    var planningClickTimer = null;
    var presenzeClickTimer = null;

    // --- SETUP CALENDARIO 1: PLANNING ---
    if (calPlanningEl) {
        calPlanning = new FullCalendar.Calendar(calPlanningEl, {
            locale: 'it', 
            initialView: 'dayGridMonth', 
            initialDate: '$today', 
            eventDisplay: 'block', 
            displayEventTime: false,
            firstDay: 1, 
            height: 750,
            customButtons: {
                agendaOggi: {
                    text: 'Giornaliero',
                    click: function() {
                        calPlanning.changeView('listDay'); // Passa alla vista lista giornaliera
                        calPlanning.today();               // Si posiziona su oggi
                    }
                }
            },
            headerToolbar: { 
                left: 'prev,next today agendaOggi', 
                center: 'title', 
                right: 'dayGridMonth,timeGridWeek,listWeek' 
            },
            buttonText: { 
                today: 'Oggi', month: 'Mese', week: 'Settimana', list: 'Lista'
            },
// GESTIONE DOPPIO CLICK SPAZIO VUOTO (PLANNING)
dateClick: function(info) {
    if (planningClickTimer === null) {
        planningClickTimer = setTimeout(function() { planningClickTimer = null; }, 300);
    } else {
        clearTimeout(planningClickTimer);
        planningClickTimer = null;
        
        var targetUrl = '$urlCreate' + (('$urlCreate'.indexOf('?') !== -1) ? '&' : '?') + 'data_attivita=' + info.dateStr;
        
        $('#modal-create').modal('show');
        // Ho messo l'HTML tutto su una riga qui sotto
        $('#modalContent').html('<div class=\"text-center p-5\"><div class=\"spinner-border text-primary\"></div></div>').load(targetUrl);
    }
},
events: function(fetchInfo, successCallback, failureCallback) {
    $.ajax({
        url: '$urlPlanning', 
        type: 'GET', 
        dataType: 'json',
        data: { 
            start: fetchInfo.startStr, 
            end: fetchInfo.endStr, 
            personale_id: $('#filtro-planning').val() 
        },
        success: function(res) { 
            var legend = $('#veicoli-legend');
            legend.find('.legend-item').remove();
            var veicoli = {};
            
            res.forEach(function(e) { 
                if(e.extendedProps && e.extendedProps.targa && e.extendedProps.targa !== 'N/D') 
                    veicoli[e.extendedProps.targa] = e.color || e.backgroundColor; 
            });
            
            Object.keys(veicoli).forEach(function(t) {
                // Anche qui, l'HTML deve stare su una riga sola
                legend.append('<span class=\"legend-item\"><span class=\"dot\" style=\"background-color:'+veicoli[t]+'\"></span>'+t+'</span>');
            });
            successCallback(res); 
        }
    });
},
eventDidMount: function(info) {
                var props = info.event.extendedProps;
                var content = '<div style=\"text-align:left;\">' +
                    '<strong><i class=\"fa fa-building\"></i> Cliente:</strong> ' + (props.cliente || 'N.D.') + '<br>' +
                    '<strong><i class=\"fa fa-user\"></i> Personale:</strong> ' + (props.personale || 'N.D.') + '<br>' +
                    '<strong><i class=\"fa fa-truck\"></i> Veicolo:</strong> ' + (props.targa || 'N.D.') + '<br>' +
                    '<hr style=\"margin:5px 0; border-top:1px solid rgba(255,255,255,0.3)\">' +
                    '<strong>Attività:</strong> ' + info.event.title + '<br>' +
                    '<strong>Luogo:</strong> ' + (props.indirizzo || '-') + '</div>';

                tippy(info.el, { content: content, allowHTML: true, theme: 'material' });
            },
            eventClick: function(info) {
                window.location.href = '" . Url::to(['planning/view']) . "&id=' + info.event.id;
            }
        });
        calPlanning.render();
        
        // Eventi Filtro Planning
        $('#filtro-planning').on('change', function() { calPlanning.refetchEvents(); });
        $('#btn-reset-planning').on('click', function() { $('#filtro-planning').val(null).trigger('change'); });
    }

    // --- SETUP CALENDARIO 2: PRESENZE ---
    if (calPresenzeEl) {
        calPresenze = new FullCalendar.Calendar(calPresenzeEl, {
            locale: 'it', 
            initialView: 'dayGridMonth', 
            initialDate: '$today', 
            eventDisplay: 'block', 
            allDayText: 'Giornata Intera', // <--- AGGIUNGI QUESTA RIGA
            firstDay: 1, 
            height: 750,
            customButtons: {
                agendaOggiPresenze: {
                    text: 'Giornaliero',
                    click: function() {
                        calPresenze.changeView('listDay');
                        calPresenze.today();
                    }
                }
            },
            headerToolbar: { 
                left: 'prev,next today agendaOggiPresenze', 
                center: 'title', 
                right: 'dayGridMonth,timeGridWeek,listWeek' 
            },
            buttonText: { 
                today: 'Oggi', month: 'Mese', week: 'Settimana', list: 'Lista'
            },
// GESTIONE DOPPIO CLICK SPAZIO VUOTO (PRESENZE)
            dateClick: function(info) {
                if (presenzeClickTimer === null) {
                    presenzeClickTimer = setTimeout(function() {
                        presenzeClickTimer = null;
                    }, 300);
                } else {
                    clearTimeout(presenzeClickTimer);
                    presenzeClickTimer = null;
                    
                    var personaleId = $('#filtro-presenze').val();
                    var baseUrl = '" . Url::to(['presenze/create']) . "';
                    var separator = baseUrl.indexOf('?') !== -1 ? '&' : '?';
                    
                    var targetUrl = baseUrl + separator + 'data_presenza=' + info.dateStr;
                    
                    if (personaleId) {
                        targetUrl += '&personale_id=' + personaleId;
                    }
                }
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                $.ajax({
                    url: '$urlPresenze', type: 'GET', dataType: 'json',
                    data: { start: fetchInfo.startStr, end: fetchInfo.endStr, 
                    personale_id: $('#filtro-presenze').val() },
                    success: function(res) { successCallback(res); }
                });
            },
            eventDidMount: function(info) {
                tippy(info.el, { content: info.event.extendedProps.dettaglio, 
                allowHTML: true, theme: 'material' });
            },
            eventClick: function(info) {
                window.location.href = '" . Url::to(['presenze/view']) . "&id=' + info.event.extendedProps.pre_id;;
            }
        });
        // Rendering iniziale anche se nascosto
        calPresenze.render();

        // Eventi Filtro Presenze
        $('#filtro-presenze').on('change', function() { calPresenze.refetchEvents(); });
        $('#btn-reset-presenze').on('click', function() { $('#filtro-presenze').val(null).trigger('change'); });
    }

// --- FIX PER LE TABS (Universale Bootstrap 4 e 5) ---
    $('a[data-toggle=\"tab\"], a[data-bs-toggle=\"tab\"], button[data-bs-toggle=\"tab\"]').on('shown.bs.tab', function (e) {
        var targetId = $(e.target).attr('id');
        
        if (targetId === 'presenze-tab' && calPresenze) {
            calPresenze.render();
            window.dispatchEvent(new Event('resize'));
        }
        if (targetId === 'planning-tab' && calPlanning) {
            calPlanning.render();
            window.dispatchEvent(new Event('resize'));
        }
    });
");
?>



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
                <input type="date" class="form-control" id="copy-source-date" required onkeydown="return false;" onpaste="return false;" style="cursor: default;">
                <small class="text-muted">Seleziona il giorno da cui copiare le attività.</small>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold text-success">A: (Data di Destinazione)</label>
                <input type="date" class="form-control" id="copy-target-date" required onkeydown="return false;" onpaste="return false;" style="cursor: default;">
                <small class="text-muted">I record verranno ricreati in questa data con stato "Da Iniziare".</small>
            </div>
        </form>
      </div>
      <div class="modal-footer d-flex justify-content-between">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-warning text-dark fw-bold" id="btn-esegui-copia">
            <i class="fa fa-play"></i> Esegui Copia
        </button>
      </div>
    </div>
  </div>
</div>


<?php
// --- 1. AGGIUNGI QUESTO GUSCIO HTML ---
\yii\bootstrap5\Modal::begin([
    'title' => '<h4 class="m-0 text-primary"><i class="fa fa-calendar-plus"></i> Nuova Attività</h4>',
    'id' => 'modal-create',
    'size' => 'modal-xl',
    'options' => ['tabindex' => false],
]);
echo '<div id="modalContent"><div class="text-center my-5"><div class="spinner-border text-primary"></div><p>Caricamento...</p></div></div>';
\yii\bootstrap5\Modal::end();
?>


<script>
// --- 1. APERTURA MODALE CREAZIONE ---
$(document).on('click', '.show-modal', function(e) {
    e.preventDefault();
    var url = $(this).data('url');
    
    $('#modalContent').html('<div class="text-center my-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Caricamento...</p></div>');
    
    // QUESTO FUNZIONA CON QUALSIASI VERSIONE DI BOOTSTRAP (3, 4 o 5)
    // Non usa il metodo nativo che ti sta dando errore
    $('#modal-create').modal('show'); 
    
    $('#modalContent').load(url);
});

// --- 2. SALVATAGGIO FORM ---
$(document).on('submit', '#planning-form-dynamic', function(e) {
    e.preventDefault();
    var $form = $(this); 
    $.ajax({
        url: $form.attr('action'), type: 'post', data: $form.serialize(), 
        success: function(res) {
            if(res.success) {
                // CHIUSURA UNIVERSALE (Funziona anche se Bootstrap 5 è rotto)
                $('#modal-create').modal('hide');
                
                if (typeof calPlanning !== 'undefined') {
                    calPlanning.refetchEvents();
                } else {
                    location.reload(); 
                }
            } else { 
                Swal.fire({ icon: 'error', title: 'Attenzione', text: res.error }); 
            }
        }
    });
});

    </script>

