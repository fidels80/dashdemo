<style>
    .xdivclass {
        width: 100% !important;
        /* Increased from 80% to 95% */
        margin: auto;
        overflow-x: auto;
        padding: 10px;
        /* Add some padding */
    }

    .xdivclass table {
        min-width: 1200px;
        /* Reduced from 1500px to 1200px */
        width: 100%;
    }
</style>
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Json;
/* @var $this yii\web\View */
/* @var $searchModel app\models\XvenueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '';

$this->registerJsFile("https://code.jquery.com/jquery-3.6.0.min.js", [
    'position' => \yii\web\View::POS_HEAD
]);

$this->registerJsFile("https://cdn.datatables.net/2.2.2/js/dataTables.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);
$this->registerJsFile("https://cdn.datatables.net/buttons/3.2.2/js/dataTables.buttons.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdn.datatables.net/buttons/3.2.2/js/buttons.html5.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdn.datatables.net/buttons/3.2.2/js/buttons.print.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdn.datatables.net/fixedheader/4.0.1/js/fixedHeader.dataTables.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerJsFile("https://cdn.datatables.net/colreorder/2.0.4/js/dataTables.colReorder.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerCssFile("https://cdn.datatables.net/fixedheader/4.0.1/css/fixedHeader.dataTables.min.css");
$this->registerCssFile("https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css");
$this->registerCssFile("https://cdn.datatables.net/buttons/3.2.2/css/buttons.dataTables.min.css");
$this->registerCssFile("https://cdn.datatables.net/colreorder/2.0.4/css/colReorder.dataTables.min.css");

// Include Select2 for better dropdowns
$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.full.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);
$this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css");

$this->registerJsFile("https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerCssFile("https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css");

$tableId = 'venue_' . uniqid();
$jsUserLevel = Yii::$app->user->identity->level ?? '';

?>
<BR>
<BR>
<BR>
<BR>
<BR>
<div class="xvenue-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Crea Xvenue', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

</div>
<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger">
        <?= Yii::$app->session->getFlash('error') ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success">
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<?php

// Ottieni i dati raw dal provider
$rawData = $dataProvider->query->all();

// Converti gli oggetti ActiveRecord in array associativi
$flatData = [];
foreach ($rawData as $item) {
    if (is_object($item)) {
        $flatData[] = $item->toArray();
    } elseif (is_array($item)) {
        if (array_keys($item) === range(0, count($item) - 1)) {
            foreach ($item as $subItem) {
                $flatData[] = is_object($subItem) ? $subItem->toArray() : $subItem;
            }
        } else {
            $flatData[] = $item;
        }
    }
}

// Codifica in JSON con gestione degli errori
$filteredDataJson = Json::encode($flatData, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRETTY_PRINT);

// Verifica se la codifica JSON è riuscita
if (json_last_error() !== JSON_ERROR_NONE) {
    throw new \Exception('Errore nella codifica JSON: ' . json_last_error_msg());
}

$this->registerJs("
    $(document).ready(function () {
        console.log('Inizializzazione DataTable per il tavolo con ID: " . $tableId . "');
        
        let rawData = " . $filteredDataJson . ";
        console.log('Dati del provider:', rawData);
        console.log('Numero di elementi:', rawData.length);
        
        if (rawData.length > 0) {
            console.log('Primo elemento:', rawData[0]);
            console.log('Chiavi del primo elemento:', Object.keys(rawData[0]));
        }
        
        let table = $('#" . $tableId . "').DataTable({
            data: rawData,
            responsive: false, // Disable responsive for better horizontal scroll
            fixedHeader: true,
            colReorder: false,
            scrollX: true, // Enable horizontal scrolling
            scrollCollapse: true,
            paging: true,
            pageLength: 20,
            dom: 'Bfrtip',
            autoWidth: false, // Disable auto width calculation
            
            buttons: [
                {
                    extend: 'copyHtml5',
                    text: 'Copia'
                },
                {
                    extend: 'excelHtml5',
                    text: 'Excel'
                },
                {
                    extend: 'csvHtml5',
                    text: 'CSV'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    orientation: 'landscape' // Better for wide tables
                },
                {
                    extend: 'print',
                    text: 'Stampa'
                }
            ],
            
            fixedColumns: {
                leftColumns: 1,
                //rightColumns: 1 // Fix the actions column on the right
            },
            
            columns: [
                            {
                    title: 'Azioni',
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'fixed-actions-column text-center',
                    width: '120px',
                    render: function (data, type, row) {
                        let actions = '<div style=\"white-space: nowrap;\">';
                        
                        // Pulsante View
                        actions += '<a href=\"" . \yii\helpers\Url::to(['view']) . "&id=' + row.id + '\" ' +
                                  'class=\"btn btn-sm btn-primary\" title=\"Visualizza\" style=\"margin: 1px;\">' +
                                  '<i class=\"fas fa-eye\"></i></a>';
                        
                        // Pulsante Update
                        actions += '<a href=\"" . \yii\helpers\Url::to(['update']) . "&id=' + row.id + '\" ' +
                                  'class=\"btn btn-sm btn-info\" title=\"Modifica\" style=\"margin: 1px;\">' +
                                  '<i class=\"fas fa-edit\"></i></a>';
                        
                        // Pulsante Delete
                        actions += '<a href=\"" . \yii\helpers\Url::to(['delete']) . "&id=' + row.id + '\" ' +
                                  'class=\"btn btn-sm btn-danger delete-btn\" title=\"Elimina\" ' +
                                  'data-confirm=\"Sei sicuro di voler eliminare questo elemento?\" ' +
                                  'data-method=\"post\" style=\"margin: 1px;\">' +
                                  '<i class=\"fas fa-trash\"></i></a>';
                        
                        actions += '</div>';
                        return actions;
                    }
                },
                { title: 'Venue', data: 'venue', width: '100px' },
                { title: 'Città', data: 'citta', width: '100px' },
                { title: 'Indirizzo', data: 'indirizzo', width: '150px' },
                { title: 'CAP', data: 'cap', width: '80px' },
                { title: 'Provincia', data: 'provincia', width: '80px' },
                { title: 'Tipologia', data: 'tipologia', width: '100px' },
                { title: 'Capienza', data: 'capienza', width: '80px' },
                { title: 'Sito Web', data: 'sito_web', width: '80px',
                    render: function(data, type, row) {
                        if (data && data.trim() !== '') {
                            return '<a href=\"' + data + '\" target=\"_blank\">Link</a>';
                        }
                        return '';
                    }
                },
                { title: 'Telefono', data: 'telefono', width: '100px' },
                { title: 'Email', data: 'email', width: '50px' },
                { title: 'Note', data: 'note', width: '80px',
                    render: function(data, type, row) {
                        if (data && data.length > 50) {
                            return '<span title=\"' + data + '\">' + data.substring(0, 50) + '...</span>';
                        }
                        return data || '';
                    }
                },
                { title: 'Mappa', data: 'mappa', width: '80px',
                 
                render: function(data, type, row) {
                        if (data && data.trim() !== '') {
                            return '<a href=\"' + data + '\" target=\"_blank\">Link</a>';
                        }
                        return '';
                    }
                
                
                },
                { title: 'Pos', data: 'pos', width: '1px' }

            ],
            
            // Column definitions for better control
            columnDefs: [
                {
                    targets: '_all',
                    className: 'dt-body-nowrap' // Prevent text wrapping
                }
            ]
        });

        // Gestione dei pulsanti di cancellazione
        $('#" . $tableId . "').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            
            let url = $(this).attr('href');
            let confirmMessage = $(this).data('confirm');
            
            if (confirm(confirmMessage)) {
                let form = $('<form>', {
                    'method': 'POST',
                    'action': url
                });
                
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '" . Yii::$app->request->csrfParam . "',
                    'value': '" . Yii::$app->request->csrfToken . "'
                }));
                
                $('body').append(form);
                form.submit();
            }
        });
    });
");
?>

<div class="xdivclass_venue">
    <table id="<?= Html::encode($tableId) ?>" class="display nowrap" style="width:100%"></table>
</div>