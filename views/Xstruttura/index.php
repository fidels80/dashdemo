<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\XstrutturaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $roomlistData array */

$this->title = 'Gestione Strutture e Roomlist';


// CSS per DataTables e Pulsanti
$this->registerCssFile('https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css');
$this->registerCssFile('https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css');

// JS Core
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// JS Pulsanti ed Esportazione
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.copy.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
?>


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

    .btn-tool {
        background-color: #f39c12 !important;
        color: white !important;
    }

    .table-title {
        margin-top: 40px;
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
    }

    @media screen and (min-width: 800px) and (max-width: 1200px) {
        .xdivclass table {
            min-width: 800px;
            /* Reduced from 1500px to 1200px */
            width: 100%;
        }
    }
</style>

<div class="xstruttura-index">
    <br><br><br>
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Html::a('Crea Nuova Struttura', ['create'], ['class' => 'btn btn-success']) ?></p>

    <h3 class="table-title">Elenco Strutture</h3>
    <div class="xdivclass">
        <?php $tableStrId = 'struttura_table'; ?>
        <table id="<?= $tableStrId ?>" class="display nowrap"></table>
    </div>

    <h3 class="table-title">Room List</h3>
    <div class="xdivclass">
        <?php $tableRoomId = 'roomlist_table'; ?>
        <table id="<?= $tableRoomId ?>" class="display nowrap"></table>
    </div>
</div>

<?php
// Usiamo $struttureData che arriva dal controller (già processata col matching PHP)
// Se per caso non è settata, facciamo un fallback sul dataProvider per non rompere la pagina
$datiDaRenderizzare = isset($struttureData) ? $struttureData : $dataProvider->getModels();

$struttureJson = Json::encode($datiDaRenderizzare, JSON_INVALID_UTF8_SUBSTITUTE);
$roomlistJson = Json::encode($roomlistData ?? [], JSON_INVALID_UTF8_SUBSTITUTE);

// Debug: ora vedrai l'array "viaggi" dentro ogni struttura nel log di Yii
yii::warning($struttureJson);
?>

