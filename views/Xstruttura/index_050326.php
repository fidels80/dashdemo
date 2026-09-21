<style>
    .select2-dropdown-above-table {
        z-index: 9999 !important;
    }

    .dropdown-select2 {
        z-index: 9999 !important;
    }

    .divclass {
        width: 100% !important;
    }

    div.dt-container {
        width: 100%;
        margin: 0;
    }

    @media screen and (width: 1080px) {
        div.dt-container {
            width: 100%;
            margin: 0;
        }
    }

    @media screen and (width: 1366px) {
        div.dt-container {
            width: 100%;
            margin: 0;
        }
    }

    /* Aggiungi questo CSS al tuo style esistente */

    /* Stili per i pulsanti di azione nella DataTable */
    .dataTables_wrapper .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
    }

    /* Hover effects per i pulsanti */
    .dataTables_wrapper .btn-primary:hover {
        background-color: #286090;
        border-color: #122b40;
    }

    .dataTables_wrapper .btn-info:hover {
        background-color: #269abc;
        border-color: #1f7e9a;
    }

    .dataTables_wrapper .btn-danger:hover {
        background-color: #c12e2a;
        border-color: #ac2925;
    }

    /* Allineamento dei pulsanti nella colonna azioni */
    .dataTables_wrapper td .btn {
        margin-right: 3px;
        margin-bottom: 2px;
    }

    /* Larghezza fissa per la colonna azioni */
    .dataTables_wrapper th:last-child,
    .dataTables_wrapper td:last-child {
        width: 120px;
        text-align: center;
    }
</style>


<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Json;
/* @var $this yii\web\View */
/* @var $searchModel app\models\XstrutturaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Xstrutturas';
//$this->params['breadcrumbs'][] = $this->title;



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
$this->registerJsFile("https://cdnjs.cloudflare.com/ajax/libs/select2/
4.0.12/js/select2.full.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);
$this->registerCssFile("https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css");


$this->registerJsFile("https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js", [
    'depends' => [\yii\web\JqueryAsset::class]
]);

$this->registerCssFile("https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css");



//$filteredDataJson = Json::encode($dwroomlist->allModels, JSON_INVALID_UTF8_SUBSTITUTE);
$tableId = 'struttura_' . uniqid();
//print_r($filteredDataJson); 
$jsUserLevel = Yii::$app->user->identity->level ?? '';

?>
<BR>
<BR>
<BR>
<BR>
<BR>
<div class="xstruttura-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Xstruttura', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?php // echo $this->render('_search', ['model' => $searchModel]); 
   /* 'id',
    'Struttura',
    'Descrizione',
    'Citta',
    'Cd_cf',
    'Partitaiva',
    //'xcheck',
    */
    ?>


    



</div>


<?php

// Ottieni i dati raw dal provider
$rawData = $dataProvider->query->all();

// Converti gli oggetti ActiveRecord in array associativi
$flatData = [];
foreach ($rawData as $item) {
    if (is_object($item)) {
        // Se è un oggetto ActiveRecord, usa toArray()
        $flatData[] = $item->toArray();
    } elseif (is_array($item)) {
        // Se è già un array
        if (array_keys($item) === range(0, count($item) - 1)) {
            // Array indicizzato numericamente
            foreach ($item as $subItem) {
                $flatData[] = is_object($subItem) ? $subItem->toArray() : $subItem;
            }
        } else {
            // Array associativo
            $flatData[] = $item;
        }
    }
}

// Debug per verificare la struttura dei dati
if (YII_DEBUG and 1 == 2) {
    echo '<pre>Debug - Numero di elementi: ' . count($flatData) . '</pre>';
    if (!empty($flatData)) {
        echo '<pre>Debug - Primo elemento: ' . print_r($flatData[0], true) . '</pre>';
    }
}
//http://dashboard.ilvbc.it:8090/test/web/index.php?r=xvenue%2Fupdate&id=5CB937F4-C2BD-480F-AA5C-1CC06661A18E
//http://dashboard.ilvbc.it:8090/test/web/index.php?r=xvenue%2Fupdate?id=0D6130EE-914F-4721-9119-A7A8B3E6ED05



