<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $documenti app\models\MgDocumento[] */
/* @var $riscontri array */
/* @var $barcode array */

$this->title = 'Riscontro quantita' . "'" . ' documenti fornitore';
$this->params['breadcrumbs'][] = ['label' => 'Controllo documenti fornitore', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Riscontro';

$fmt = function ($v) { return number_format((float) $v, 2, ',', '.'); };

$totRighe = 0;
$totRigheRis = 0;
foreach ($documenti as $doc) {
    foreach ($doc->righe as $r) {
        $totRighe++;
        $cr = $riscontri[$doc->id][$r->id] ?? null;
        if ($cr && $cr->qta_riscontrata !== null && $cr->qta_riscontrata !== '') {
            $totRigheRis++;
        }
    }
}
?>
<div class="dcf-controlla">

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
        <div>
            <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Passo 2 di 3 — Riscontro quantita'</h5>
            <div class="text-muted small"><?= count($documenti) ?> documenti selezionati</div>
        </div>
        <form method="post" action="<?= Url::to(['annulla']) ?>" class="m-0"
              onsubmit="return confirm('Annullare la selezione corrente? I riscontri gia\' salvati restano memorizzati.');">
            <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
            <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Selezione</a>
            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-times"></i> Annulla selezione</button>
        </form>
    </div>

    <!-- Riepilogo sempre visibile -->
    <div class="card shadow-sm mb-3 border-primary" style="position: sticky; top: 4.6rem; z-index: 1020;">
        <div class="card-body py-2">
            <div class="row text-center">
                <div class="col-6 col-md-2 border-right">
                    <div class="text-muted small">Righe riscontrate</div>
                    <div class="font-weight-bold" id="g-righe-ris"><?= $totRigheRis ?> / <?= $totRighe ?></div>
                </div>
                <div class="col-6 col-md-2 border-right">
                    <div class="text-muted small">Totale pezzi documento</div>
                    <div class="font-weight-bold" id="g-tot-doc">—</div>
                </div>
                <div class="col-6 col-md-2 border-right">
                    <div class="text-muted small">Totale pezzi riscontrati</div>
                    <div class="font-weight-bold text-primary" id="g-tot-ris">—</div>
                </div>
                <div class="col-6 col-md-2 border-right">
                    <div class="text-muted small">Differenza pezzi</div>
                    <div class="font-weight-bold" id="g-diff">—</div>
                </div>
                <div class="col-6 col-md-2 border-right">
                    <div class="text-muted small">Totale documenti</div>
                    <div class="font-weight-bold" id="g-tot-doc-eur">—</div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="text-muted small">Documenti</div>
                    <div class="font-weight-bold"><?= count($documenti) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Azioni rapide -->
    <div class="d-flex justify-content-end mb-3 flex-wrap">
        <button type="button" class="btn btn-outline-secondary btn-sm mr-2" id="dcf-conformi-all">
            <i class="fas fa-equals"></i> Tutte conformi
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="dcf-azzera-all">
            <i class="fas fa-ban"></i> Azzera tutte a 0
        </button>
    </div>

    <!-- Import da file TXT -->
    <div class="card shadow-sm mb-3">
        <div class="card-header py-2">
            <a class="text-decoration-none" data-toggle="collapse" href="#dcf-import" role="button">
                <i class="fas fa-file-import"></i> Importa quantita' da file (cd_ar o barcode + qta)
            </a>
        </div>
        <div class="collapse" id="dcf-import">
            <div class="card-body">
                <p class="small text-muted mb-2">
                    Una riga per articolo: <code>CODICE QTA</code> (es. <code>ART00021 12</code>).
                    Separatori ammessi: spazio, tab, punto e virgola. Il codice puo' essere
                    il codice articolo oppure un barcode/matricola. Le righe senza quantita' valgono 1.
                </p>
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4 mb-2">
                        <label class="small mb-1">File .txt</label>
                        <input type="file" id="dcf-file" accept=".txt,.csv,text/plain" class="form-control-file">
                    </div>
                    <div class="form-group col-md-6 mb-2">
                        <label class="small mb-1">Oppure incolla il testo</label>
                        <textarea id="dcf-txt" rows="3" class="form-control" placeholder="ART00021 12&#10;8001234567890 5"></textarea>
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <button type="button" id="dcf-import-btn" class="btn btn-primary btn-block">
                            <i class="fas fa-upload"></i> Importa
                        </button>
                    </div>
                </div>
                <div id="dcf-import-report" class="small"></div>
            </div>
        </div>
    </div>

    <form method="post" action="<?= Url::to(['salva']) ?>" id="dcf-controlla-form">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
        <?php foreach ($documenti as $doc): ?>
            <?php
            $totPezziDoc = 0.0;
            foreach ($doc->righe as $r) {
                $totPezziDoc += (float) $r->qta;
            }
            ?>
            <div class="card shadow-sm mb-3" data-doc-card="<?= (int) $doc->id ?>" data-doc-tot="<?= (float) $doc->totale ?>">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <strong><?= Html::encode($doc->etichetta) ?></strong>
                        <span class="text-muted">·</span>
                        <?= Html::encode($doc->anagrafica->ragione_sociale ?? '') ?>
                        <span class="text-muted small ml-2">
                            <?= $doc->data ? date('d/m/Y', strtotime($doc->data)) : '' ?>
                        </span>
                    </div>
                    <div class="small">
                        <span class="mr-3">Righe: <span class="font-weight-bold doc-righe-ris">0 / 0</span></span>
                        <span class="mr-3">Pezzi doc.: <span class="font-weight-bold"><?= $fmt($totPezziDoc) ?></span></span>
                        <span class="mr-3">Riscontrati: <span class="font-weight-bold text-primary doc-tot-ris">0,00</span></span>
                        <span>Diff.: <span class="font-weight-bold doc-diff">0,00</span></span>
                        <button type="button" class="btn btn-outline-secondary btn-sm ml-2 dcf-conformi">
                            <i class="fas fa-equals"></i> Tutte conformi
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm ml-1 dcf-azzera">
                            <i class="fas fa-ban"></i> Azzera
                        </button>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                        <tr>
                            <th style="width:14%">Codice</th>
                            <th>Descrizione</th>
                            <th style="width:7%">U.M.</th>
                            <th class="text-right" style="width:11%">Q.ta documento</th>
                            <th class="text-right" style="width:13%">Q.ta riscontrata</th>
                            <th class="text-right" style="width:11%">Differenza</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($doc->righe as $r): ?>
                            <?php
                            $cr = $riscontri[$doc->id][$r->id] ?? null;
                            $valRis = ($cr && $cr->qta_riscontrata !== null && $cr->qta_riscontrata !== '')
                                ? number_format((float) $cr->qta_riscontrata, 2, ',', '.') : '';
                            $codice = strtoupper(trim((string) $r->codice_articolo));
                            $bc = $barcode[$r->codice_articolo] ?? [];
                            $bcAttr = strtoupper(implode(',', array_map('strval', $bc)));
                            $diff = ($valRis !== '') ? round((float) $cr->qta_riscontrata - (float) $r->qta, 4) : null;
                            ?>
                            <tr>
                                <td><?= Html::encode($r->codice_articolo) ?></td>
                                <td><?= Html::encode($r->descrizione) ?></td>
                                <td><?= Html::encode($r->um) ?></td>
                                <td class="text-right"><?= $fmt($r->qta) ?></td>
                                <td class="text-right">
                                    <input type="text" inputmode="decimal"
                                           class="form-control form-control-sm text-right qta-riscontrata"
                                           name="qta[<?= (int) $doc->id ?>][<?= (int) $r->id ?>]"
                                           value="<?= $valRis ?>"
                                           placeholder="<?= $fmt($r->qta) ?>"
                                           data-doc="<?= (int) $doc->id ?>"
                                           data-riga="<?= (int) $r->id ?>"
                                           data-qta-doc="<?= (float) $r->qta ?>"
                                           data-codice="<?= Html::encode($codice) ?>"
                                           data-barcode="<?= Html::encode($bcAttr) ?>">
                                </td>
                                <td class="text-right">
                                    <?php if ($diff === null): ?>
                                        <span class="diff-cell text-muted">—</span>
                                    <?php elseif (abs($diff) > 0.0001): ?>
                                        <span class="diff-cell text-danger font-weight-bold"><?= $fmt($diff) ?></span>
                                    <?php else: ?>
                                        <span class="diff-cell text-success"><?= $fmt($diff) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($doc->righe)): ?>
                            <tr><td colspan="6" class="text-muted">Nessuna riga.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                <div class="text-muted small">
                    Le quantita' non compilate restano da riscontrare.
                </div>
                <div>
                    <button type="submit" name="azione" value="controlla" class="btn btn-outline-primary">
                        <i class="fas fa-save"></i> Salva riscontro
                    </button>
                    <button type="submit" name="azione" value="riepilogo" class="btn btn-success">
                        <i class="fas fa-arrow-right"></i> Salva e vai al riepilogo
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    function parseQty(s) {
        s = (s === null || s === undefined) ? '' : ('' + s).trim();
        if (s === '') return null;
        if (s.indexOf(',') >= 0) s = s.replace(/\./g, '').replace(',', '.');
        var n = parseFloat(s);
        return isNaN(n) ? null : n;
    }
    function fmt(n) {
        var v = Math.round(n * 100) / 100;
        var neg = v < 0;
        v = Math.abs(v);
        var s = v.toFixed(2).replace('.', ',');
        var parts = s.split(',');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return (neg ? '-' : '') + parts.join(',');
    }
    function setText(sel, txt, cls) {
        var el = document.querySelector(sel);
        if (el) { el.textContent = txt; if (cls !== undefined) el.className = cls; }
    }

    function ricalcola() {
        var gDoc = 0, gRis = 0, gDiff = 0, gEur = 0, nRighe = 0, nRis = 0;
        var perDoc = {};

        document.querySelectorAll('input.qta-riscontrata').forEach(function (inp) {
            var docId = inp.dataset.doc;
            var qd = parseFloat(inp.dataset.qtaDoc) || 0;
            if (!perDoc[docId]) perDoc[docId] = { doc: 0, ris: 0, diff: 0, tot: 0, risCount: 0 };
            perDoc[docId].doc += qd; perDoc[docId].tot++;
            gDoc += qd; nRighe++;

            var tr = inp.closest('tr');
            var cell = tr.querySelector('.diff-cell');
            var val = parseQty(inp.value);
            if (val === null) {
                if (cell) { cell.textContent = '—'; cell.className = 'diff-cell text-muted'; }
                tr.classList.remove('table-warning');
                return;
            }
            var d = Math.round((val - qd) * 10000) / 10000;
            perDoc[docId].ris += val; perDoc[docId].risCount++; gRis += val; nRis++;
            perDoc[docId].diff += d; gDiff += d;
            if (Math.abs(d) > 0.0001) {
                if (cell) { cell.textContent = fmt(d); cell.className = 'diff-cell text-danger font-weight-bold'; }
                tr.classList.add('table-warning');
            } else {
                if (cell) { cell.textContent = fmt(0); cell.className = 'diff-cell text-success'; }
                tr.classList.remove('table-warning');
            }
        });

        Object.keys(perDoc).forEach(function (id) {
            var card = document.querySelector('[data-doc-card="' + id + '"]');
            if (!card) return;
            var pd = perDoc[id];
            var elR = card.querySelector('.doc-righe-ris'); if (elR) elR.textContent = pd.risCount + ' / ' + pd.tot;
            var elT = card.querySelector('.doc-tot-ris'); if (elT) elT.textContent = fmt(pd.ris);
            var elD = card.querySelector('.doc-diff');
            if (elD) {
                elD.textContent = fmt(pd.diff);
                elD.className = 'font-weight-bold doc-diff ' + (Math.abs(pd.diff) > 0.0001 ? 'text-danger' : 'text-success');
            }
        });

        setText('#g-righe-ris', nRis + ' / ' + nRighe);
        setText('#g-tot-doc', fmt(gDoc));
        setText('#g-tot-ris', fmt(gRis));
        var gd = document.getElementById('g-diff');
        if (gd) {
            gd.textContent = fmt(gDiff);
            gd.className = 'font-weight-bold ' + (Math.abs(gDiff) > 0.0001 ? 'text-danger' : 'text-success');
        }

        // Totale documenti in euro (statico, dalle intestazioni caricate)
        var eur = 0;
        document.querySelectorAll('[data-doc-card]').forEach(function (card) {
            var e = parseFloat(card.dataset.docTot || '0');
            if (!isNaN(e)) eur += e;
        });
        setText('#g-tot-doc-eur', fmt(eur));
    }

    // Memorizza il totale documento su ogni card (data-doc-tot dal PHP).

    document.querySelectorAll('input.qta-riscontrata').forEach(function (inp) {
        inp.addEventListener('input', ricalcola);
        inp.addEventListener('change', ricalcola);
    });

    document.querySelectorAll('.dcf-conformi').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var card = btn.closest('[data-doc-card]');
            if (!card) return;
            card.querySelectorAll('input.qta-riscontrata').forEach(function (inp) {
                inp.value = fmt(parseFloat(inp.dataset.qtaDoc) || 0);
            });
            ricalcola();
        });
    });

    document.querySelectorAll('.dcf-azzera').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var card = btn.closest('[data-doc-card]');
            if (!card) return;
            card.querySelectorAll('input.qta-riscontrata').forEach(function (inp) {
                inp.value = '0,00';
            });
            ricalcola();
        });
    });

    var confAll = document.getElementById('dcf-conformi-all');
    if (confAll) {
        confAll.addEventListener('click', function () {
            document.querySelectorAll('input.qta-riscontrata').forEach(function (inp) {
                inp.value = fmt(parseFloat(inp.dataset.qtaDoc) || 0);
            });
            ricalcola();
        });
    }

    var azzAll = document.getElementById('dcf-azzera-all');
    if (azzAll) {
        azzAll.addEventListener('click', function () {
            document.querySelectorAll('input.qta-riscontrata').forEach(function (inp) {
                inp.value = '0,00';
            });
            ricalcola();
        });
    }

    // ---- Import da file / testo ----
    function rowsByCode() {
        var map = {};
        document.querySelectorAll('input.qta-riscontrata').forEach(function (inp) {
            var codes = [];
            if (inp.dataset.codice) codes.push(inp.dataset.codice.toUpperCase());
            if (inp.dataset.barcode) {
                inp.dataset.barcode.split(',').forEach(function (b) {
                    b = b.trim(); if (b) codes.push(b.toUpperCase());
                });
            }
            codes.forEach(function (c) {
                if (!map[c]) map[c] = [];
                map[c].push(inp);
            });
        });
        return map;
    }

    function parseText(text) {
        var map = {};
        text.split(/\r?\n/).forEach(function (line) {
            line = line.trim();
            if (line === '') return;
            var parts = line.split(/[\t;,]+|\s{2,}/);
            if (parts.length === 1) parts = line.split(/\s+/);
            parts = parts.map(function (s) { return s.trim(); }).filter(function (s) { return s !== ''; });
            if (!parts.length) return;
            var code = parts[0].toUpperCase();
            var qty = null;
            for (var i = parts.length - 1; i >= 1; i--) {
                var n = parseQty(parts[i]);
                if (n !== null) { qty = n; break; }
            }
            if (qty === null) qty = 1;
            map[code] = (map[code] || 0) + qty;
        });
        return map;
    }

    function applicaImport(map) {
        var rb = rowsByCode();
        var matchedCodes = 0, matchedRows = 0, unmatched = [], ambigui = [];
        Object.keys(map).forEach(function (code) {
            var rows = rb[code];
            if (!rows || !rows.length) { unmatched.push(code); return; }
            var qty = map[code];
            if (rows.length === 1) {
                rows[0].value = fmt(qty);
                matchedCodes++; matchedRows++;
                return;
            }
            var vuote = rows.filter(function (inp) { return inp.value.trim() === ''; });
            if (vuote.length === 1) {
                vuote[0].value = fmt(qty);
                matchedCodes++; matchedRows++;
            } else {
                ambigui.push(code + ' (' + rows.length + ' righe)');
            }
        });
        return { codes: matchedCodes, rows: matchedRows, unmatched: unmatched, ambigui: ambigui };
    }

    function mostraReport(rep) {
        var html = '<div class="alert alert-info py-2 mb-0">'
            + '<i class="fas fa-check-circle"></i> Importate <strong>' + rep.rows + '</strong> righe ('
            + rep.codes + ' codici).';
        if (rep.unmatched.length) {
            html += '<div class="text-danger mt-1"><i class="fas fa-exclamation-triangle"></i> Codici non trovati ('
                + rep.unmatched.length + '): ' + rep.unmatched.slice(0, 30).join(', ')
                + (rep.unmatched.length > 30 ? '…' : '') + '</div>';
        }
        if (rep.ambigui.length) {
            html += '<div class="text-warning mt-1"><i class="fas fa-question-circle"></i> Codici presenti in piu\' righe, non assegnati: '
                + rep.ambigui.join(', ') + '</div>';
        }
        html += '</div>';
        document.getElementById('dcf-import-report').innerHTML = html;
    }

    var importBtn = document.getElementById('dcf-import-btn');
    if (importBtn) {
        importBtn.addEventListener('click', function () {
            var txt = document.getElementById('dcf-txt').value;
            var file = document.getElementById('dcf-file').files[0];
            if (txt.trim() !== '') {
                mostraReport(applicaImport(parseText(txt)));
                ricalcola();
                return;
            }
            if (!file) { alert('Seleziona un file o incolla il testo.'); return; }
            var reader = new FileReader();
            reader.onload = function (e) {
                mostraReport(applicaImport(parseText(e.target.result)));
                ricalcola();
            };
            reader.readAsText(file);
        });
    }

    ricalcola();
})();
</script>
