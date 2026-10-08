<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dati array|null valori di ogni sezione (null = sezione non visibile) */
/* @var $vis array visibilità delle sezioni in base ai permessi dell'utente */

$this->title = 'Dashboard';

$f = Yii::$app->formatter;
$eur = function ($v) use ($f) { return '€ ' . $f->asDecimal((float) $v, 2); };
$num = function ($v) use ($f) { return $f->asInteger((int) $v); };
$data = function ($v) { return $v ? date('d/m/Y', strtotime($v)) : '—'; };
$orario = function ($v) { return $v ? substr($v, 0, 5) : '—'; };

$etichettaDoc = function ($r) {
    $s = trim($r['codice_tipo'] . ' ' . $r['anno'] . '/' . $r['numero']);
    if (!empty($r['suffisso'])) {
        $s .= '/' . $r['suffisso'];
    }
    return $s;
};

$badgeStatoDoc = function ($stato) {
    $map = [
        'bozza'      => ['secondary', 'Bozza'],
        'confermato' => ['primary', 'Confermato'],
        'chiuso'     => ['success', 'Chiuso'],
    ];
    $b = $map[$stato] ?? ['light', $stato];
    return Html::tag('span', Html::encode($b[1]), ['class' => 'badge badge-' . $b[0]]);
};

$badgeStatoPlanning = function ($stato) {
    $map = [
        'Da Iniziare' => ['secondary', 'Da iniziare'],
        'In Corso'    => ['info', 'In corso'],
        'Completato'  => ['success', 'Completato'],
        'Annullato'   => ['danger', 'Annullato'],
    ];
    $b = $map[$stato] ?? ['light', $stato];
    return Html::tag('span', Html::encode($b[1]), ['class' => 'badge badge-' . $b[0]]);
};

$sc = $dati['scadenze'];
$or = $dati['ordini'];
$gi = $dati['giacenze'];
$pl = $dati['planning'];
$td = $dati['todo'];
$vt = $dati['vtiger'];

$mostra = [
    'scadenze' => $vis['scadenze'],
    'ordini'   => $vis['ordini'],
    'giacenze' => $vis['giacenze'],
    'planning' => $vis['planning'] || $vis['presenze'],
    'todo'     => $vis['todo'],
    'vtiger'   => $vis['vtiger'],
];
$qualcosa = in_array(true, $mostra, true);

$totScadute = $sc ? $sc['scadute'] + $sc['prossime'] : 0;
$totImporto = $sc ? $sc['importo_scadute'] + $sc['importo_prossime'] : 0;
$ordiniTotali = $or ? $or['cliente']['n'] + $or['fornitore']['n'] : 0;
$valoreOrdini = $or ? $or['cliente']['totale'] + $or['fornitore']['totale'] : 0;
?>

