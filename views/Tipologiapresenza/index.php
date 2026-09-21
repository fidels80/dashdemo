<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = ''; // Lasciamo vuoto per evitare il doppio titolo

use app\assets\DataTablesAsset;

DataTablesAsset::register($this);
// 3. CSS Custom per uniformare lo stile (Header chiaro, bottoni e allineamento)

// 3. CSS Custom per uniformare lo stile (Header chiaro, bottoni e allineamento)
$this->registerCss("
    .dataTables_filter { text-align: left !important; float: left !important; }
    .dataTables_filter label { display: flex; align-items: center; gap: 10px; }
    .dt-buttons { float: right !important; margin-bottom: 15px; }
    #tipologiapresenza-table thead tr { background-color: #f8f9fa !important; color: #212529 !important; }
    #tipologiapresenza-table th { border-bottom: 1px solid #dee2e6 !important; }
    .btn-xs { padding: 4px 10px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px; font-weight: 600; min-width: 90px; justify-content: center; }
    .btn-xs i { font-size: 0.9rem; }
    .td-azioni { min-width: 150px !important; }
    .badge-codice { font-size: 1rem; padding: 6px 10px; }
");
?>

<div class="tipologiapresenza-index">

    <h1><?= Html::encode('Registro Causali Presenze') ?></h1>

    <?php foreach (Yii::$app->session->getAllFlashes() as $key => $message): ?>
        <div class="alert alert-<?= ($key === 'error') ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?= Html::encode($message) ?>
        </div>
    <?php endforeach; ?>

    <p><?= Html::a('<i class="fa fa-plus"></i> Nuova Causale', ['create'], ['class' => 'btn btn-success']) ?></p>

    <div class="table-responsive">
        <table id="tipologiapresenza-table" class="table table-striped table-bordered nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Codice</th>
                    <th>Descrizione</th>
                    <th class="td-azioni text-center">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataProvider->getModels() as $m): ?>
                    <tr>
                        <td class="text-center">
                            <span class="badge bg-dark badge-codice">
                                <?= Html::encode($m->codice) ?>
                            </span>
                        </td>
                        <td><strong><?= Html::encode($m->descrizione) ?></strong></td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="<?= Url::to(['view', 'codice' => $m->codice]) ?>" class="btn btn-xs btn-info">
                                    <i class="fa fa-eye"></i> Vedi
                                </a>
                                <a href="<?= Url::to(['update', 'codice' => $m->codice]) ?>" class="btn btn-xs btn-warning">
                                    <i class="fa fa-edit"></i> Modifica
                                </a>
                                <?= Html::a('<i class="fa fa-trash"></i> Elimina', ['delete', 'codice' => $m->codice], [
                                    'class' => 'btn btn-xs btn-danger',
                                    'data-confirm' => 'Eliminare questa causale?',
                                    'data-method' => 'post',
                                ]) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$js = <<<JS
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#tipologiapresenza-table')) {
        $('#tipologiapresenza-table').DataTable().destroy();
    }

    $('#tipologiapresenza-table').DataTable({
        "dom": '<"row"<"col-md-6"f><"col-md-6"B>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
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
                orientation: 'portrait',
                pageSize: 'A4'
            },
            { extend: 'csv', className: 'btn btn-info', text: '<i class="fa-solid fa-file-csv"></i> CSV' },
            { extend: 'print', className: 'btn btn-primary', text: '<i class="fa-solid fa-print"></i> Stampa' }
        ],
        "pageLength": 25,
        "scrollX": false, "responsive": true, "autoWidth": false,
        "columnDefs": [
            { "width": "150px", "targets": 0 }, // Larghezza codice
            { "width": "120px", "targets": 2 }  // Azioni
        ],
        "order": [[0, "asc"]] // Ordine alfabetico sul codice
    });
});
JS;
$this->registerJs($js);
?>