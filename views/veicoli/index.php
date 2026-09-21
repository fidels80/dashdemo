<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = ''; // Pulizia titolo come anagrafica

use app\assets\DataTablesAsset;

DataTablesAsset::register($this);
// 3. CSS Custom per uniformare lo stile (Header chiaro, bottoni e allineamento)

// 3. CSS Custom per uniformare lo stile
$this->registerCss("
    .dataTables_filter { text-align: left !important; float: left !important; }
    .dataTables_filter label { display: flex; align-items: center; gap: 10px; }
    .dt-buttons { float: right !important; margin-bottom: 15px; }
    #veicoli-table thead tr { background-color: #f8f9fa !important; color: #212529 !important; }
    .btn-xs { padding: 4px 10px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px; font-weight: 600; min-width: 90px; justify-content: center; }
    .td-azioni { min-width: 250px !important; }
    .scadenza-alert { color: #d9534f; font-weight: bold; }
    .scadenza-warning { color: #f0ad4e; font-weight: bold; }
");
$oggi = time();
$soglia_preavviso = strtotime('+30 days');

// Funzione helper per il colore delle date nell'index
$getColorDate = function ($dataStr) use ($oggi, $soglia_preavviso) {
    if (!$dataStr) return '';
    $ts = strtotime($dataStr);
    if ($ts < $oggi) return 'text-danger font-weight-bold'; // SCADUTA
    if ($ts <= $soglia_preavviso) return 'text-warning font-weight-bold'; // SCADE BREVE (30gg)
    return 'text-success font-weight-bold'; // OK
};
?>

<div class="veicoli-index card p-4 shadow-sm">

    <h1>Gestione Flotta Veicoli</h1>

    <?php foreach (Yii::$app->session->getAllFlashes() as $key => $message): ?>
        <div class="alert alert-<?= ($key === 'error') ? 'danger' : 'success' ?> alert-dismissible" role="alert">
            <?= Html::encode($message) ?>
        </div>
    <?php endforeach; ?>

    <p><?= Html::a('<i class="fa fa-plus"></i> Nuovo Veicolo', ['create'], ['class' => 'btn btn-success']) ?></p>

    <div class="table-responsive">
        <table id="veicoli-table" class="table table-striped table-bordered nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Targa</th>
                    <th>Marca/Modello</th>
                    <th>Stato</th>
                    <th>Assicurazione</th>
                    <th>Revisione</th>
                    <th>ZTL</th>
                    <th>KM Attuali</th>
                    <th class="td-azioni text-center">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($models as $model): ?>
                    <?php
                    // Logica colori scadenze (30gg di preavviso)
                    $oggi = time();
                    $limite = strtotime('+30 days');

                    $scadAss = strtotime($model->scadenza_assicurazione);
                    $classAss = ($scadAss < $oggi) ? 'scadenza-alert' : (($scadAss < $limite) ? 'scadenza-warning' : '');

                    $scadRev = strtotime($model->scadenza_revisione);
                    $classRev = ($scadRev < $oggi) ? 'scadenza-alert' : (($scadRev < $limite) ? 'scadenza-warning' : '');
                    ?>
                    <td><strong><?= Html::encode($model->targa) ?></strong></td>
                    <td><?= Html::encode($model->marca_modello) ?></td>
                    <td class="text-center"><?= $model->getStatoBadge() ?></td>

                    <td class="<?= $getColorDate($model->scadenza_assicurazione) ?>">
                        <?= $model->scadenza_assicurazione ? Yii::$app->formatter->asDate($model->scadenza_assicurazione, 'php:d/m/Y') : '-' ?>
                    </td>

                    <td class="<?= $getColorDate($model->scadenza_revisione) ?>">
                        <?= $model->scadenza_revisione ? Yii::$app->formatter->asDate($model->scadenza_revisione, 'php:d/m/Y') : '-' ?>
                    </td>

                    <td class="<?= $getColorDate($model->scadenza_ztl) ?>">
                        <?= $model->scadenza_ztl ? Yii::$app->formatter->asDate($model->scadenza_ztl, 'php:d/m/Y') : '-' ?>
                    </td>

                    <td class="text-end"><?= number_format($model->ultimo_km, 0, ',', '.') ?></td>

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
                                'data-confirm' => 'Eliminare questa registrazione?',
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
    if ($.fn.DataTable.isDataTable('#veicoli-table')) {
        $('#veicoli-table').DataTable().destroy();
    }

    $('#veicoli-table').DataTable({
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