<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '';
$this->params['breadcrumbs'][] = $this->title;
use app\assets\DataTablesAsset;
DataTablesAsset::register($this);

?>

<div class="reparti-index card p-4 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0 text-primary"><i class="fa fa-layer-group"></i> <?= Html::encode('Gestione Reparti') ?></h1>
        <p class="m-0">
            <?= Html::a('<i class="fa fa-plus"></i> Nuovo Reparto', ['create'], ['class' => 'btn btn-success btn-lg']) ?>
        </p>
    </div>

    <div class="table-responsive">
        <table id="reparti-table" class="table table-striped table-bordered align-middle" style="width:100%">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Codice</th>
                    <th>Descrizione</th>
                    <th style="width: 150px;" class="no-export text-center">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $models = $dataProvider->getModels(); 
                $i = 1;
                foreach ($models as $model): 
                ?>
                    <tr>
                        <td class="text-muted"><?= $i++ ?></td>
                        <td class="fw-bold"><?= Html::encode($model->codice) ?></td>
                        <td><?= Html::encode($model->descrizione) ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <?= Html::a('<i class="fa fa-eye"></i>', ['view', 'id' => $model->codice], ['class' => 'btn btn-outline-primary']) ?>
                                <?= Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $model->codice], ['class' => 'btn btn-outline-secondary']) ?>
                                <?= Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->codice], [
                                    'class' => 'btn btn-outline-danger',
                                    'data' => [
                                        'confirm' => 'Eliminare questo reparto?',
                                        'method' => 'post',
                                    ],
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
$this->registerJs("
    console.log('Inizializzazione Reparti...');
    if (!\$.fn.DataTable.isDataTable('#reparti-table')) {
        \$('#reparti-table').DataTable({
            dom: \"<'row'<'col-md-6'f><'col-md-6 text-end'B>>\" +
                 \"<'row'<'col-md-12'tr>>\" +
                 \"<'row'<'col-md-5'i><'col-md-7'p>>\",
            buttons: [
                { extend: 'copy', className: 'btn btn-sm btn-outline-secondary', text: '<i class=\"fa fa-copy\"></i> Copia' },
                { extend: 'excel', className: 'btn btn-sm btn-outline-success', text: '<i class=\"fa fa-file-excel\"></i> Excel' },
                { extend: 'pdf', className: 'btn btn-sm btn-outline-danger', text: '<i class=\"fa fa-file-pdf\"></i> PDF' }
            ],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json' },
            pageLength: 20,
            columnDefs: [
                { targets: [3], orderable: false }
            ]
        });
    }
");
?>