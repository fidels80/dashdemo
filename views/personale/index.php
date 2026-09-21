<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '';

// 1. Registrazione CSS
$this->registerCssFile('https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css');
$this->registerCssFile('https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');

// 2. Registrazione JS (con dipendenze corrette)
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js', ['depends' => [\yii\web\JqueryAsset::class]]);
// 3. CSS Custom per pulizia e bottoni
$this->registerCss("
    .dataTables_filter { text-align: left !important; float: left !important; }
    .dt-buttons { float: right !important; margin-bottom: 15px; }
    .btn-xs { padding: 4px 10px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px; font-weight: 600; min-width: 90px; justify-content: center; }
    .btn-xs i { font-size: 0.9rem; }
    #personale-table th { background-color: #f8f9fa; white-space: nowrap; }
    .td-azioni { min-width: 250px !important; }
");
?>

<div class="personale-index card p-4 shadow-sm">

    <h1>Anagrafica Personale</h1>

    <?php foreach (Yii::$app->session->getAllFlashes() as $key => $message): ?>
        <div class="alert alert-<?= ($key === 'error') ? 'danger' : 'success' ?> alert-dismissible" role="alert">
            <?= Html::encode($message) ?>
        </div>
    <?php endforeach; ?>

    <p><?= Html::a('<i class="fa fa-plus"></i> Nuovo Dipendente', ['create'], ['class' => 'btn btn-success btn-lg']) ?></p>

    <div class="table-responsive">
        <table id="personale-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>Cognome</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Cellulare</th>
                    <th>Città</th>
                    <th>Ruolo</th>
                    <th>Reparto</th>
                    <th>Mansione</th>
                    <th>Tariffa (€/h)</th>
                    <th>Stato</th>
                    <th class="td-azioni">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($models as $model): ?>
                    <tr>
                        <td><strong><?= Html::encode($model->cognome) ?></strong></td>
                        <td><?= Html::encode($model->nome) ?></td>
                        <td><?= $model->email ? Html::encode($model->email) : '-' ?></td>
                        <td><?= Html::encode($model->cellulare) ?></td>
                        <td><?= Html::encode($model->citta) ?></td>
                        <td><?= Html::encode($model->getDescrizioneRuolo()) ?></td>
                        <td><?= Html::encode($model->getDescrizioneReparto()) ?></td>
                        <td><?= Html::encode($model->getDescrizioneMansione()) ?></td>

                        <td class="text-end"><?= number_format($model->tariffa_oraria, 2, ',', '.') ?></td>
                        <td class="text-center">
                            <span class="btn btn-xs btn-<?= $model->stato_attivo ? 'success' : 'secondary' ?> disabled" style="opacity:1">
                                <i class="fa fa-<?= $model->stato_attivo ? 'check' : 'times' ?>"></i>
                                <?= $model->stato_attivo ? 'Attivo' : 'Inattivo' ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" class="btn btn-xs btn-info">
                                    <i class="fa fa-eye"></i> Vedi
                                </a>
                                <a href="<?= Url::to(['update', 'id' => $model->id]) ?>" class="btn btn-xs btn-warning">
                                    <i class="fa fa-edit"></i> Modifica
                                </a>
                                <?= Html::a('<i class="fa fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-danger',
                                    'data-confirm' => 'Sei sicuro?',
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
    if ($.fn.DataTable.isDataTable('#personale-table')) {
        $('#personale-table').DataTable().destroy();
    }

    var table = $('#personale-table').DataTable({
        "dom": '<"row"<"col-md-6"f><"col-md-6"B>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        language: {
            search: 'Cerca:',
            lengthMenu: 'Mostra _MENU_ record per pagina',
            info: 'Visualizzati da _START_ a _END_ di _TOTAL_ record',
            paginate: { first: 'Inizio', last: 'Fine', next: 'Successivo', previous: 'Precedente' }
        },
        "buttons": [
            { 
                extend: 'copy', 
                className: 'btn btn-secondary', 
                text: '<i class="fa-solid fa-copy"></i> Copia'
            },
            { 
                extend: 'excel', 
                className: 'btn btn-success', 
                text: '<i class="fa-solid fa-file-excel"></i> Excel' 
            },
            { 
                extend: 'pdfHtml5', 
                className: 'btn btn-danger', 
                text: '<i class="fa-solid fa-file-pdf"></i> PDF',
                orientation: 'landscape', // Imposta orizzontale
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible' // Esporta solo le colonne visibili
                },
                customize: function (doc) {
                    doc.defaultStyle.fontSize = 8; // Riduciamo un po' il font per farlo stare tutto
                    doc.styles.tableHeader.fontSize = 9;
                }
            },
            { 
                extend: 'csv', 
                className: 'btn btn-info', 
                text: '<i class="fa-solid fa-file-csv"></i> CSV' 
            },
            { 
                extend: 'print', 
                className: 'btn btn-primary', 
                text: '<i class="fa-solid fa-print"></i> Stampa' 
            }
        ],
        "paging": true,
        "pageLength": 25,
        "scrollX": true, 
        "responsive": false, 
        "ordering": true,
        "order": [[ 0, "asc" ]]
    });
});
JS;
$this->registerJs($js);
?>