// Codifica in JSON con gestione degli errori
$filteredDataJson = Json::encode($flatData, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRETTY_PRINT);

// Verifica se la codifica JSON è riuscita
if (json_last_error() !== JSON_ERROR_NONE) {
    throw new \Exception('Errore nella codifica JSON: ' . json_last_error_msg());
}


$this->registerJs("
    $(document).ready(function () {
        console.log('Inizializzazione DataTable per il tavolo con ID: " . $tableId . "');
        
        // Converti i dati in JSON per il debug
        let rawData = " . $filteredDataJson . ";
        console.log('Dati del provider (JSON stringified):', JSON.stringify(rawData, null, 2));
        console.log('Dati del provider (oggetto):', rawData);
        console.log('Numero di elementi:', rawData.length);
        
        // Verifica il primo elemento per controllare la struttura
        if (rawData.length > 0) {
            console.log('Primo elemento:', rawData[0]);
            console.log('Chiavi del primo elemento:', Object.keys(rawData[0]));
        }
        
        let table = $('#" . $tableId . "').DataTable({
            data: rawData,
            responsive: true,
            fixedHeader: true,
            colReorder: true,
            scrollX: true,
            dom: 'Bfrtip',
            paging: false,
            responsive: true,
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
                    text: 'PDF'
                },
                {
                    extend: 'print',
                    text: 'Stampa'
                }
            ],
            columns: [
              
                { title: 'Struttura', data: 'Struttura' },
                { title: 'Descrizione', data: 'Descrizione' },
        { title: 'Citta', data: 'Citta' },
 { title: 'Cd_cf', data: 'Cd_cf' },
   { title: 'Partitaiva', data: 'Partitaiva' },
                
                {
                    title: 'Azioni',
                    data: null,
                    orderable: false,
                    searchable: false,
                     width: '100px',
                    render: function (data, type, row) {
                        let actions = '';
                        
                        // Pulsante View
                        actions += '<a href=\"" . \yii\helpers\Url::to(['view']) . "&id=' + row.id + '\" ' +
                                  'class=\"btn btn-sm btn-primary\" title=\"Visualizza\" style=\"margin-right: 5px;\">' +
                                  '<i class=\"fas fa-eye\"></i></a>';
                        
                        // Pulsante Update
                        actions += '<a href=\"" . \yii\helpers\Url::to(['update']) . "&id=' + row.id + '\" ' +
                                  'class=\"btn btn-sm btn-info\" title=\"Modifica\" style=\"margin-right: 5px;\">' +
                                  '<i class=\"fas fa-edit\"></i></a>';
                        
                        // Pulsante Delete
                        actions += '<a href=\"" . \yii\helpers\Url::to(['delete']) . "&id=' + row.id + '\" ' +
                                  'class=\"btn btn-sm btn-danger delete-btn\" title=\"Elimina\" ' +
                                  'data-confirm=\"Sei sicuro di voler eliminare questo elemento?\" ' +
                                  'data-method=\"post\">' +
                                  '<i class=\"fas fa-trash\"></i></a>';
                        
                        return actions;
                    }
                }
            ]
        });

        // Inizializzazione Select2 se hai dei filtri custom
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            width: 'auto',
            dropdownCssClass: 'dropdown-select2'
        });

        // Gestione dei pulsanti di cancellazione
        $('#" . $tableId . "').on('click', '.delete-btn', function(e) {
            e.preventDefault();
            
            let url = $(this).attr('href');
            let confirmMessage = $(this).data('confirm');
            
            if (confirm(confirmMessage)) {
                // Crea un form nascosto per inviare la richiesta POST
                let form = $('<form>', {
                    'method': 'POST',
                    'action': url
                });
                
                // Aggiungi il token CSRF di Yii2
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': '" . Yii::$app->request->csrfParam . "',
                    'value': '" . Yii::$app->request->csrfToken . "'
                }));
                
                // Aggiungi il form al body e invialo
                $('body').append(form);
                form.submit();
            }
        });
    });
");
?>
<div class="divclass">
    <table id="<?= Html::encode($tableId) ?>" class="display nowrap"></table>
</div>