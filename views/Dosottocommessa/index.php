<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = '';
$this->params['breadcrumbs'][] = $this->title;
use app\assets\DataTablesAsset;
DataTablesAsset::register($this);


?>

<style>
    .reparti-index { width: 98%; margin: 0 auto; }
    .table-sottocommesse th { background-color: #f8f9fa; font-size: 13px; white-space: nowrap; }
    .table-sottocommesse td { font-size: 13px; vertical-align: middle; }
    .badge-stato { font-size: 11px; padding: 6px; border-radius: 4px; }
    .nowrap { white-space: nowrap; }
</style>

<div class="dosottocommessa-index card p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="m-0 text-primary"><i class="fas fa-list"></i> Riepilogo Sottocommesse</h4>
        <?= Html::a('<i class="fa fa-plus"></i> Crea Nuova', ['create'], ['class' => 'btn btn-success']) ?>
    </div>

    <div class="table-responsive">
        <table id="tabella-sottocommesse" class="table table-striped table-bordered align-middle w-100">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Commessa Padre</th>
                    <th>Cod. Sotto</th>
                    <th>Descrizione</th>
                    <th>Cliente</th>
                    <th>Stato</th>
                    <th class="text-center">Data Inizio</th>
                    <th class="text-center">Data Fine</th>
                    <th class="text-center no-export">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $models = $dataProvider->getModels();
                foreach ($models as $model):
                ?>
                    <tr>
                        <td class="text-muted"><?= $model->Id_DOSottoCommessa ?></td>
                        <td class="fw-bold"><?= Html::encode($model->Cd_DOCommessa) ?></td>
                        <td><?= Html::encode($model->Cd_DOSottoCommessa) ?></td>
                        <td><small><?= Html::encode($model->Descrizione) ?></small></td>
                        <td><?= Html::encode($model->getDescrizioneCliente()) ?></td>
                        <td class="text-center">
                            <span class="badge bg-info text-dark badge-stato">
                                <?= Html::encode($model->getDescrizioneStato()) ?>
                            </span>
                        </td>
                        <td class="text-center nowrap"><?= $model->DataInizio ? date('d/m/Y', strtotime($model->DataInizio)) : '-' ?></td>
                        <td class="text-center nowrap"><?= $model->DataFinePresunta ? date('d/m/Y', strtotime($model->DataFinePresunta)) : '-' ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $model->Cd_DOSottoCommessa], ['class' => 'btn btn-outline-secondary']) ?>
                                <?= Html::a('<i class="fas fa-edit"></i>', ['update', 'id' => $model->Cd_DOSottoCommessa], ['class' => 'btn btn-outline-primary']) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// JAVASCRIPT CORRETTO CON BACKSLASH \$ PER EVITARE CRASH
$this->registerJs("
    if (!\$.fn.DataTable.isDataTable('#tabella-sottocommesse')) {
        var table = \$('#tabella-sottocommesse').DataTable({
            dom: \"<'row'<'col-md-6'f><'col-md-6 text-end'B>>\" +
                 \"<'row'<'col-md-12'tr>>\" +
                 \"<'row'<'col-md-5'i><'col-md-7'p>>\",
            buttons: [
                { 
                    extend: 'copy', 
                    className: 'btn btn-sm btn-outline-secondary', 
                    text: '<i class=\"fas fa-copy\"></i> Copia',
                    exportOptions: { columns: ':not(.no-export)' }
                },
                { 
                    extend: 'excel', 
                    className: 'btn btn-sm btn-outline-success', 
                    text: '<i class=\"fas fa-file-excel\"></i> Excel',
                    exportOptions: { columns: ':not(.no-export)' }
                },
                { 
                    extend: 'pdf', 
                    className: 'btn btn-sm btn-outline-danger', 
                    text: '<i class=\"fas fa-file-pdf\"></i> PDF',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: { columns: ':not(.no-export)' }
                },
                { 
                    extend: 'print', 
                    className: 'btn btn-sm btn-outline-primary', 
                    text: '<i class=\"fas fa-print\"></i> Stampa',
                    exportOptions: { columns: ':not(.no-export)' }
                }
            ],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json' },
            pageLength: 25,
            autoWidth: false,
            responsive: true,
            columnDefs: [
                { width: '50px', targets: 0 },
                { width: '100px', targets: 1 },
                { orderable: false, targets: 8 } // Disabilita ordine su azioni
            ]
        });

        // Forza ricalcolo larghezze
        setTimeout(function() {
            table.columns.adjust().draw();
        }, 300);
    }
");
?>