<script>
    $(document).ready(function() {
        // Definizione dei bottoni comuni per entrambe le tabelle
        const commonButtons = [{
                extend: 'copy',
                className: 'btn btn-secondary btn-sm',
                text: '<i class="fas fa-copy"></i> Copia'
            },
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm',
                text: '<i class="fas fa-file-excel"></i> Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger btn-sm',
                text: '<i class="fas fa-file-pdf"></i> PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-info btn-sm',
                text: '<i class="fas fa-print"></i> Stampa'
            }
        ];

        // --- 1. DATATABLE STRUTTURE ---
        $('#<?= $tableStrId ?>').DataTable({
            data: <?= $struttureJson ?>,
            pageLength: 50, // Ripristinato a 100 come richiesto inizialmente
            responsive: true,
            dom: 'Bfrtip',
            buttons: commonButtons,
            columns: [{
                    title: 'Struttura',
                    data: 'Struttura',
                    width: '20%' // <--- Impostata al 20%
                },
                {
                    title: 'Fornitore',
                    data: 'nome_fornitore',
                    width: '20%', // <--- Impostata al 20%
                    render: function(data, type, row) {
                        // Se c'è la descrizione mostriamo quella, altrimenti il codice Cd_cf
                        console.log(data);
                        return data ? data : (row.nome_fornitore ? row.Cd_cf : '-');

                    }
                },

                {
                    title: 'Città',
                    data: 'Citta',
                    width: '15%', // <--- Impostata al 20%
                },
                {
                    // --- NUOVA COLONNA FATTURATO (CHECKBOX) ---
                    title: 'Fatt.',
                    data: 'viaggi',
                    width: '5%',
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        // Verifichiamo se almeno uno dei viaggi associati ha fatturato = 1 (o true)
                        var isFatturato = false;
                        if (data && data.length > 0) {
                            isFatturato = data.some(function(v) {
                                return v.fatturato == 1 || v.fatturato == true;
                            });
                        }

                        // Ritorniamo una checkbox disabilitata (solo visualizzazione)
                        return '<input type="checkbox" ' + (isFatturato ? 'checked' : '') + ' onclick="return false;">';
                    }
                },
                {
                    title: 'Tour Associati',
                    data: 'viaggi', // Qui arrivano i dati processati dal PHP
                    width: '30%', // <--- Impostata al 20%
                    render: function(data, type, row) {
                        if (data && data.length > 0) {
                            var buttons = '';
                            data.forEach(function(v) {
                                // --- LOGICA DI FORMATTAZIONE DATA ---
                                var dataFormattata = v.datap; // Valore di default

                                if (v.datap) {
                                    // 1. Prendiamo solo la parte YYYY-MM-DD (prima dello spazio)
                                    var soloData = v.datap.split(' ')[0];
                                    // 2. Dividiamo anno, mese e giorno
                                    var parti = soloData.split('-');

                                    if (parti.length === 3) {
                                        // 3. Ricomponiamo nel formato DD/MM/YYYY
                                        dataFormattata = parti[2] + '/' + parti[1] + '/' + parti[0];
                                    }
                                }
                                // ------------------------------------

                                var url = 'https://dashboard.planorys.it:4433/index.php?r=xtravelhead%2Ftool&id=' + v.id;

                                // Usiamo dataFormattata invece di v.datap
                                buttons += '<br><a href="' + url + '" target="_blank" class="btn btn-xs btn-tool" style="display:block; margin-bottom:2px;">' +
                                    '<i class="fas fa-external-link-alt"></i> ' + v.desc + '<br> del ' + dataFormattata + '</a>';
                            });
                            return buttons;
                        }
                        return '<span class="text-muted small">Nessun viaggio</span>';
                    }
                },
                {
                    title: 'Azioni',
                    data: null,
                    orderable: false,
                    width: '10%', // <--- Impostata al 20%
                    render: function(data, type, row) {
                        var viewUrl = '<?= Url::to(['view']) ?>&id=' + row.id;
                        var updateUrl = '<?= Url::to(['update']) ?>&id=' + row.id;
                        return '<a href="' + viewUrl + '" class="btn btn-sm btn-primary" title="Visualizza"><i class="fas fa-eye"></i></a> ' +
                            '<a href="' + updateUrl + '" class="btn btn-sm btn-info" title="Modifica"><i class="fas fa-edit"></i></a>';
                    }
                }
            ],
            autoWidth: false
        });

        // --- 2. DATATABLE ROOMLIST ---
        $('#<?= $tableRoomId ?>').DataTable({
            data: <?= $roomlistJson ?>,
            pageLength: 100,
            responsive: true,
            dom: 'Bfrtip',
            buttons: commonButtons,
            columns: [{
                    title: 'Nominativo',
                    data: 'nominativo',
                    width: '25%'
                },
                {
                    title: 'Ruolo',
                    data: 'ruolo',
                    width: '15%'
                },
                {
                    title: 'Note',
                    data: 'note',
                    width: '20%'
                },
                {
                    title: 'Azioni Tool',
                    data: null,
                    width: '40%', // Aumentato lo spazio per ospitare il pulsante ricco
                    orderable: false,
                    render: function(data, type, row) {
                        // 1. Recuperiamo descrizione e data dalla riga
                        var descViaggio = row.viaggio_desc ? row.viaggio_desc : 'N/D';
                        var dataFormattata = '';

                        if (row.viaggio_data) {
                            var soloData = row.viaggio_data.split(' ')[0];
                            var parti = soloData.split('-');
                            if (parti.length === 3) {
                                dataFormattata = ' del ' + parti[2] + '/' + parti[1] + '/' + parti[0];
                            }
                        }

                        var roomToolUrl = 'https://dashboard.planorys.it:4433/index.php?r=xtravelhead%2Ftool&id=' + row.th_id;

                        // 2. Costruiamo il pulsante con descrizione e data all'interno
                        return '<br><a href="' + roomToolUrl + '" target="_blank" class="btn btn-xs btn-tool" style="display:block; text-align:left; white-space: normal;">' +
                            '<i class="fas fa-bed"></i> <strong>' + descViaggio + '<BR>' + dataFormattata + '</strong><br>' +
                            '</a><br>';
                    }
                }
            ],
            autoWidth: false
        });
    });
</script>