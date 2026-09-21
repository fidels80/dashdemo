<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '';

// Caricamento librerie CSS/JS (incluse quelle per i bottoni)
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

// Statistiche rapide
$totaleAttivita = count($dati);
// Estraiamo quante targhe UNICHE sono state usate nel periodo
$veicoliImpiegati = count(array_unique(array_filter(array_column($dati, 'targa'))));
?>

<div class="report-planning">
<div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0 text-dark">
            <i class="fa fa-user-clock text-primary"></i> <?= Html::encode('Report Attività Flotta') ?>
        </h2>
        <?= Html::a('<i class="fa fa-arrow-left"></i> Torna alla Dashboard Report', ['report/index'], ['class' => 'btn btn-secondary']) ?>
    </div>
    <div class="card shadow-sm mb-4 border-primary">
    <div class="card shadow-sm mb-4 border-success">
        <div class="card-header bg-success text-white">
            <h5 class="m-0"><i class="fa fa-filter"></i> Filtri Report Planning</h5>
        </div>
        <div class="card-body bg-light">
            <?= Html::beginForm(['report/planning'], 'get', ['id' => 'form-report']) ?>
            <div class="row align-items-end">
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Dal</label>
                    <?= Html::textInput('dal', $dal, ['type' => 'date', 'class' => 'form-control']) ?>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Al</label>
                    <?= Html::textInput('al', $al, ['type' => 'date', 'class' => 'form-control']) ?>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Autista / Dipendente</label>
                    <?= Html::dropDownList('personale_id[]', $dipendente_id, $listaDipendenti, ['class' => 'form-control select2-report', 'multiple' => true, 'data-placeholder' => 'Tutti...']) ?>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Veicolo</label>
                    <?= Html::dropDownList('veicolo_id[]', $veicolo_id, $listaVeicoli, ['class' => 'form-control select2-report', 'multiple' => true, 'data-placeholder' => 'Tutti...']) ?>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Stato</label>
                    <?= Html::dropDownList('stato[]', $stato, $listaStati, ['class' => 'form-control select2-report', 'multiple' => true, 'data-placeholder' => 'Tutti...']) ?>
                </div>
                <div class="col-md-2 mb-3 d-grid gap-2">
                    <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Genera</button>
                    <?= Html::a('Reset', ['report/planning'], ['class' => 'btn btn-outline-secondary']) ?>
                </div>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="alert alert-primary border-0 shadow-sm d-flex justify-content-between align-items-center">
                <div><h6 class="m-0 text-muted">Totale Attività nel periodo</h6><h3 class="m-0 text-dark"><?= $totaleAttivita ?> <small>interventi</small></h3></div>
                <i class="fa fa-clipboard-list fa-3x opacity-25"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-warning border-0 shadow-sm d-flex justify-content-between align-items-center">
                <div><h6 class="m-0 text-muted">Veicoli Distinti Impiegati</h6><h3 class="m-0 text-dark"><?= $veicoliImpiegati ?> <small>mezzi</small></h3></div>
                <i class="fa fa-truck fa-3x opacity-25 text-dark"></i>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="report-table" class="table table-striped table-bordered w-100">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Orario</th>
                            <th>Dipendente</th>
                            <th>Veicolo</th>
                            <th>Indirizzo / Luogo</th>
                            <th>Stato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dati as $riga): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($riga['data_attivita'])) ?></td>
                                <td><?= ($riga['ora_inizio'] ? date('H:i', strtotime($riga['ora_inizio'])) : '-') . ' - ' . ($riga['ora_fine'] ? date('H:i', strtotime($riga['ora_fine'])) : '-') ?></td>
                                <td><?= Html::encode($riga['cognome'] . ' ' . $riga['nome']) ?></td>
                                <td>
                                    <?php if($riga['targa']): ?>
                                        <strong><?= Html::encode($riga['targa']) ?></strong> <small>(<?= Html::encode($riga['marca_modello']) ?>)</small>
                                    <?php else: ?>
                                        <span class="text-muted">Nessun veicolo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= Html::encode($riga['indirizzo']) ?></td>
                                <td>
                                    <?php
                                    $coloreBadge = 'secondary';
                                    if ($riga['stato_completamento'] == 'Completato') $coloreBadge = 'success';
                                    if ($riga['stato_completamento'] == 'In Corso') $coloreBadge = 'warning text-dark';
                                    if ($riga['stato_completamento'] == 'Annullato') $coloreBadge = 'danger';
                                    if ($riga['stato_completamento'] == 'Pianificato') $coloreBadge = 'info text-dark';
                                    ?>
                                    <span class="badge bg-<?= $coloreBadge ?>"><?= Html::encode($riga['stato_completamento']) ?></span>
                                </td>
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
        "order": [[0, "desc"], [1, "asc"]] // Ordina per data e poi per orario inizio
    });
});
JS;
$this->registerJs($js);
?>