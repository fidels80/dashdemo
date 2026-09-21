<?php
use yii\helpers\Html;
use app\assets\DataTablesAsset;

DataTablesAsset::register($this);
$this->title = 'Logs';
?>

<div class="log-index">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="card shadow-sm p-3">
        <table id="logs-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr class="bg text-white">
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Operazione</th>
                    <th>Valore</th>
                    <th>Old Valore</th>
                    <th>Data</th>
                    <th>Azioni</th>
                </tr>
            </thead>
            <tbody>
              <?php
$this->registerCss("
    .json-container {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        padding: 8px;
        max-height: 150px;
        max-width: 400px;
        overflow: auto;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.85rem;
        white-space: pre; /* Mantiene l'indentazione */
        color: #d63384; /* Colore stile codice */
    }
");
?>
<?php foreach ($dataProvider->getModels() as $model): ?>
    <tr>
        <td><?= $model->id ?></td>
        <td><?= $model->userid ?></td>
        <td><?= Html::encode($model->operazione) ?></td>
        
        <td>
            <div class="json-container"><?php
                $val = json_decode($model->valore);
                // Se è un JSON valido lo stampiamo bello, altrimenti stampiamo il testo originale
                echo $val ? Html::encode(json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) : Html::encode($model->valore);
            ?></div>
        </td>

        <td>
            <div class="json-container"><?php
                $oldVal = json_decode($model->old_valore);
                echo $oldVal ? Html::encode(json_encode($oldVal, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) : Html::encode($model->old_valore);
            ?></div>
        </td>

        <td><?= date('d-m-Y H:i', strtotime($model->timeins)) ?></td>
        <td>
            <?= Html::a('<i class="fa fa-eye"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-sm btn-info']) ?>
        </td>
    </tr>
<?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$this->registerJs("
    $('#logs-table').DataTable({
        dom: 'Bfrtip', // B abilita i Buttons
        buttons: [
            { extend: 'copy', className: 'btn btn-secondary' },
            { extend: 'csv', className: 'btn btn-info' },
            { extend: 'excel', className: 'btn btn-success' ,exportOptions: {
            stripHtml: true, // Rimuove i tag HTML dei div
            trim: true       // Rimuove spazi bianchi extra
        }},
            { extend: 'pdf', className: 'btn btn-danger' },
            { extend: 'print', className: 'btn btn-dark' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json'
        },
        order: [[0, 'desc']],
        pageLength: 20
    });
");
?>