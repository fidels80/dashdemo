<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Rapporto Attività per Squadra';

$this->registerCssFile('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');

$this->registerJsFile('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$groupColors = ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#e67e22', '#34495e', '#16a085', '#c0392b'];

$this->registerCss("
    .rapporto-header { background: linear-gradient(135deg, #2c3e50 0%, #27ae60 100%); color: #fff; border-radius: 12px; padding: 24px 28px; margin-bottom: 24px; }
    .rapporto-header h2 { margin: 0; font-weight: 700; }
    .rapporto-header .subtitle { opacity: 0.85; font-size: 0.85rem; margin-top: 4px; }
    .rapporto-header .stats { display: flex; gap: 16px; margin-top: 14px; flex-wrap: wrap; }
    .rapporto-header .stat-item { background: rgba(255,255,255,0.15); border-radius: 8px; padding: 8px 16px; font-size: 0.82rem; backdrop-filter: blur(4px); }
    .rapporto-header .stat-item strong { font-size: 1.1rem; display: block; }

    .group-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 16px; border: 1px solid #e9ecef; overflow: hidden; transition: box-shadow 0.2s; }
    .group-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,0.1); }
    .group-header { display: flex; align-items: center; gap: 12px; padding: 14px 18px; cursor: pointer; user-select: none; transition: background 0.15s; }
    .group-header:hover { background: #f8f9fa; }
    .group-toggle { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 0.7rem; font-weight: 700; flex-shrink: 0; transition: transform 0.3s; }
    .group-card.collapsed .group-toggle { transform: rotate(-90deg); }
    .group-info { flex: 1; min-width: 0; }
    .group-title { font-weight: 700; font-size: 0.95rem; color: #2c3e50; }
    .group-meta { font-size: 0.78rem; color: #6c757d; margin-top: 2px; }
    .group-meta i { margin-right: 4px; }
    .group-badge { background: #e9ecef; color: #6c757d; font-size: 0.73rem; font-weight: 600; padding: 4px 12px; border-radius: 20px; white-space: nowrap; }
    .group-body { overflow: hidden; transition: max-height 0.4s ease, opacity 0.3s ease; max-height: 5000px; opacity: 1; }
    .group-card.collapsed .group-body { max-height: 0 !important; opacity: 0; }

    .rapporto-table { width: 100%; border-collapse: collapse; font-size: 0.84rem; }
    .rapporto-table thead th { background: #f8f9fa; color: #6c757d; font-weight: 600; font-size: 0.73rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 12px; border-bottom: 2px solid #dee2e6; }
    .rapporto-table tbody tr { transition: background 0.15s; }
    .rapporto-table tbody tr:hover { background: #f0f7ff !important; }
    .rapporto-table td { padding: 9px 12px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
    .rapporto-table .td-center { text-align: center; font-weight: 700; }
    .rapporto-table .td-orario { white-space: nowrap; font-weight: 600; }
    .rapporto-table .td-indirizzo { max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .rapporto-table .td-note { font-size: 0.78rem; color: #6c757d; font-style: italic; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .stato-pill { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.3px; }

    .toolbar-ricerca { position: sticky; top: 0; z-index: 50; background: #fff; border-bottom: 1px solid #dee2e6; padding: 12px 0; margin-bottom: 16px; }
    .toolbar-ricerca input { width: 100%; padding: 8px 12px 8px 36px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 0.88rem; outline: none; }
    .toolbar-ricerca input:focus { border-color: #3498db; box-shadow: 0 0 0 3px rgba(52,152,219,0.15); }
    .toolbar-ricerca .search-wrap { position: relative; }
    .toolbar-ricerca .search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6c757d; }

    @media print {
        .toolbar-ricerca, .card-filtri, .btn-expand-collapse, #loadingOverlay { display: none !important; }
        .group-card { break-inside: avoid; box-shadow: none; border: 1px solid #ccc; }
        .group-card.collapsed .group-body { max-height: none !important; opacity: 1 !important; }
        .rapporto-header { background: #2c3e50 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
    #loadingOverlay {
        position: fixed; inset: 0; background: rgba(255,255,255,0.85); z-index: 9999;
        display: none; align-items: center; justify-content: center; flex-direction: column; gap: 16px;
        backdrop-filter: blur(2px);
    }
    #loadingOverlay.active { display: flex; }
    #loadingOverlay .spinner-grow { width: 3rem; height: 3rem; }
");

$totaleAttivita = count($rows);
$totaleSquadre = count($grouped);
$clientiUnici = count(array_unique(array_filter(array_column($rows, 'cliente'))));
?>

<div id="loadingOverlay" class="active">
    <div class="spinner-grow text-success"></div>
    <div class="fw-bold text-secondary">Caricamento attività in corso...</div>
</div>

<div class="rapporto-header">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h2><i class="fa-solid fa-people-group me-2"></i><?= Html::encode($this->title) ?></h2>
            <div class="subtitle">
                Periodo: <?= date('d/m/Y', strtotime($dal)) ?> — <?= date('d/m/Y', strtotime($al)) ?>
                &mdash; Generato il <?= date('d/m/Y H:i') ?>
            </div>
        </div>
        <?= Html::a('<i class="fa fa-arrow-left"></i> Torna alla Dashboard', ['report/index'], ['class' => 'btn btn-sm btn-light']) ?>
    </div>
    <div class="stats">
        <div class="stat-item"><strong><?= $totaleSquadre ?></strong> Squadre</div>
        <div class="stat-item"><strong><?= $totaleAttivita ?></strong> Attività</div>
        <div class="stat-item"><strong><?= $clientiUnici ?></strong> Clienti</div>
    </div>
</div>

<div class="card shadow-sm mb-4 border-success">
    <div class="card-header bg-success text-white">
        <h5 class="m-0"><i class="fa fa-filter"></i> Filtri</h5>
    </div>
    <div class="card-body bg-light">
        <?= Html::beginForm(['report/rapporto-attivita'], 'get') ?>
        <div class="row align-items-end">
            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold small">Dal</label>
                <?= Html::textInput('dal', $dal, ['type' => 'date', 'class' => 'form-control form-control-sm']) ?>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold small">Al</label>
                <?= Html::textInput('al', $al, ['type' => 'date', 'class' => 'form-control form-control-sm']) ?>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold small">Dipendente</label>
                <?= Html::dropDownList('personale_id[]', $dipendente_id, $listaDipendenti, [
                    'class' => 'form-control form-control-sm select2-filtro', 'multiple' => true, 'data-placeholder' => 'Tutti...'
                ]) ?>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold small">Veicolo</label>
                <?= Html::dropDownList('veicolo_id[]', $veicolo_id, $listaVeicoli, [
                    'class' => 'form-control form-control-sm select2-filtro', 'multiple' => true, 'data-placeholder' => 'Tutti...'
                ]) ?>
            </div>
            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold small">Ditta Esterna</label>
                <?= Html::dropDownList('ditta_esterna[]', $dittaEsterna, $listaDitte, [
                    'class' => 'form-control form-control-sm select2-filtro', 'multiple' => true, 'data-placeholder' => 'Tutte...'
                ]) ?>
            </div>
            <div class="col-md-2 mb-3 d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-search me-1"></i> Genera</button>
                <?= Html::a('Reset', ['report/rapporto-attivita'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
            </div>
        </div>
        <?= Html::endForm() ?>
    </div>
</div>

<div class="toolbar-ricerca">
    <div class="container-fluid px-0">
        <div class="row align-items-center">
            <div class="col-md-5">
                <div class="search-wrap">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" id="searchRicerca" placeholder="Cerca indirizzo, cliente, targa, nominativo...">
                </div>
            </div>
            <div class="col-md-7 text-end">
                <div class="d-inline-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" id="btnExpandAll">
                        <i class="fa-solid fa-plus-square me-1"></i> Espandi Tutti
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="btnCollapseAll">
                        <i class="fa-solid fa-minus-square me-1"></i> Comprimi Tutti
                    </button>
                    <button class="btn btn-sm btn-outline-dark" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i> Stampa
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (empty($grouped)): ?>
    <div class="text-center py-5">
        <i class="fa-solid fa-inbox fa-3x text-muted mb-3 d-block"></i>
        <p class="text-muted">Nessuna attività trovata per i filtri selezionati.</p>
    </div>
<?php else: ?>
    <?php foreach ($grouped as $gi => $g):
        $color = $groupColors[$gi % count($groupColors)];
    ?>
    <div class="group-card" data-group>
        <div class="group-header" onclick="toggleGroup(this)">
            <div class="group-toggle" style="background:<?= $color ?>;">
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div class="group-info">
                <div class="group-title"><?= Html::encode($g['nominativi']) ?></div>
                <div class="group-meta">
                    <?php if ($g['targhe']): ?>
                        <i class="fa-solid fa-truck"></i> <?= Html::encode($g['targhe']) ?>
                    <?php endif; ?>
                </div>
            </div>
            <span class="group-badge"><?= count($g['items']) ?> attivit&agrave;</span>
        </div>
        <div class="group-body">
            <table class="rapporto-table">
                <thead>
                    <tr>
                        <th style="width:4%; text-align:center;">Giro</th>
                        <th style="width:10%;">Data</th>
                        <th style="width:10%;">Orario</th>
                        <th style="width:20%;">Cliente</th>
                        <th style="width:26%;">Indirizzo</th>
                        <th style="width:16%;">Note</th>
                        <th style="width:8%;">Stato</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($g['items'] as $r): ?>
                        <tr data-search="<?= Html::encode(strtolower($r['indirizzo'] . ' ' . $r['cliente'] . ' ' . $r['targhe'] . ' ' . $r['nominativi'] . ' ' . $r['note'] . ' ' . $r['ora_inizio'] . ' ' . $r['ora_fine'])) ?>">
                        <td class="td-center"><?= Html::encode($r['giro']) ?></td>
                        <td style="white-space:nowrap;"><?= Html::encode($r['dataFmt']) ?></td>
                        <td class="td-orario"><?= Html::encode($r['ora_inizio'] . ' - ' . $r['ora_fine']) ?></td>
                        <td style="font-weight:600; text-transform:uppercase;"><?= Html::encode($r['cliente']) ?></td>
                        <td class="td-indirizzo" title="<?= Html::encode($r['indirizzo']) ?>"><?= Html::encode($r['indirizzo']) ?></td>
                        <td class="td-note" title="<?= Html::encode($r['note']) ?>"><?= Html::encode($r['note']) ?></td>
                        <td>
                            <span class="stato-pill" style="background:<?= Html::encode($r['stato_colore']) ?>;">
                                <?= Html::encode($r['stato']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php
$js = <<<JS
$(document).ready(function() {
    // Nascondi overlay caricamento
    $('#loadingOverlay').removeClass('active');

    $('.select2-filtro').select2({ theme: 'bootstrap-5', width: '100%', allowClear: true });

    // Mostra spinner alla submit del form filtri
    $('form').on('submit', function() {
        $('#loadingOverlay').addClass('active');
    });

    // Toggle singolo gruppo
    window.toggleGroup = function(header) {
        header.closest('.group-card').classList.toggle('collapsed');
    };

    // Espandi / Comprimi tutti
    $('#btnExpandAll').on('click', function() {
        $('.group-card').removeClass('collapsed');
    });
    $('#btnCollapseAll').on('click', function() {
        $('.group-card').addClass('collapsed');
    });

    // Ricerca live
    $('#searchRicerca').on('input', function() {
        var q = this.value.toLowerCase().trim();
        $('.group-card').each(function() {
            var card = $(this);
            var visibleCount = 0;
            card.find('tbody tr[data-search]').each(function() {
                var match = !q || $(this).data('search').toString().indexOf(q) !== -1;
                $(this).toggle(match);
                if (match) visibleCount++;
            });
            card.toggle(visibleCount > 0 || !q);
            if (q && visibleCount > 0) card.removeClass('collapsed');
            card.find('.group-badge').text(visibleCount + ' attivit\\u00e0');
        });
    });

    // ESC chiude la ricerca
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            var input = $('#searchRicerca');
            if (input.is(':focus')) { input.val('').trigger('input').blur(); }
        }
    });
});
JS;
$this->registerJs($js);
?>
