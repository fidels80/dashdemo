<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Consuntivo Ore per Ditta Esterna';

$this->registerCssFile('https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css');
$this->registerCssFile('https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css');

$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$totaleGiorni = array_sum(array_column($dati, 'giorni_lavorati'));
$numDitte = count($riepilogoDitte);
?>

<div class="report-consuntivo-ditta">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0 text-dark">
            <i class="fa fa-building text-warning"></i> <?= Html::encode($this->title) ?>
        </h2>
        <?= Html::a('<i class="fa fa-arrow-left"></i> Torna alla Dashboard Report', ['report/index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <!-- Filtri -->
    <div class="card shadow-sm mb-4 border-warning">
        <div class="card-header bg-warning text-dark">
            <h5 class="m-0"><i class="fa fa-filter"></i> Filtri</h5>
        </div>
        <div class="card-body bg-light">
            <?= Html::beginForm(['report/consuntivo-ditta'], 'get', ['id' => 'form-report']) ?>
            <div class="row align-items-end">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Ditta Esterna</label>
                    <?= Html::dropDownList('ditta_esterna[]', $dittaEsterna, $listaDitte, [
                        'class' => 'form-control select2-report',
                        'multiple' => true,
                        'data-placeholder' => 'Tutte le ditte...'
                    ]) ?>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Dal</label>
                    <?= Html::textInput('dal', $dal, ['type' => 'date', 'class' => 'form-control']) ?>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Al</label>
                    <?= Html::textInput('al', $al, ['type' => 'date', 'class' => 'form-control']) ?>
                </div>
                <div class="col-md-2 mb-3 d-grid gap-2">
                    <button type="submit" class="btn btn-warning"><i class="fa fa-search"></i> Genera</button>
                    <?= Html::a('Reset', ['report/consuntivo-ditta'], ['class' => 'btn btn-outline-secondary']) ?>
                </div>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <!-- KPI Globali -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="alert alert-warning border-0 shadow-sm d-flex justify-content-between align-items-center">
                <div><h6 class="m-0 text-muted">Ditte Estrane</h6><h3 class="m-0 text-dark"><?= $numDitte ?></h3></div>
                <i class="fa fa-building fa-3x opacity-25"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="alert alert-primary border-0 shadow-sm d-flex justify-content-between align-items-center">
                <div><h6 class="m-0 text-muted">Totale Ore</h6><h3 class="m-0 text-dark"><?= number_format($totaleOreGlobali, 1, ',', '.') ?> <small>h</small></h3></div>
                <i class="fa fa-clock fa-3x opacity-25"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center">
                <div><h6 class="m-0 text-muted">Dipendenti</h6><h3 class="m-0 text-dark"><?= $totaleDipendentiGlobali ?> <small>persone</small></h3></div>
                <i class="fa fa-users fa-3x opacity-25"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="alert alert-success border-0 shadow-sm d-flex justify-content-between align-items-center">
                <div><h6 class="m-0 text-muted">Costo Totale</h6><h3 class="m-0 text-dark">€ <?= number_format($totaleCostoGlobale, 2, ',', '.') ?></h3></div>
                <i class="fa fa-euro-sign fa-3x opacity-25"></i>
            </div>
        </div>
    </div>

    <!-- Riepilogo per Ditta Esterna -->
    <?php if (!empty($riepilogoDitte)): ?>
    <div class="card shadow-sm mb-4 border-warning">
        <div class="card-header bg-dark text-white">
            <h5 class="m-0"><i class="fa fa-chart-bar"></i> Riepilogo per Ditta Esterna</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Ditta Esterna</th>
                            <th class="text-center">Dipendenti</th>
                            <th class="text-center">Giornate</th>
                            <th class="text-end">Ore Totali</th>
                            <th class="text-end">Costo Totale (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riepilogoDitte as $codice => $riga): ?>
                            <tr>
                                <td><strong><?= Html::encode($riga['descrizione']) ?></strong></td>
                                <td class="text-center"><span class="badge bg-info"><?= $riga['totale_dipendenti'] ?></span></td>
                                <td class="text-center"><span class="badge bg-secondary"><?= $riga['totale_giorni'] ?></span></td>
                                <td class="text-end fw-bold"><?= number_format($riga['totale_ore'], 1, ',', '.') ?> h</td>
                                <td class="text-end text-success fw-bold">€ <?= number_format($riga['totale_costo'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Dettaglio Attività -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="m-0"><i class="fa fa-list"></i> Dettaglio Attività</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="report-table" class="table table-striped table-bordered w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Giro</th>
                            <th>Dipendente</th>
                            <th>Cliente</th>
                            <th>Indirizzo</th>
                            <th>Note Attività</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datiDettaglio as $riga): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($riga['data_attivita'])) ?></td>
                                <td class="text-center"><?= Html::encode($riga['giro'] ?? '-') ?></td>
                                <td><strong><?= Html::encode($riga['cognome'] . ' ' . $riga['nome']) ?></strong></td>
                                <td><?= Html::encode($riga['cliente'] ?? '-') ?></td>
                                <td><?= Html::encode($riga['indirizzo'] ?? '-') ?></td>
                                <td><small><?= Html::encode($riga['note_attivita'] ?? '-') ?></small></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$(document).ready(function() {
    $('.select2-report').select2({ theme: 'bootstrap-5', width: '100%', allowClear: true });

    $('#report-table').DataTable({
        "dom": "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center justify-content-start'B><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row mt-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
            search: 'Cerca:',
            lengthMenu: 'Mostra _MENU_ record per pagina',
            info: 'Visualizzati da _START_ a _END_ di _TOTAL_ record',
            paginate: { first: 'Inizio', last: 'Fine', next: 'Successivo', previous: 'Precedente' }
        },
        "buttons": [
            { extend: 'copy', className: 'btn btn-secondary', text: '<i class="fa-solid fa-copy"></i> Copia' },
            { extend: 'excel', className: 'btn btn-success', text: '<i class="fa-solid fa-file-excel"></i> Excel' },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-danger',
                text: '<i class="fa-solid fa-file-pdf"></i> PDF',
                orientation: 'landscape',
                pageSize: 'A4'
            },
            { extend: 'csv', className: 'btn btn-info', text: '<i class="fa-solid fa-file-csv"></i> CSV' },
            { extend: 'print', className: 'btn btn-primary', text: '<i class="fa-solid fa-print"></i> Stampa' }
        ],
        "pageLength": 50,
        "order": [[0, "asc"], [1, "asc"]]
    });
});
JS;
$this->registerJs($js);
?>
