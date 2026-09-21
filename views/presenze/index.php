<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = ''; // Lasciamo vuoto per evitare il doppio titolo

use app\assets\DataTablesAsset;
DataTablesAsset::register($this);
// 3. CSS Custom per uniformare lo stile (Header chiaro, bottoni e allineamento)
 
?>

<div class="presenze-index">

    <h1><?= Html::encode('Registro Presenze e Costi') ?></h1>

    <?php foreach (Yii::$app->session->getAllFlashes() as $key => $message): ?>
        <div class="alert alert-<?= ($key === 'error') ? 'danger' : 'success' ?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?= Html::encode($message) ?>
        </div>
    <?php endforeach; ?>

    <p><?= Html::a('<i class="fa fa-plus"></i> Registra Presenza', ['create'], ['class' => 'btn btn-success']) ?></p>

    <div class="table-responsive">
        <table id="presenze-table" class="table table-striped table-bordered nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Dipendente</th>
                    <th>Ingresso</th>
                    <th>Uscita</th>
                    <th>Ore</th>
                    <th>Tipo</th>
                    <th>Costo Totale (€)</th>
                    <th class="td-azioni text-center">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($models as $m): ?>
                    <?php
                    $tariffa = ($m->personale) ? $m->personale->tariffa_oraria : 0;
                    $costoRiga = $m->ore_lavorate * $tariffa;
                    $ingresso = $m->ora_ingresso ? date('H:i', strtotime($m->ora_ingresso)) : '-';
                    $uscita = $m->ora_uscita ? date('H:i', strtotime($m->ora_uscita)) : '-';
                    ?>
                    <tr>
                        <td><strong><?= Yii::$app->formatter->asDate($m->data_presenza, 'php:d/m/Y') ?></strong></td>
                        <td><?= $m->personale ? Html::encode($m->personale->cognome . ' ' . $m->personale->nome) : 'N.D.' ?></td>
                        <td><?= Html::encode($ingresso) ?></td>
                        <td><?= Html::encode($uscita) ?></td>
                        <td class="text-center"><?= $m->ore_lavorate ?></td>
                        <td class="text-center">
                            <?php
                            // Decodifica il testo
                            $descrizione = $m->getDescrizionePresenza();

                            $class = 'primary';
                            if ($m->tipo_assenza == 'MAL') $class = 'danger'; // Se usi i codici brevi
                            if ($m->tipo_assenza == 'FER') $class = 'warning text-dark';
                            ?>
                            <span class="btn btn-xs btn-<?= $class ?> disabled" style="opacity:1; min-width: 100px;">
                                <?= Html::encode($descrizione) ?>
                            </span>
                        </td>
                        <td class="text-end costo-cella">
                            € <?= number_format($costoRiga, 2, ',', '.') ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="<?= Url::to(['view', 'id' => $m->id]) ?>" class="btn btn-xs btn-info">
                                    <i class="fa fa-eye"></i> Vedi
                                </a>
                                <a href="<?= Url::to(['update', 'id' => $m->id]) ?>" class="btn btn-xs btn-warning">
                                    <i class="fa fa-edit"></i> Modifica
                                </a>
                                <?= Html::a('<i class="fa fa-trash"></i> Elimina', ['delete', 'id' => $m->id], [
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
    if ($.fn.DataTable.isDataTable('#presenze-table')) {
        $('#presenze-table').DataTable().destroy();
    }

    $('#presenze-table').DataTable({
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
                orientation: 'landscape',
                pageSize: 'A4'
            },
            { extend: 'csv', className: 'btn btn-info', text: '<i class="fa-solid fa-file-csv"></i> CSV' },
            { extend: 'print', className: 'btn btn-primary', text: '<i class="fa-solid fa-print"></i> Stampa' }
        ],
        "pageLength": 25,
        "scrollX": false, "responsive": true, "autoWidth": false,
        "columnDefs": [
           { "width": "350px", "targets": 1 }, // Larghezza dipendente
            { "width": "80px", "targets": 6 },  // STRETTA: Costo Totale
            { "width": "120px", "targets": 7 }  // Azioni
        ],
        "order": [[0, "desc"]] 
    });
});
JS;
$this->registerJs($js);
?>