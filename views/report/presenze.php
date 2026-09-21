<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Report Presenze e Costi';

// Librerie DataTables (già conosciute)
// CSS
$this->registerCssFile('https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css');
$this->registerCssFile('https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css');

// JS Core
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// JS Export (I colpevoli erano qui, mancavano i file per il print e il layout di BS5)
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]); // <-- QUESTO MANCAVA!
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js', ['depends' => [\yii\web\JqueryAsset::class]]); // <-- QUESTO MANCAVA!

$this->registerJsFile('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
// Costo Totale da mostrare nel riepilogo
$totaleCosto = array_sum(array_column($dati, 'costo_totale'));
$totaleOre = array_sum(array_column($dati, 'ore_lavorate'));
?>

<div class="report-presenze">
<div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0 text-dark">
            <i class="fa fa-truck text-success"></i> <?= Html::encode($this->title) ?>
        </h2>
        <?= Html::a('<i class="fa fa-arrow-left"></i> Torna alla Dashboard Report', ['report/index'], ['class' => 'btn btn-secondary']) ?>
    </div>
    <div class="card shadow-sm mb-4 border-success">
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="m-0"><i class="fa fa-filter"></i> Filtri Report</h5>
        </div>
        <div class="card-body bg-light">
            <?= Html::beginForm(['report/presenze'], 'get', ['id' => 'form-report']) ?>
            
            <div class="row align-items-end">
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Dal</label>
                    <?= Html::textInput('dal', $dal, ['type' => 'date', 'class' => 'form-control']) ?>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label fw-bold">Al</label>
                    <?= Html::textInput('al', $al, ['type' => 'date', 'class' => 'form-control']) ?>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Dipendente</label>
                    <?= Html::dropDownList('personale_id[]', $dipendente_id, $listaDipendenti, [
                        'class' => 'form-control select2-report', 
                        'multiple' => true, 
                        'data-placeholder' => 'Tutti i dipendenti...'
                    ]) ?>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Causale</label>
                    <?= Html::dropDownList('causale[]', $causale, $listaCausali, [
                        'class' => 'form-control select2-report', 
                        'multiple' => true, 
                        'data-placeholder' => 'Tutte le causali...'
                    ]) ?>
                </div>
                <div class="col-md-2 mb-3 d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Genera</button>
                    <?= Html::a('Reset', ['report/presenze'], ['class' => 'btn btn-outline-secondary']) ?>
                </div>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="alert alert-info border-0 shadow-sm d-flex justify-content-between align-items-center">
                <div><h6 class="m-0 text-muted">Totale Ore Lavorate</h6><h3 class="m-0 text-dark"><?= number_format($totaleOre, 1, ',', '.') ?> <small>h</small></h3></div>
                <i class="fa fa-clock fa-3x opacity-25"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-success border-0 shadow-sm d-flex justify-content-between align-items-center">
                <div><h6 class="m-0 text-muted">Costo Stimato Totale</h6><h3 class="m-0 text-dark">€ <?= number_format($totaleCosto, 2, ',', '.') ?></h3></div>
                <i class="fa fa-euro-sign fa-3x opacity-25"></i>
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
                            <th>Dipendente</th>
                            <th>In / Out</th>
                            <th>Ore</th>
                            <th>Causale</th>
                            <th>Costo (€)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dati as $riga): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($riga['data_presenza'])) ?></td>
                                <td><strong><?= Html::encode($riga['cognome'] . ' ' . $riga['nome']) ?></strong></td>
                                <td><?= ($riga['ora_ingresso'] ? date('H:i', strtotime($riga['ora_ingresso'])) : '-') . ' - ' . ($riga['ora_uscita'] ? date('H:i', strtotime($riga['ora_uscita'])) : '-') ?></td>
                                <td class="text-center"><?= $riga['ore_lavorate'] ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= Html::encode($riga['tipo_assenza']) ?></span> 
                                    <small><?= Html::encode($riga['desc_causale']) ?></small>
                                </td>
                                <td class="text-end text-success fw-bold"><?= number_format($riga['costo_totale'], 2, ',', '.') ?></td>
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
    // Inizializza Select2
    $('.select2-report').select2({
        theme: 'bootstrap-5',
        width: '100%',
        allowClear: true
    });

    // Inizializza DataTables
    $('#report-table').DataTable({
        // Struttura DOM fissa per far convivere correttamente Bottoni (B) e Ricerca (f) in Bootstrap 5
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
        "order": [[0, "desc"]]
    });
});
JS;
$this->registerJs($js);
?>