<style>
    .dash-kpi { border-top-width: 3px !important; border-radius: 10px; }
    .dash-kpi .dash-num { font-size: 1.7rem; line-height: 1.1; font-weight: 700; }
    .dash-kpi .dash-label { font-size: .7rem; letter-spacing: .06em; text-transform: uppercase; font-weight: 700; }
    .dash-kpi .dash-sub { font-size: .8rem; }
    .dash-card .card-header { padding: .6rem 1rem; }
    .dash-card .card-title { font-size: .95rem; font-weight: 600; }
    .dash-card .card-body { padding: 0; }
    .dash-card table { margin-bottom: 0; font-size: .85rem; }
    .dash-card thead th { font-weight: 600; font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; border-top: 0; }
    .dash-card td { vertical-align: middle; }
    .dash-card .card-footer { padding: .45rem 1rem; font-size: .8rem; background: transparent; border-top: 1px solid rgba(0,0,0,.06); }
    .dash-stat { font-size: .8rem; }
    .dash-stat b { font-size: 1.05rem; }
    .dash-empty { padding: 1.6rem 1rem; text-align: center; color: #6c757d; font-size: .85rem; }
    @media (max-width: 767.98px) { .dash-kpi .dash-num { font-size: 1.4rem; } }
</style>

<div class="dashboard-index">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1 class="h3 mb-2"><i class="fas fa-tachometer-alt text-primary"></i> Dashboard</h1>
        <div class="mb-2">
            <span class="text-muted small mr-3"><?= Html::encode(date('d/m/Y')) ?></span>
            <?php if ($vis['planning']): ?>
                <?= Html::a('<i class="fas fa-calendar-alt"></i> Planning', ['planning/index'], ['class' => 'btn btn-sm btn-outline-primary']) ?>
            <?php endif; ?>
            <?php if ($vis['scadenze'] || $vis['ordini']): ?>
                <?= Html::a('<i class="fas fa-file-invoice"></i> Documenti', ['mgdocumento/index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$qualcosa): ?>
        <div class="alert alert-light border">
            <i class="fas fa-lock text-muted"></i>
            Nessuna sezione disponibile per il tuo livello di accesso.
            Contatta l'amministratore se ritieni che manchino i permessi.
        </div>
    <?php endif; ?>

    <!-- ============================ KPI ============================ -->
    <?php if ($qualcosa): ?>
    <div class="row mb-3">
        <?php if ($mostra['scadenze']): ?>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dash-kpi shadow-sm h-100" style="border-top-color: #dc3545;">
                <div class="card-body d-flex align-items-center py-3">
                    <div class="mr-3"><i class="fas fa-file-invoice-dollar fa-2x" style="color:#dc3545;"></i></div>
                    <div class="flex-grow-1">
                        <div class="dash-label text-muted">Scadenze da pagare</div>
                        <div class="dash-num" style="color:#dc3545;"><?= $num($totScadute) ?></div>
                        <div class="dash-sub text-muted">
                            <?= $eur($totImporto) ?> nei prossimi 30 giorni
                        </div>
                        <?php if ($sc['scadute'] > 0): ?>
                            <div class="dash-sub font-weight-bold" style="color:#dc3545;">
                                <?= $num($sc['scadute']) ?> già scadute (<?= $eur($sc['importo_scadute']) ?>)
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mostra['ordini']): ?>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dash-kpi shadow-sm h-100" style="border-top-color: #0d6efd;">
                <div class="card-body d-flex align-items-center py-3">
                    <div class="mr-3"><i class="fas fa-cart-shopping fa-2x" style="color:#0d6efd;"></i></div>
                    <div class="flex-grow-1">
                        <div class="dash-label text-muted">Ordini aperti</div>
                        <div class="dash-num" style="color:#0d6efd;"><?= $num($ordiniTotali) ?></div>
                        <div class="dash-sub text-muted"><?= $eur($valoreOrdini) ?> complessivi</div>
                        <div class="dash-sub text-muted">
                            <?= $num($or['cliente']['n']) ?> cliente · <?= $num($or['fornitore']['n']) ?> fornitore
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mostra['giacenze']): ?>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dash-kpi shadow-sm h-100" style="border-top-color: #ffc107;">
                <div class="card-body d-flex align-items-center py-3">
                    <div class="mr-3"><i class="fas fa-boxes-stacked fa-2x" style="color:#e0a800;"></i></div>
                    <div class="flex-grow-1">
                        <div class="dash-label text-muted">Giacenze sotto zero</div>
                        <div class="dash-num" style="color:#e0a800;"><?= $num($gi['sotto_zero']) ?></div>
                        <div class="dash-sub text-muted">
                            articoli su <?= $num($gi['movimentati']) ?> movimentati (<?= $num($gi['catalogo']) ?> a catalogo)
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mostra['planning']): ?>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card dash-kpi shadow-sm h-100" style="border-top-color: #198754;">
                <div class="card-body d-flex align-items-center py-3">
                    <div class="mr-3"><i class="fas fa-calendar-check fa-2x" style="color:#198754;"></i></div>
                    <div class="flex-grow-1">
                        <?php if ($pl['con_planning']): ?>
                            <div class="dash-label text-muted">Attività di oggi</div>
                            <div class="dash-num" style="color:#198754;"><?= $num($pl['oggi']) ?></div>
                            <div class="dash-sub text-muted">
                                <?= $num($pl['settimana']) ?> nei prossimi 7 giorni
                            </div>
                        <?php else: ?>
                            <div class="dash-label text-muted">Presenze di oggi</div>
                            <div class="dash-num" style="color:#198754;"><?= $num($pl['presenze']) ?></div>
                            <div class="dash-sub text-muted">
                                <?= $f->asDecimal($pl['ore'], 1) ?> h lavorate
                            </div>
                        <?php endif; ?>
                        <?php if ($pl['con_planning'] && $pl['con_presenze']): ?>
                            <div class="dash-sub text-muted">
                                <?= $num($pl['presenze']) ?> presenze (<?= $f->asDecimal($pl['ore'], 1) ?> h)
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ================= SCADENZE · ORDINI · GIACENZE ================= -->
    <?php if ($mostra['scadenze'] || $mostra['ordini'] || $mostra['giacenze']): ?>
    <div class="row mb-3">

        <?php if ($mostra['scadenze']): ?>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-outline card-primary shadow-sm h-100 dash-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-hourglass-half text-danger"></i> Scadenze prossime</h3>
                    <div class="card-tools">
                        <span class="badge badge-danger"><?= $eur($totImporto) ?></span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($sc['lista'])): ?>
                        <div class="dash-empty">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                            Nessuna scadenza aperta: tutto in regola.
                        </div>
                    <?php else: ?>
                        <table class="table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Data</th>
                                    <th>Anagrafica</th>
                                    <th>Documento</th>
                                    <th class="text-right">Importo</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($sc['lista'] as $r):
                                $gg = (int) floor((strtotime($r['data_scadenza']) - strtotime(date('Y-m-d'))) / 86400);
                                if ($gg < 0) {
                                    $classe = 'badge-danger';
                                    $testo = 'scaduta';
                                } elseif ($gg <= 7) {
                                    $classe = 'badge-warning';
                                    $testo = 'tra ' . $gg . ' g';
                                } else {
                                    $classe = 'badge-light';
                                    $testo = 'tra ' . $gg . ' g';
                                }
                            ?>
                                <tr>
                                    <td class="text-nowrap">
                                        <?= Html::encode($data($r['data_scadenza'])) ?><br>
                                        <span class="badge <?= $classe ?>"><?= Html::encode($testo) ?></span>
                                    </td>
                                    <td><?= Html::encode($r['ragione_sociale'] ?: '—') ?></td>
                                    <td class="text-nowrap">
                                        <?= Html::a(Html::encode($etichettaDoc($r)), ['mgdocumento/view', 'id' => $r['id_documento']]) ?>
                                    </td>
                                    <td class="text-right text-nowrap"><?= $eur($r['importo']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-right">
                    <?= Html::a('Vai ai documenti <i class="fas fa-arrow-right"></i>', ['mgdocumento/index']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mostra['ordini']): ?>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-outline card-primary shadow-sm h-100 dash-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cart-shopping text-primary"></i> Ordini aperti</h3>
                    <div class="card-tools">
                        <span class="badge badge-primary"><?= $num($ordiniTotali) ?></span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="px-3 py-2 border-bottom d-flex justify-content-between dash-stat">
                        <span class="text-muted">Cliente: <b><?= $num($or['cliente']['n']) ?></b> · <?= $eur($or['cliente']['totale']) ?></span>
                        <span class="text-muted">Fornitore: <b><?= $num($or['fornitore']['n']) ?></b> · <?= $eur($or['fornitore']['totale']) ?></span>
                    </div>
                    <div class="px-3 py-2 border-bottom dash-stat text-muted">
                        Da evadere: <b><?= $num($or['da_evadere']) ?></b> pz ·
                        impegnati: <b><?= $num($or['impegnato']) ?></b> pz
                    </div>
                    <?php if (empty($or['lista'])): ?>
                        <div class="dash-empty">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                            Nessun ordine aperto.
                        </div>
                    <?php else: ?>
                        <table class="table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Documento</th>
                                    <th>Anagrafica</th>
                                    <th>Stato</th>
                                    <th class="text-right">Totale</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($or['lista'] as $r): ?>
                                <tr>
                                    <td class="text-nowrap">
                                        <?= Html::a(Html::encode($etichettaDoc($r)), ['mgdocumento/view', 'id' => $r['id']]) ?><br>
                                        <small class="text-muted"><?= Html::encode($data($r['data'])) ?></small>
                                    </td>
                                    <td><?= Html::encode($r['ragione_sociale'] ?: '—') ?></td>
                                    <td><?= $badgeStatoDoc($r['stato']) ?></td>
                                    <td class="text-right text-nowrap"><?= $eur($r['totale']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-right">
                    <?= Html::a('Vai agli ordini <i class="fas fa-arrow-right"></i>', ['mgdocumento/index']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mostra['giacenze']): ?>
        <div class="col-lg-4 col-md-12 mb-3">
            <div class="card card-outline card-primary shadow-sm h-100 dash-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-boxes-stacked text-warning"></i> Giacenze sotto zero</h3>
                    <div class="card-tools">
                        <span class="badge badge-warning"><?= $num($gi['sotto_zero']) ?> articoli</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($gi['lista'])): ?>
                        <div class="dash-empty">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                            Nessun articolo con giacenza negativa.
                        </div>
                    <?php else: ?>
                        <table class="table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Codice</th>
                                    <th>Descrizione</th>
                                    <th class="text-right">Giacenza</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($gi['lista'] as $r): ?>
                                <tr>
                                    <td class="text-nowrap"><?= Html::encode($r['codice_articolo']) ?></td>
                                    <td><?= Html::encode($r['descrizione'] ?: '—') ?></td>
                                    <td class="text-right text-nowrap font-weight-bold text-danger">
                                        <?= $f->asDecimal($r['giacenza']) ?>
                                        <?php if (!empty($r['um'])): ?>
                                            <small class="text-muted font-weight-normal"><?= Html::encode($r['um']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-right">
                    <?= Html::a('Vai agli articoli <i class="fas fa-arrow-right"></i>', ['mgarticolo/index']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ============== PLANNING · TODO · VTIGER ============== -->
    <?php if ($mostra['planning'] || $mostra['todo'] || $mostra['vtiger']): ?>
    <div class="row">

        <?php if ($mostra['planning']): ?>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-outline card-primary shadow-sm h-100 dash-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calendar-day text-success"></i> Planning &amp; presenze di oggi</h3>
                    <div class="card-tools">
                        <span class="badge badge-success"><?= $num($pl['oggi']) ?> attività</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if ($pl['con_planning']): ?>
                        <div class="px-3 py-2 border-bottom dash-stat text-muted">
                            Settimana: <b><?= $num($pl['settimana']) ?></b> attività
                        </div>
                    <?php endif; ?>
                    <?php if ($pl['con_presenze']): ?>
                        <div class="px-3 py-2 border-bottom dash-stat text-muted">
                            Presenze di oggi: <b><?= $num($pl['presenze']) ?></b> (<?= $f->asDecimal($pl['ore'], 1) ?> h) su
                            <b><?= $num($pl['persone']) ?></b> persone
                        </div>
                    <?php endif; ?>
                    <?php if (!$pl['con_planning']): ?>
                        <div class="dash-empty">
                            <i class="fas fa-users fa-2x mb-2 d-block text-muted"></i>
                            Solo le presenze sono visibili: il calendario non è accessibile.
                        </div>
                    <?php elseif (empty($pl['lista'])): ?>
                        <div class="dash-empty">
                            <i class="fas fa-mug-hot fa-2x mb-2 d-block text-muted"></i>
                            Nessuna attività pianificata per oggi.
                        </div>
                    <?php else: ?>
                        <table class="table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Orario</th>
                                    <th>Attività</th>
                                    <th>Stato</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($pl['lista'] as $r): ?>
                                <tr>
                                    <td class="text-nowrap">
                                        <?= Html::encode($orario($r['ora_inizio'])) ?>–<?= Html::encode($orario($r['ora_fine'])) ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($r['descrizione'])): ?>
                                            <?= Html::encode(mb_substr($r['descrizione'], 0, 60)) ?><br>
                                        <?php endif; ?>
                                        <small class="text-muted">
                                            <?php
                                            $dettagli = array_filter([trim($r['persona'] ?? ''), trim($r['targa'] ?? '')]);
                                            echo Html::encode($dettagli ? implode(' · ', $dettagli) : '—');
                                            ?>
                                        </small>
                                    </td>
                                    <td><?= $badgeStatoPlanning($r['stato_completamento']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <?php if ($vis['planning']): ?>
                <div class="card-footer text-right">
                    <?= Html::a('Vai al calendario <i class="fas fa-arrow-right"></i>', ['planning/index']) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mostra['todo']): ?>
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="card card-outline card-primary shadow-sm h-100 dash-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-check-square text-info"></i> ToDo</h3>
                    <div class="card-tools">
                        <span class="badge badge-info"><?= $num($td['aperti'] + $td['attesa']) ?> attivi</span>
                        <?php if ($td['scaduti'] > 0): ?>
                            <span class="badge badge-danger"><?= $num($td['scaduti']) ?> scaduti</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="px-3 py-2 border-bottom dash-stat text-muted">
                        <?php foreach ($td['stati'] as $i => $s): ?>
                            <?= $i > 0 ? '·' : '' ?>
                            <b><?= $num($s['n']) ?></b> <?= Html::encode(strtolower($s['label'])) ?>
                        <?php endforeach; ?>
                    </div>
                    <?php if (empty($td['lista'])): ?>
                        <div class="dash-empty">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                            Nessun to-do aperto.
                        </div>
                    <?php else: ?>
                        <table class="table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Attività</th>
                                    <th>Scadenza</th>
                                    <th>Priorità</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($td['lista'] as $r): ?>
                                <tr>
                                    <td><?= Html::encode(mb_substr(trim($r['descrizione']), 0, 55)) ?></td>
                                    <td class="text-nowrap">
                                        <?php if ($r['data_scadenza']): ?>
                                            <?php
                                            $scaduto = strtotime($r['data_scadenza']) < strtotime(date('Y-m-d'));
                                            ?>
                                            <span class="badge <?= $scaduto ? 'badge-danger' : 'badge-light' ?>">
                                                <?= Html::encode($data($r['data_scadenza'])) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php $p = trim((string) $r['priorita']); ?>
                                        <?php if ($p === '1'): ?>
                                            <span class="badge badge-danger">alta</span>
                                        <?php elseif ($p === '2'): ?>
                                            <span class="badge badge-secondary">bassa</span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-right">
                    <?= Html::a('Vai alla board <i class="fas fa-arrow-right"></i>', ['todomain/board']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mostra['vtiger']): ?>
        <div class="col-lg-4 col-md-12 mb-3">
            <div class="card card-outline card-primary shadow-sm h-100 dash-card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-headset text-secondary"></i> Vtiger</h3>
                    <?php if ($vt['disponibile']): ?>
                        <div class="card-tools">
                            <span class="badge badge-secondary"><?= $num($vt['ticket']) ?> ticket</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <?php if (!$vt['disponibile']): ?>
                        <div class="dash-empty">
                            <i class="fas fa-plug-circle-xmark fa-2x mb-2 d-block text-muted"></i>
                            Database Vtiger non raggiungibile.
                        </div>
                    <?php else: ?>
                        <div class="row no-gutters">
                            <div class="col-6 p-3 text-center border-right">
                                <div class="dash-label text-muted">Ticket aperti</div>
                                <div class="dash-num text-primary"><?= $num($vt['ticket']) ?></div>
                                <div class="dash-sub text-muted">non chiusi</div>
                            </div>
                            <div class="col-6 p-3 text-center">
                                <div class="dash-label text-muted">Progetti in corso</div>
                                <div class="dash-num text-success"><?= $num($vt['progetti']) ?></div>
                                <div class="dash-sub text-muted">iniziati o in avanzamento</div>
                            </div>
                        </div>
                        <div class="px-3 py-2 border-top border-bottom dash-stat text-muted">
                            <?= $num($vt['utenti']) ?> utenti attivi su Vtiger · dati letti dal crm
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-right">
                    <?= Html::a('Ticket <i class="fas fa-arrow-right"></i>', ['vtiger/ticket']) ?>
                    <span class="text-muted mx-1">·</span>
                    <?= Html::a('Progetti <i class="fas fa-arrow-right"></i>', ['vtiger/progetti']) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>
