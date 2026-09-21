// ============================================================
// SEGNALAZIONE ANOMALIA - Modale SweetAlert2 riusabile
// Disponibile globalmente in ogni pagina (incluso da main.php)
// ============================================================

// Rileva il nome della pagina corrente dal titolo o dall'URL
function detectCurrentPage() {
    var title = document.title || '';
    var path  = window.location.pathname + window.location.search;

    // Mappa route -> nome leggibile
    var map = {
        'r=planning/list':   'Planning - Registro Attività',
        'r=planning/index':  'Planning - Calendario',
        'r=planning/daily':  'Planning - Vista Giornaliera',
        'r=planning':        'Planning',
        'r=presenze':        'Presenze',
        'r=personale':       'Personale',
        'r=veicoli':         'Veicoli',
        'r=rapportini':      'Rapportini',
        'r=agenda':          'Agenda',
        'r=todomain':        'ToDo',
        'r=doc_head':        'Documenti',
        'r=docommessa':      'Commesse',
        'r=gacprv':          'GAC - Preventivi',
        'r=anacli':          'Clienti',
        'r=uecanagrafica':   'Anagrafica',
        'r=uecarticoli':     'Articoli',
        'r=uectesta':        'Testate',
        'r=allfiles':        'File',
        'r=report':          'Report',
        'r=statistiche':     'Statistiche',
        'r=user/index':      'Gestione Utenti',
        'r=user/update':     'Utente - Modifica',
        'r=site/index':      'Home',
        'r=site/login':      'Login',
        'r=two-factor':      'Sicurezza - 2FA'
    };

    // Cerca corrispondenza in base al percorso
    for (var key in map) {
        if (path.indexOf(key) !== -1) {
            return map[key];
        }
    }

    // Fallback: usa il titolo della pagina
    return title.replace(/\s*[-–|].*$/, '').trim() || path;
}

// Handler globale per il bottone nella navbar
$(document).on('click', '#btn-segnalazione-global', function() {
    openSegnalazione({
        pagina: detectCurrentPage(),
        url: window.location.href
    });
});

// Funzione core del modale
function openSegnalazione(opts) {
    opts = opts || {};
    var pagina   = opts.pagina   || detectCurrentPage();
    var url      = opts.url      || window.location.href;
    var recordId = opts.recordId || '';
    var datiExtra= opts.datiExtra|| '';

    var swalContent = document.createElement('div');
    swalContent.style.cssText = 'text-align:left; max-width:100%; box-sizing:border-box;';
    swalContent.innerHTML =
        '<div style="background:#fff3cd; border:1px solid #ffc107; border-radius:6px; padding:10px 14px; margin-bottom:14px; font-size:0.88rem;">' +
        '<i class="fa fa-exclamation-triangle" style="color:#856404;"></i> ' +
        '<b style="color:#856404;">Attenzione:</b> verranno allegati automaticamente dati tecnici (pagina, URL, browser, utente, sessione).' +
        '</div>' +
        '<label style="font-weight:600; display:block; margin-bottom:6px;">Cosa stavi facendo quando hai riscontrato l\'anomalia? *</label>' +
        '<textarea id="segna-descrizione" class="swal2-textarea" placeholder="Descrivi il problema o l\'anomalia riscontrata..."' +
        ' style="width:90%; box-sizing:border-box; max-height:200px; min-height:100px; border:1px solid #ced4da; border-radius:6px; padding:10px 14px; font-size:0.9rem; resize:vertical;"></textarea>' +
        '<div style="margin-top:10px; font-size:0.8rem; color:#6c757d;">' +
        '<i class="fa fa-info-circle"></i> <b>Pagina rilevata:</b> ' + pagina +
        '</div>';

    Swal.fire({
        title: '<i class="fa fa-bug" style="color:#dc3545;"></i> Segnalazione Anomalia',
        html: swalContent,
        width: '520px',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-paper-plane"></i> Invia Segnalazione',
        cancelButtonText: 'Annulla',
        focusConfirm: false,
        didOpen: function() {
            var ta = document.getElementById('segna-descrizione');
            if (ta) {
                ta.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 200) + 'px';
                });
            }
        },
        preConfirm: function() {
            var desc = document.getElementById('segna-descrizione').value.trim();
            if (!desc) {
                Swal.showValidationMessage('La descrizione è obbligatoria');
                return false;
            }
            return { descrizione: desc };
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Invio in corso...',
                text: 'Attendere prego',
                allowOutsideClick: false,
                didOpen: function() { Swal.showLoading(); }
            });

            var userAgent = navigator.userAgent || '';

            $.ajax({
                url: 'index.php?r=site/segnalazione',
                type: 'POST',
                data: {
                    descrizione: result.value.descrizione,
                    pagina: pagina,
                    url: url,
                    browser: userAgent,
                    record_id: recordId,
                    dati_extra: datiExtra,
                    _csrf: yii.getCsrfToken()
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Segnalazione Inviata!',
                            html: '<small>Grazie. Il supporto tecnico riceverà i tuoi dati e analizzerà il problema.</small>',
                            confirmButtonColor: '#198754'
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Errore', text: res.error || 'Invio non riuscito.' });
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Errore di Rete', text: 'Impossibile contattare il server. Riprova.' });
                }
            });
        }
    });
}
