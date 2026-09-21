<?php
use yii\helpers\Html;
use app\assets\DataTablesAsset;

DataTablesAsset::register($this);

$this->title ='';
// Recuperiamo il colore scelto dall'utente
$usrgrid = $ris['grid_color'] ?? 'primary';

// CSS per gestire i colori condizionali delle righe
$this->registerCss("
    .row-finished { background-color: rgb(144, 238, 144) !important; }
    .dt-buttons .btn { margin-right: 5px; }
");
?>

<div class="sottocommessa-index">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><?= Html::encode('Sotto Commesse') ?></h1>
        <p>
            <?= Html::a('<i class="fas fa-plus"></i> Create Sottocommessa', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <div class="card shadow-sm p-3">
        <table id="sottocommessa-table" class="table table-bordered table-hover display nowrap" style="width:100%">
            <thead>
                <tr class="bg-<?= $usrgrid ?> text-white">
                    <th>Commessa</th>
                    <th>SottoCommessa</th>
                    <th>Descrizione</th>
                    <th>Descrizione Breve</th>
                    <th>Inizio</th>
                    <th>Fine Presunta</th>
                    <th>Fine Reale</th>
                    <th class="text-center">Modi.</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dataProvider->getModels() as $model): ?>
                    <?php 
                        // Logica colore riga (verde se conclusa)
                        $isFinished = (!is_null($model->DataFinePresunta) || !is_null($model->DataFineReale));
                        $rowClass = $isFinished ? 'row-finished' : '';
                    ?>
                    <tr class="<?= $rowClass ?>">
                        <td><?= Html::encode($model->Cd_DOCommessa) ?></td>
                        <td><?= Html::encode($model->Cd_DOSottoCommessa) ?></td>
                        <td><?= Html::encode($model->Descrizione) ?></td>
                        <td><?= Html::encode($model->DescrizioneBreve) ?></td>
                        <td><?= $model->DataInizio ? date('d/m/Y', strtotime($model->DataInizio)) : '' ?></td>
                        <td><?= $model->DataFinePresunta ? date('d/m/Y', strtotime($model->DataFinePresunta)) : '' ?></td>
                        <td><?= $model->DataFineReale ? date('d/m/Y', strtotime($model->DataFineReale)) : '' ?></td>
                        <td class="text-center">
                            <?= Html::a('<i class="fa-solid fa-pen"></i>', ['update', 'id' => $model->Cd_DOSottoCommessa], [
                                'class' => 'btn btn-sm btn-outline-dark',
                                'title' => 'Modifica'
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// 1. FORZIAMO I CARICAMENTI (Buttons, JSZip per Excel, PDFMake per PDF)
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js');
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js');
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// 2. JAVASCRIPT BLINDATO (Con backslash davanti ai $)
$this->registerJs("
    if (!\$.fn.DataTable.isDataTable('#sottocommessa-table')) {
        \$('#sottocommessa-table').DataTable({
            // dom: f = ricerca, B = bottoni, t = tabella, i = info, p = paginazione
            dom: \"<'row'<'col-md-6'f><'col-md-6 text-end'B>>\" +
                 \"<'row'<'col-md-12'tr>>\" +
                 \"<'row'<'col-md-5'i><'col-md-7'p>>\",
            buttons: [
                { extend: 'copy', className: 'btn btn-sm btn-secondary', text: '<i class=\"fa fa-copy\"></i> Copia' },
                { extend: 'excel', className: 'btn btn-sm btn-success', text: '<i class=\"fa fa-file-excel\"></i> Excel' },
                { extend: 'pdf', className: 'btn btn-sm btn-danger', text: '<i class=\"fa fa-file-pdf\"></i> Pdf' },
                { extend: 'print', className: 'btn btn-sm btn-dark', text: '<i class=\"fa fa-print\"></i> Stampa' }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json'
            },
            responsive: true,
            pageLength: 25,
            order: [[0, 'asc']], 
            columnDefs: [
                { targets: -1, orderable: false } 
            ]
        });
    }
");
?>