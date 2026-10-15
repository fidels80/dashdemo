<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $fatture array */
/* @var $fornitori array */
/* @var $filtri array */

$this->title = 'Fatture fornitori';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('https://unpkg.com/tippy.js@6', ['position' => \yii\web\View::POS_HEAD]);

$fmt = function ($v) { return number_format((float) $v, 2, ',', '.'); };

$reclamiSummary = [];
foreach ($fatture as $row) {
    if (!empty($row['reclami'])) {
        $reclamiSummary[(int) $row['model']->id] = $row['reclami'];
    }
}
$reclamiJson = json_encode($reclamiSummary, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
<div class="fatturafornitore-index">

    <div class="card p-3 shadow-sm mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-1"><i class="fas fa-file-invoice"></i> Fatture fornitori</h4>
                <div class="text-muted small">
                    Elenco delle fatture ricevute dai fornitori. Per ogni fattura puoi
                    <strong>bloccare le scadenze</strong> oppure <strong>inviare un reclamo</strong> al fornitore.
                </div>
            </div>
        </div>
    </div>

    <div class="card p-3 shadow-sm mb-3">
        <form method="get" class="form-row align-items-end">
            <div class="form-group col-md-4 mb-2">
                <label class="small mb-1">Fornitore</label>
                <?= Html::dropDownList('id_anagrafica', $filtri['id_anagrafica'], $fornitori, [
                    'class' => 'form-control',
                    'prompt' => 'Tutti i fornitori',
                ]) ?>
            </div>
            <div class="form-group col-md-4 mb-2">
                <label class="small mb-1">Cerca</label>
                <input type="text" name="q" class="form-control" value="<?= Html::encode($filtri['q']) ?>"
                       placeholder="Numero, data o descrizione...">
            </div>
            <div class="form-group col-md-2 mb-2">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" name="solo_bloccate" value="1"
                           id="filtro-solo-bloccate" <?= $filtri['solo_bloccate'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="filtro-solo-bloccate">Solo scadenze bloccate</label>
                </div>
            </div>
            <div class="form-group col-md-2 mb-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtra</button>
                <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">Azzera</a>
            </div>
        </form>
    </div>

    <div class="card p-3 shadow-sm">
        <table id="ff-index-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr>
                <th>Documento</th>
                <th>Data</th>
                <th>Fornitore</th>
                <th>Descrizione</th>
                <th class="text-right">Importo</th>
                <th class="text-right">Righe</th>
                <th class="text-right">Pezzi</th>
                <th>Scadenze</th>
                <th>Blocco</th>
                <th class="text-center">Reclami</th>
                <th class="text-center text-nowrap no-export">Azioni</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($fatture as $row): ?>
                <?php
                $d = $row['model'];
                $dataDoc = $d->data ? date('d/m/Y', strtotime($d->data)) : '';
                $oggetto = trim('Reclamo per vostro documento ' . $d->numero . ' ' . $dataDoc);
                ?>
                <tr>
                    <td><?= Html::encode($d->etichetta) ?></td>
                    <td data-order="<?= Html::encode($d->data) ?>"><?= $dataDoc ?></td>
                    <td><?= Html::encode($row['fornitore']) ?></td>
                    <td><?= Html::encode($d->descrizione) ?></td>
                    <td class="text-right"><?= $fmt($row['totale']) ?></td>
                    <td class="text-right"><?= (int) $row['n_righe'] ?></td>
                    <td class="text-right"><?= $fmt($row['tot_pezzi']) ?></td>
                    <td>
                        <?php if ($row['n_scadenze'] > 0): ?>
                            <span class="badge badge-secondary"><?= (int) $row['n_scadenze'] ?></span>
                            <?php if ($row['prossima_scadenza']): ?>
                                <span class="text-muted small">pross. <?= date('d/m/Y', strtotime($row['prossima_scadenza'])) ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted small">nessuna</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['bloccata']): ?>
                            <span class="badge badge-danger"><i class="fas fa-lock"></i> bloccate</span>
                        <?php elseif ($row['n_bloccate'] > 0): ?>
                            <span class="badge badge-warning"><i class="fas fa-lock"></i> <?= (int) $row['n_bloccate'] ?>/<?= (int) $row['n_scadenze'] ?></span>
                        <?php else: ?>
                            <span class="badge badge-success"><i class="fas fa-lock-open"></i> libere</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if ($row['n_reclami'] > 0): ?>
                            <span class="badge badge-info badge-reclami"
                                  data-id="<?= (int) $d->id ?>"><?= (int) $row['n_reclami'] ?></span>
                        <?php else: ?>
                            <span class="text-muted">0</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center text-nowrap no-export">
                        <button type="button"
                                class="btn btn-sm <?= $row['bloccata'] ? 'btn-outline-warning' : 'btn-outline-success' ?> btn-blocca"
                                title="<?= $row['bloccata'] ? 'Sblocca scadenze' : 'Blocca scadenze' ?>"
                                data-id="<?= (int) $d->id ?>"
                                data-bloccata="<?= $row['bloccata'] ? 1 : 0 ?>"
                                data-doc="<?= Html::encode($d->etichetta) ?>"
                                <?= $row['n_scadenze'] === 0 ? 'disabled' : '' ?>>
                            <i class="fas <?= $row['bloccata'] ? 'fa-lock' : 'fa-lock-open' ?>"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-reclamo"
                                title="Invia reclamo"
                                data-id="<?= (int) $d->id ?>"
                                data-doc="<?= Html::encode($d->etichetta) ?>"
                                data-numero="<?= Html::encode($d->numero) ?>"
                                data-data="<?= Html::encode($dataDoc) ?>"
                                data-fornitore="<?= Html::encode($row['fornitore']) ?>"
                                data-email="<?= Html::encode($row['fornitore_email']) ?>"
                                data-oggetto="<?= Html::encode($oggetto) ?>">
                            <i class="fas fa-envelope"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info btn-reclami"
                                title="Storico reclami"
                                data-id="<?= (int) $d->id ?>"
                                data-doc="<?= Html::encode($d->etichetta) ?>">
                            <i class="fas fa-history"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($fatture)): ?>
            <div class="alert alert-info mb-0">
                Nessuna fattura fornitore trovata con i filtri selezionati.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modale invio reclamo -->
<div class="modal fade" id="modal-reclamo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white"><i class="fas fa-envelope"></i> Invia reclamo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reclamo-id-documento" value="">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label class="small mb-1">Documento</label>
                        <div class="form-control-plaintext font-weight-bold" id="reclamo-doc"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="small mb-1">Fornitore</label>
                        <div class="form-control-plaintext" id="reclamo-fornitore"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="small mb-1">Destinatari (separati da virgola)</label>
                    <input type="text" id="reclamo-destinatari" class="form-control" placeholder="email@fornitore.it">
                </div>
                <div class="form-group">
                    <label class="small mb-1">Oggetto</label>
                    <input type="text" id="reclamo-oggetto" class="form-control" maxlength="200">
                </div>
                <div class="form-group">
                    <label class="small mb-1">Testo del reclamo *</label>
                    <textarea id="reclamo-messaggio" class="form-control" rows="6"
                              placeholder="Descrivi il motivo del reclamo..."></textarea>
                </div>
                <div id="reclamo-errori"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" id="btn-invia-reclamo">
                    <i class="fas fa-paper-plane"></i> Invia reclamo
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modale storico reclami -->
<div class="modal fade" id="modal-reclami" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">
                    <i class="fas fa-history"></i> Reclami inviati — <span id="reclami-titolo-doc"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="reclami-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var csrf = (typeof yii !== 'undefined' && yii.getCsrfToken) ? yii.getCsrfToken() : '<?= Yii::$app->request->csrfToken ?>';
    var toggleUrl = '<?= Url::to(['toggle-blocco']) ?>';
    var reclamoUrl = '<?= Url::to(['invia-reclamo']) ?>';
    var reclamiUrl = '<?= Url::to(['reclami']) ?>';

    var reclamiSummary = <?= $reclamiJson ?>;

    function esc(v) {
        if (v === null || v === undefined) { return ''; }
        return String(v).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function swalConfirm(text, onConfirm) {
        if (typeof Swal === 'undefined') {
            if (window.confirm(text)) { onConfirm(); }
            return;
        }
        Swal.fire({
            title: 'Conferma',
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sì, procedi',
            cancelButtonText: 'Annulla'
        }).then(function (r) {
            if (r.isConfirmed) { onConfirm(); }
        });
    }

    function swalError(text) {
        if (typeof Swal === 'undefined') { window.alert(text); return; }
        Swal.fire('Errore', text, 'error');
    }

    function swalToast(icon, title) {
        if (typeof Swal === 'undefined') { return; }
        Swal.fire({
            toast: true, position: 'top-end', icon: icon, title: title,
            showConfirmButton: false, timer: 2000, timerProgressBar: true
        });
    }

    function mostraErroriReclamo(msgs) {
        var $box = $('#reclamo-errori').empty();
        var $ul = $('<ul class="mb-0 pl-3">');
        $.each(msgs, function (i, m) { $ul.append($('<li>').text(m)); });
        $('<div class="alert alert-danger mb-0">').append($ul).appendTo($box);
    }

    // Tippy: riepilogo dei reclami al passaggio sul contatore.
    function contenutoReclami(el) {
        var id = el.getAttribute('data-id');
        var lista = reclamiSummary[id] || [];
        if (!lista.length) { return 'Nessun reclamo'; }
        var html = '<div style="text-align:left;font-size:.82rem">'
            + '<strong><i class="fas fa-envelope"></i> Reclami (' + lista.length + ')</strong>'
            + '<ul style="margin:.35rem 0 0;padding-left:1.1rem">';
        lista.forEach(function (r) {
            html += '<li>' + esc(r.data) + ' — '
                + (r.esito
                    ? '<span style="color:#28a745">inviato</span>'
                    : '<span style="color:#dc3545">errore</span>')
                + (r.utente ? ' da ' + esc(r.utente) : '')
                + '<br><span style="color:#666">' + esc(r.oggetto) + '</span></li>';
        });
        html += '</ul></div>';
        return html;
    }

    function initReclamiTippy() {
        if (typeof tippy === 'undefined') { return; }
        var els = document.querySelectorAll('.badge-reclami');
        Array.prototype.forEach.call(els, function (el) {
            if (el._tippy) { return; }
            tippy(el, {
                allowHTML: true,
                maxWidth: 420,
                placement: 'left',
                content: contenutoReclami(el)
            });
        });
    }

    $(document).on('click', '.btn-blocca', function () {
        var $b = $(this);
        var bloccata = String($b.data('bloccata')) === '1';
        var blocca = bloccata ? 0 : 1;
        var msg = bloccata
            ? 'Sbloccare le scadenze di questa fattura?'
            : 'Bloccare le scadenze di questa fattura?';

        swalConfirm(msg, function () {
            $b.prop('disabled', true);
            $.post(toggleUrl, {id_documento: $b.data('id'), blocca: blocca, _csrf: csrf})
                .done(function (res) {
                    if (res && res.success) {
                        swalToast('success', res.message || 'Operazione completata.');
                        window.setTimeout(function () { window.location.reload(); }, 1200);
                        return;
                    }
                    $b.prop('disabled', false);
                    swalError(res && res.error ? res.error : 'Operazione non riuscita.');
                })
                .fail(function () {
                    $b.prop('disabled', false);
                    swalError('Errore di rete durante l\'operazione.');
                });
        });
    });

    $(document).on('click', '.btn-reclamo', function () {
        var $b = $(this);
        $('#reclamo-id-documento').val($b.data('id'));
        $('#reclamo-doc').text($b.data('doc'));
        $('#reclamo-fornitore').text($b.data('fornitore'));
        $('#reclamo-oggetto').val($b.data('oggetto'));
        $('#reclamo-destinatari').val($b.data('email') || '');
        $('#reclamo-messaggio').val('');
        $('#reclamo-errori').empty();
        $('#modal-reclamo').modal('show');
    });

    $('#btn-invia-reclamo').on('click', function () {
        var messaggio = $.trim($('#reclamo-messaggio').val());
        if (!messaggio) {
            mostraErroriReclamo(['Scrivi il testo del reclamo.']);
            return;
        }
        var $btn = $(this).prop('disabled', true);
        $.post(reclamoUrl, {
            id_documento: $('#reclamo-id-documento').val(),
            oggetto: $('#reclamo-oggetto').val(),
            destinatari: $('#reclamo-destinatari').val(),
            messaggio: messaggio,
            _csrf: csrf
        }).done(function (res) {
            if (res && res.success) {
                swalToast('success', res.message || 'Reclamo inviato.');
                window.setTimeout(function () { window.location.reload(); }, 1200);
                return;
            }
            $btn.prop('disabled', false);
            mostraErroriReclamo([res && res.error ? res.error : 'Invio non riuscito.']);
        }).fail(function () {
            $btn.prop('disabled', false);
            mostraErroriReclamo(['Errore di rete durante l\'invio.']);
        });
    });

    $(document).on('click', '.btn-reclami', function () {
        var $b = $(this);
        $('#reclami-titolo-doc').text($b.data('doc'));
        $('#reclami-body').html('<div class="text-muted">Caricamento...</div>');
        $('#modal-reclami').modal('show');

        $.get(reclamiUrl, {id_documento: $b.data('id')}, function (res) {
            if (!res || !res.success) {
                $('#reclami-body').html('<div class="alert alert-danger mb-0">Errore nel caricamento dei reclami.</div>');
                return;
            }
            if (!res.reclami.length) {
                $('#reclami-body').html('<div class="alert alert-info mb-0">Nessun reclamo inviato per questa fattura.</div>');
                return;
            }
            var html = '<table class="table table-sm table-bordered mb-0"><thead><tr>'
                + '<th style="width:130px">Data</th><th style="width:90px">Esito</th>'
                + '<th>Oggetto</th><th>Destinatari</th><th>Utente</th></tr></thead><tbody>';
            $.each(res.reclami, function (i, r) {
                html += '<tr>'
                    + '<td>' + esc(r.created_at) + '</td>'
                    + '<td>' + (r.esito
                        ? '<span class="badge badge-success">Inviato</span>'
                        : '<span class="badge badge-danger">Errore</span>') + '</td>'
                    + '<td>' + esc(r.oggetto) + '</td>'
                    + '<td>' + esc(r.destinatari) + '</td>'
                    + '<td>' + esc(r.utente) + '</td>'
                    + '</tr>'
                    + '<tr><td colspan="5" class="bg-light">'
                    + '<strong>Testo:</strong> <span style="white-space:pre-line">' + esc(r.messaggio) + '</span>'
                    + (r.errore ? '<div class="text-danger small mt-1">' + esc(r.errore) + '</div>' : '')
                    + '</td></tr>';
            });
            html += '</tbody></table>';
            $('#reclami-body').html(html);
        }).fail(function () {
            $('#reclami-body').html('<div class="alert alert-danger mb-0">Errore di rete.</div>');
        });
    });

    $(function () {
        initReclamiTippy();
        $('#ff-index-table').on('draw.dt', function () { initReclamiTippy(); });
    });
})();
</script>
<?php DataTables::render('ff-index-table', 1, 'desc'); ?>
