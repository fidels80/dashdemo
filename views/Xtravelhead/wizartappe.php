<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\nav\NavX;
//use kartik\select2\Select2;
use onmotion\apexcharts\ApexchartsWidget;
use yii\helpers\Json;
use kartik\dialog\Dialog;
use yii\web\JsExpression;
use yii\data\ArrayDataProvider;
use kartik\export\ExportMenu;
use yii\bootstrap4\Modal;
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use app\models\Xtravelrow;
use yii\db\Expression;
use app\models\XStruttura;
use app\models\XVenue;

use app\models\XRoomlist;

if (Yii::$app->cache->exists($tappa)) {
    // esiste
    $data = Yii::$app->cache->get($tappa);
    yii::error("Cache hit for $tappa: " . print_r($data, true));
    if ($data <> Yii::$app->user->id and $data <> 0) {
        // Cache hit, user already has access
        // yii::error("Cache hit for $tappa: " . print_r($data, true));
        echo "Tappa già in uso da un altro utente, impossibile modificare";
    } else {
        // Cache miss, different user
        // yii::error("Cache miss for $tappa: " . print_r($data, true));
        Yii::$app->cache->set($tappa, Yii::$app->user->id); // 3600 = TTL in secondi
    }
} else {

    Yii::$app->cache->set($tappa, Yii::$app->user->id); // 3600 = TTL in secondi
}

//                      yii::warning($data);
$table_tmpid = uniqid();
/*$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['grid_color', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrgrid = $ris['grid_color'] ?? '';*/
$filteredData = [];

// Fetch data from xruoli table for role dropdown
$ruoliData = (new \yii\db\Query())
    ->select(['cd_ruolo', 'descrizione'])
    ->from('xruoli')
    ->createCommand(Yii::$app->db5)
    ->queryAll();

// Fetch data from party table for party dropdown
$partyData = (new \yii\db\Query())
    ->select(['party AS cd_party', 'party AS dparty'])
    ->from('xtravelrow')
    ->where([
        'and',
        ['not', ['party' => null]],
        ['<>', 'party', '']
    ])
    ->distinct()
    ->createCommand(Yii::$app->db5)
    ->queryAll();

$strutturaData
    = (new \yii\db\Query())
    ->select([
        'id',
        new Expression(" struttura +' - '+citta as descrizione ")
    ])
    ->from('x_struttura')
    ->createCommand(Yii::$app->db5)
    ->queryAll();
// Convert to format needed for Select2

$venuedata = (new \yii\db\Query())
    ->select([
        'id',
        new Expression(" venue +' - '+citta as descrizione ")
    ])
    ->from('x_venue')
    ->createCommand(Yii::$app->db5)
    ->queryAll();
$commedataData = (new \yii\db\Query())
    ->select(['cd_dosottocommessa as id', 'descrizione'])
    ->from('dosottocommessa')
    ->createCommand(Yii::$app->db5)
    ->queryAll();
$Clidata = (new \yii\db\Query())
    ->select(['cd_Cf as id', 'descrizione'])
    ->from('cf')
    ->where(['TipoCf' => 'C'])
    ->createCommand(Yii::$app->db5)
    ->queryAll();
$Fordata = (new \yii\db\Query())
    ->select(['cd_Cf as id', 'descrizione'])
    ->from('cf')
    ->where(['TipoCf' => 'F'])
    ->createCommand(Yii::$app->db5)
    ->queryAll();
$Creditdata = (new \yii\db\Query())
    ->select(['codicecarta  as id', 'descrizione'])
    ->from('x_creditcard')
    //->where(['TipoCf' => 'F'])
    ->createCommand(Yii::$app->db5)
    ->queryAll();

$Ospitedata = (new \yii\db\Query())
    ->select(['nominativo  as id', 'nominativo as descrizione'])
    ->from('x_roomlist')
    ->where(['th_id' => $th_id])
    //->where(['TipoCf' => 'F'])
    ->createCommand(Yii::$app->db5)
    ->queryAll();







$ruoliOptions = ArrayHelper::map($ruoliData, 'cd_ruolo', 'descrizione');
$partyOptions = ArrayHelper::map($partyData, 'cd_party', 'dparty');
//$cittaOptions = ArrayHelper::map($cittaData, 'cd_citta', 'descrizione');
$strutturaOptions = ArrayHelper::map($strutturaData, 'id', 'descrizione');
$venueOptions = ArrayHelper::map($venuedata, 'id', 'descrizione');
$commessaOptions = ArrayHelper::map($commedataData, 'id', 'descrizione');
$ClidataOptions = ArrayHelper::map($Clidata, 'id', 'descrizione');
$FordataOptions = ArrayHelper::map($Fordata, 'id', 'descrizione');
$CreditdataOptions = ArrayHelper::map($Creditdata, 'id', 'descrizione');
$OspitedataOptions = ArrayHelper::map($Ospitedata, 'id', 'descrizione');
// Convert to JSON for use in JavaScript
$ruoliOptionsJson = Json::encode($ruoliOptions);
$partyOptionsJson = Json::encode($partyOptions);
//$cittaOptionsJson = Json::encode($cittaOptions);
$strutturaOptionsJson = Json::encode($strutturaOptions);
$venueOptionsJson = Json::encode($venueOptions);
$commessaOptionsJson = Json::encode($commessaOptions);
$ClidataOptionsJson = Json::encode($ClidataOptions);
$FordataOptionsJson = Json::encode($FordataOptions);

$CreditdataOptionsJson = Json::encode($CreditdataOptions);
$OspitedataOptionsJson = Json::encode($OspitedataOptions);

yii::error($OspitedataOptionsJson);
yii::error($CreditdataOptionsJson);
$this->registerCss("
    .editable {
        background-color: rgba(240, 240, 240, 0.5);
        cursor: pointer;
        padding: 2px;
        border-radius: 3px;
        transition: all 0.3s;
    }
    .editable:hover {
        background-color: rgba(200, 200, 200, 0.7);
    }
    .editing {
        padding: 0 !important;
        background-color: rgb(255, 255, 224);
    }
    .editing input, .editing select {
        width: 100%;
        padding: 5px;
        box-sizing: border-box;
        border: 1px solid #ccc;
    }
    .save-row-btn {
        margin-right: 5px;
    }
    .action-buttons {
        white-space: nowrap;
    }
    /* Style for highlighting changes */
    .cell-changed {
        animation: highlight 2s;
    }
    @keyframes highlight {
        0% { background-color: #ffff99; }
        100% { background-color: transparent; }
    }
    /* Style for selects in table */
    .select2-container {
        width: 100% !important;
    }
    .select2-dropdown {
        z-index: 9999;
    }
         /* Stile per il bottone Aggiungi Nuova Riga */
    .btn-add-row {
        margin-bottom: 0px;
    }
");

// Il resto del tuo codice PHP rimane invariato
?>


<style>
    .content {
        width: 95%;
    }

    .euro-column {
        min-width: 190px;
        /* Puoi aumentare il valore se serve più spazio */
        text-align: right;
        /* Allinea a destra per una migliore leggibilità */
    }

    .euro-total {
        min-width: 190px;
        /* Puoi aumentare il valore se serve più spazio */
        text-align: right;
        /* Allinea a destra per una migliore leggibilità */
        white-space: nowrap;
    }

    /* Forza una larghezza minima per la colonna struttura */
    .struttura-column {
        min-width: 200px !important;
        /* Regola questo valore in base alle tue esigenze */
        white-space: normal !important;
        /* Permette al testo di andare a capo se necessario */
        word-break: break-word;
    }

    .table-info,
    .table-info>td,
    .table-info>th {
        background-color: #fdfdfd;
    }

    /* Colori basati sullo stato della riga */
    tr.stato-chiuso {
        background-color: #00cff3 !important;
        /*  color: #00d3f8;*/
    }

    /* Blu chiaro */
    tr.stato-da-prenotare {
        /* background-color: #eec612 !important;*/
    }

    /* Giallo */
    tr.stato-prenotato {
        background-color: #8af5a3 !important;
    }

    /* Verde */
    tr.stato-cancellato {
        background-color: #f59403 !important;
    }

    /* Rosso */
    tr.stato-in-penale {
        background-color: #ff0505 !important;
    }

    /* Grigio */
</style>
<?php


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
$tableId = 'wzdetailTable_righe' . uniqid();
//print_r($filteredDataJson); 
$jsUserLevel = Yii::$app->user->identity->level ?? '';
//$roomDataJson = Json::encode($dwroomlist->allModels, JSON_INVALID_UTF8_SUBSTITUTE);

$rawData = $righe;
//$dwroomlist->allModels;

$flatData = [];
foreach ($rawData as $chunk) {
    if (is_array($chunk) && array_keys($chunk) === range(0, count($chunk) - 1)) {
        foreach ($chunk as $item) {
            $flatData[] = $item;
        }
    } else {
        $flatData[] = $chunk;
    }
}

$filteredDataJson = Json::encode($flatData, JSON_INVALID_UTF8_SUBSTITUTE);
$this->title = "";
?>

<style>
    .select2-dropdown-above-table {
        z-index: 9999 !important;
    }

    .dropdown-select2 {
        z-index: 9999 !important;
    }

    .divclass {
        width: 95% !important;
    }
</style>
<BR>
<BR>
<BR>
<BR>
<BR>

<div class="row">
    <div class="col-md-6">
        <?php
        function isValidUuid($uuid)
        {
            return preg_match('/^\{?[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}\}?$/', $uuid);
        }

        echo "<h4>Modifica Tappa ";

        if (isValidUuid($tappa)) {
            $venue = Xvenue::find()->where(['id' => $tappa])->one();
            echo $venue ? $venue->venue . ' - ' . $venue->citta : $tappa;
        } else {
            echo htmlspecialchars($tappa); // fallback sicuro se non è un UUID
        }

        echo " del " . htmlspecialchars($data) . "</h4>";
        ?>

    </div>

</div>
<!-- Aggiungi qui il pulsante per nuova riga -->
<BR>
<BR>


<div class="row">
    <div>
        <button id="btnAddNewRow" class="button-base button-lift btn-add-row">
            <i class="fas fa-plus-circle"></i> Aggiungi Nuova Riga
        </button>
    </div>





    <div style="margin-left: 30px;">
        <?php

        /*  <div style="margin-left: 50px;">
        <button id="btnAddNewosp" class="button-base button-lift btn-add-xroomlist"
            data-toggle="modal" data-target="#xroomlistModal">
            <i class="fas fa-bed"></i> Aggiungi Nuovo Ospite
        </button>
    </div>

    <!-- Modal Structure -->
    <div class="modal fade" id="xroomlistModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aggiungi Nuovo Ospite</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContent2a">
                    <!-- Il contenuto della form verrà caricato qui -->
                </div>
            </div>
        </div>
    </div>

*/



        // Pulsante per aprire la modale per l'inserimento dei nominativi
        echo Html::button(' <i class="fas fa-bed"></i> Aggiungi Nominativo', [
            'class' => 'button-base button-lift',
            'data-toggle' => 'modal',
            'data-target' => '#xroomlistModal',
        ]);


        Modal::begin([
            'id' => 'xroomlistModal',
            'title' => '<h4>Aggiungi Nominativo</h4>',
            'size' => Modal::SIZE_LARGE,
        ]);
        echo '<div id="modalContent2a" class="scrollable-table-container">';
        $modelxroomlist = new XRoomlist();
        $modelxroomlist->th_id = $th_id;
        // Qui carichi direttamente il contenuto del form
        echo $this->render('/xroomlist/createaj', [
            'model' => $modelxroomlist,
            'modalId' => '#xroomlistModal',
            'th_id' => $th_id,
            // passi il modello di create
        ]);
        echo '</div>';
        Modal::end();


        ?>

        <?php

        $this->registerJs("
// Reset campi quando si apre la modale
$('#xroomlistModal').on('show.bs.modal', function() {
    // Reset del form
    $('#xroomlist-form')[0].reset();
    
    // Reset delle Select2
    $('#cd_ar-select').val(null).trigger('change');
    $('#party-select').val(null).trigger('change');
    $('#ruolo-select').val(null).trigger('change');
    // Non resettiamo commessa-select come richiesto
    
    // Reset campi testo
    $('#xroomlist-nominativo').val('');
    $('#xroomlist-note').val('');
});

// Fix Select2 nelle modali (quando la modale è completamente visibile)
$('#xroomlistModal').on('shown.bs.modal', function() {
    $('.select2-container').remove();
    
    $('#cd_ar-select, #ruolo-select, #party-select, #commessa-select').each(function() {
        if ($(this).data('select2')) {
            $(this).select2('destroy');
        }
        $(this).select2({
            dropdownParent: $('#xroomlistModal'),
            allowClear: true,
            width: '100%'
        });
    });
});

// Submit AJAX (usa .off().on() per evitare duplicazione)
$('#xroomlist-form').off('submit').on('submit', function(e) {
    e.preventDefault();
    
    // Disabilita il pulsante submit per evitare doppi click
    var submitBtn = $(this).find('button[type=\"submit\"]');
    submitBtn.prop('disabled', true);
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            console.log('Response:', response);
            
            if (response.success) {
                $('#xroomlistModal').modal('hide');
                submitBtn.prop('disabled', false);
                if (response.data) {
                    const data = response.data;
                    const nuovaRiga = `
                        <tr class=\"nuova-tappa\">
                            <td>\${data.nominativo}</td>
                            <td>\${data.cd_ar}</td>
                            <td>\${data.ruolo}</td>
                            <td>\${data.party}</td>
                            <td>\${data.commessa}</td>
                            <td>\${data.note}</td>
                            <td><i class=\"fas fa-stop\"></i></td>
                            <td>
                                <button type=\"button\" class=\"btn btn-warning btn-sm\"
                                    title=\"Modifica nominativo\" 
                                    onclick=\"window.location.href='/index.php?r=xroomlist%2Fupdate&id=\${data.id_guest}'\">
                                    <i class=\"fas fa-pen\"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    
                    $('table[name=\"roomlist\"] tbody').prepend(nuovaRiga);
                    alert('Salvataggio effettuato');
                } else {
                    alert('Ospite inserito con successo');
                    location.reload();
                }
            } else {
                // Gestione errori
                let errorMsg = 'Errore durante il salvataggio del nominativo.';
                
                console.log('Errori ricevuti:', response.errors);
                
                if (response.errors) {
                    errorMsg = Object.entries(response.errors)
                        .map(([field, errors]) => `\${field}: \${Array.isArray(errors) ? errors.join(', ') : errors}`)
                        .join('\\n');
                }
                
                if (response.html) {
                    $('#modalContent2a').html(response.html);
                    // Reinizializza le select2 dopo aver ricaricato l'HTML
                    setTimeout(function() {
                        $('#xroomlistModal').trigger('shown.bs.modal');
                    }, 100);
                }
                
                alert('ERRORE:\\n' + errorMsg);
                
                // Riabilita il pulsante
                submitBtn.prop('disabled', false);
            }
        },
        error: function(xhr, status, error) {
            console.log('Errore AJAX:', error);
            console.log('Response:', xhr.responseText);
            alert('Si è verificato un errore nella richiesta.');
            
            // Riabilita il pulsante
            submitBtn.prop('disabled', false);
        }
    });
    
    return false;
});
", \yii\web\View::POS_READY);

        ?>


    </div>


    <div style="margin-left: 30px;">
        <button id="btnAddNewstr" class="button-base button-lift btn-add-struttura" data-toggle="modal" data-target="#strutturaModal">
            <i class="fas fa-home"></i> Aggiungi Nuova Struttura
        </button>
    </div>

    <!-- Modal Structure -->
    <div class="modal fade" id="strutturaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aggiungi Nuova Struttura</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContentStr">
                    <!-- Il contenuto della form verrà caricato qui -->
                </div>
            </div>
        </div>
    </div>


    <div style="margin-left: 30px;">
        <button id="btnAddNewrl" class="button-base button-lift btn-add-struttura"
            data-toggle="modal" data-target="#ruoloModal">
            <i class="fas fa-user"></i> Aggiungi Nuovo Ruolo
        </button>
    </div>

    <!-- Modal Structure -->
    <div class="modal fade" id="ruoloModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aggiungi Nuovo Ruolo</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContentRl">
                    <!-- Il contenuto della form verrà caricato qui -->
                </div>
            </div>
        </div>
    </div>
    <div style="margin-left: 30px;">
        <button id="btnAddNewprt" class="button-base button-lift btn-add-struttura"
            data-toggle="modal" data-target="#partyModal">
            <i class="fas fa-users"></i> Aggiungi Nuovo Party
        </button>
    </div>

    <!-- Modal Structure -->
    <div class="modal fade" id="partyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Aggiungi Nuovo party</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContentPrt">
                    <!-- Il contenuto della form verrà caricato qui -->
                </div>
            </div>
        </div>
    </div>




</div>
<br>
<div class="row">
    <div style="margin-left: 0px;">
        <button id="btnDeleteSelected" class="btn btn-danger button-lift">
            <i class="fas fa-trash"></i> Elimina selezionati
        </button>
    </div>
    <div style="margin-left: 30px;">
        <button id="btnsaveSelected" class="btn btn-success button-lift">
            <i class="fas fa-save"></i>
            Salva tutto
        </button>
    </div>

    <div style="margin-left: 30px;">
        <?php
        $url = Url::to([
            'xtravelhead/tornaindietro',
            'tappa' => $tappa,
            'th_id' => $th_id
        ]);

        echo Html::button('<i class="fa fa-arrow-left"></i> Torna indietro', [
            'class' => 'button-base-support button-lift',
            'onclick' => 'window.location.href = "' . $url . '"'
        ]);

        ?>

    </div>
</div>

<BR>
<BR>
<div class="row mb-3">
    <div class="col-md-4">
        <label>Cerca Ospite:</label>
        <input type="text" id="cercaOspiteSpeciale" class="form-control" placeholder="Es: Mario Rossi">
    </div>
    <div class="col-md-4">
        <label>Cerca Struttura (Hotel):</label>
        <input type="text" id="cercaStrutturaSpeciale" class="form-control" placeholder="Es: Napoli">
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card bg-light">
            <div class="card-body p-2">
                <div class="row text-center">
                    <div class="col">
                        <strong>Tot. Costo:</strong> <span id="widget-tot-costo">0.00</span> €
                    </div>
                    <div class="col">
                        <strong>Tot. City Tax:</strong> <span id="widget-tot-tax">0.00</span> €
                    </div>
                    <div class="col">
                        <strong>Tot. Generale:</strong> <span id="widget-tot-generale" class="text-primary">0.00</span> €
                    </div>
                    <div class="col">
                        <strong>Tot. Fee:</strong> <span id="widget-tot-fee">0.00</span> €
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="divclass">
    <table id="<?= Html::encode($tableId) ?>" class="display nowrap">
    </table>
</div>

<script>
    $(document).ready(function() {
        // Aggiungi questa funzione dopo la gestione dell'editing delle celle
        // Funzione per calcolare automaticamente la qta basata su check_in e check_out

        function mettiiva(row) {

            const rowData = table.row(row).data();
            console.log(rowData);
            console.log('partita con liva' + rowData.cd_Ar);
            const codiceArticolo = rowData.cd_Ar; // suppongo che l'articolo sia in rowData.cd_ar

            if (codiceArticolo) {
                $.ajax({
                    url: 'index.php?r=xtravelhead/getaliart',
                    type: 'GET',
                    data: {
                        q: codiceArticolo
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.results && response.results.length > 0) {
                            const cdAliquotaV = response.results[0].cd_aliquota_v;

                            // Aggiorna la riga
                            rowData.codiva = cdAliquotaV;
                            table.row(row).data(rowData).draw(false);

                            console.log(`IVA impostata automaticamente: ${cdAliquotaV}`);

                            // Evidenzia la cella cd_aliquota
                            const aliqCell = $(table.row(row).node()).find('td')
                                .eq(table.column('codiva:name').index());
                            aliqCell.addClass('cell-changed');

                            // Rimuovi l’evidenziazione dopo 2 secondi
                            setTimeout(() => {
                                aliqCell.removeClass('cell-changed');
                            }, 2000);
                        } else {
                            console.warn(`Nessuna aliquota trovata per articolo ${codiceArticolo}`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(`Errore chiamata AJAX: ${status} - ${error}`);
                    }
                });
            } else {
                console.log('no articolo');
            }
        }

        function calcolaQtaAutomatico(row) {
            const rowData = table.row(row).data();
            const checkIn = rowData.check_in;
            const checkOut = rowData.check_out;


            /*   if (checkIn && !checkOut) {
                   // Se manca il checkout, impostalo a checkin + 1 giorno
                   const dataCheckInTmp = new Date(checkIn);
                   dataCheckInTmp.setDate(dataCheckInTmp.getDate() + 1);
                   checkOut = dataCheckInTmp.toISOString().split('T')[0]; // formato YYYY-MM-DD
                   rowData.check_out = checkOut; // aggiorno anche la tabella
               }*/


            if (checkIn && checkOut) {
                const dataCheckIn = new Date(checkIn);
                const dataCheckOut = new Date(checkOut);

                // Calcola la differenza in millisecondi
                const differenzaMs = dataCheckOut.getTime() - dataCheckIn.getTime();

                // Converti in giorni
                const differenzaGiorni = Math.ceil(differenzaMs / (1000 * 60 * 60 * 24));

                // Assicurati che la differenza sia positiva
                if (differenzaGiorni > 0) {
                    // Aggiorna la qta nel rowData
                    rowData.qta = differenzaGiorni;

                    // Aggiorna la riga nella tabella
                    table.row(row).data(rowData).draw(false);

                    console.log(`Qta calcolata automaticamente: ${differenzaGiorni} giorni`);

                    // Evidenzia la cella qta per mostrare che è stata aggiornata
                    const qtaCell = $(table.row(row).node()).find('td').
                    eq(table.column('qta:name').index());
                    qtaCell.addClass('cell-changed');

                    // Rimuovi l'evidenziazione dopo 2 secondi
                    setTimeout(() => {
                        qtaCell.removeClass('cell-changed');
                    }, 2000);
                }
            }
            if (checkIn && !checkOut) {
                const differenzaGiorni = 1;

                if (differenzaGiorni > 0) {
                    // Aggiorna la qta nel rowData
                    rowData.qta = differenzaGiorni;

                    // Aggiorna la riga nella tabella
                    table.row(row).data(rowData).draw(false);

                    console.log(`Qta calcolata automaticamente: ${differenzaGiorni} giorni`);

                    // Evidenzia la cella qta per mostrare che è stata aggiornata
                    const qtaCell = $(table.row(row).node()).find('td').
                    eq(table.column('qta:name').index());
                    qtaCell.addClass('cell-changed');

                    // Rimuovi l'evidenziazione dopo 2 secondi
                    setTimeout(() => {
                        qtaCell.removeClass('cell-changed');
                    }, 2000);
                }

            }
        }
        // Aggiungi questo codice dopo la definizione delle colonne e prima di inizializzare DataTable

        // Funzione per precaricire i dati di struttura e venue
        function preloadStructureAndVenueData() {
            // Precaricare tutti gli ID di struttura unici
            const structureIds = [...new Set(tableData.map(row => row.struttura).filter(id => id && !isNaN(id)))];
            const venueIds = [...new Set(tableData.map(row => row.citta).filter(id => {
                const uuidRegex = /^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/;
                return id && uuidRegex.test(id);
            }))];

            // Inizializza le cache se non esistono
            if (!window.strutturaCache) window.strutturaCache = {};
            if (!window.venueCache) window.venueCache = {};

            // Precarica dati struttura
            structureIds.forEach(id => {
                if (!window.strutturaCache[id]) {
                    $.ajax({
                        url: 'index.php?r=xtravelhead/getstruttura',
                        type: 'GET',
                        data: {
                            id: id
                        },
                        async: false, // Sincrono per precaricamento
                        success: function(response) {
                            if (response.success && response.data) {
                                window.strutturaCache[id] = response.data.nome;
                            }
                        }
                    });
                }
            });

            // Precarica dati venue
            venueIds.forEach(id => {
                if (!window.venueCache[id]) {
                    $.ajax({
                        url: 'index.php?r=xtravelhead/getvenue',
                        type: 'GET',
                        data: {
                            id: id
                        },
                        async: false, // Sincrono per precaricamento
                        success: function(response) {
                            if (response.success && response.data) {
                                window.venueCache[id] = response.data;
                            }
                        }
                    });
                }
            });
        }



        let tableId = "#<?= Html::encode($tableId) ?>";
        let tableData = <?= $filteredDataJson ?>;
        const currentUserLevel = <?= $jsUserLevel ?>;
        const tappaId = "<?= $th_id ?>"; // ID della tappa corrente
        const tappaData = "<?= $data ?>"; // Data della tappa corrente
        const xtappa = "<?= $tappa ?>"; // Nome della tappa corrente
        // Prepare dropdown options from PHP data

        const ruoliOptions = <?= $ruoliOptionsJson ?>;
        const strutturaOptions = <?= $strutturaOptionsJson ?>;

        const partyOptions = <?= $partyOptionsJson ?>;
        const venueOptions = <?= $venueOptionsJson ?>;
        const commessaOptions = <?= $commessaOptionsJson ?>;
        const ClidataOptions = <?= $ClidataOptionsJson ?>;
        const FordataOptions = <?= $FordataOptionsJson ?>;
        const CreditdataOptions = <?= $CreditdataOptionsJson ?>;
        const OspitidataOptions = <?= $OspitedataOptionsJson ?>
        // console.log(OspitidataOptions);
        // console.log(FordataOptions);
        // Calcola altezza disponibile (100vh meno intestazioni, pulsanti, ecc.)
        function calcTableHeight() {
            const headerOffset = 250; // Adatta in base al tuo layout
            return (window.innerHeight - headerOffset) + 'px';
        }

        // Ricalcola altezza e ridisegna colonne su resize
        $(window).on('resize', function() {
            const newHeight = calcTableHeight();
            $('.dataTables_scrollBody').css('max-height', newHeight);
            table.columns.adjust().draw(false);
        });

        preloadStructureAndVenueData();
        // ---------------------------------------------------------
        // GESTIONE SALVA TUTTO (SAVE ALL) - VERSIONE SINCRONIZZATA
        // ---------------------------------------------------------
        $('#btnsaveSelected').on('click', function(e) {
            e.preventDefault();

            // 1. Chiedi conferma
            if (!confirm("Sei sicuro di voler salvare tutte le righe presenti in tabella?")) {
                return;
            }

            var btn = $(this);
            var originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvataggio...');

            // --- NUOVO: SINCRONIZZAZIONE FORZATA INPUT -> DATATABLES ---
            // Prima di estrarre i dati, aggiorniamo la memoria di DataTables 
            // leggendo i valori dai campi input/select attualmente aperti (classe .editing)
            table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                var d = this.data(); // Dati in memoria
                var rowNode = this.node(); // Elemento HTML TR
                var $row = $(rowNode);
                var dataChanged = false;

                // Cerca solo le celle che sono in modalità modifica (.editing)
                $row.find('td.editing').each(function() {
                    var cell = table.cell(this);
                    var colIdx = cell.index().column;
                    var colName = table.column(colIdx).dataSrc(); // Es: 'ruolo', 'party', 'qta'

                    var newVal;
                    var $select = $(this).find('select');
                    var $input = $(this).find('input');

                    // Recupera il valore dall'elemento HTML
                    if ($select.length > 0) {
                        newVal = $select.val();
                    } else if ($input.length > 0) {
                        newVal = $input.val();
                        // Gestione specifica per i numeri se necessario
                        if (['qta', 'prezzo', 'tax_unit', 'fee', 'fee_perc'].includes(colName)) {
                            newVal = parseFloat(newVal) || 0;
                        }
                    }

                    // Aggiorna l'oggetto dati se abbiamo trovato un valore
                    if (newVal !== undefined) {
                        d[colName] = newVal;
                        dataChanged = true;
                    }
                });

                // Se abbiamo rilevato modifiche visive, salviamole nella memoria di DataTables
                if (dataChanged) {
                    this.data(d);
                    // Nota: non chiamiamo .draw() qui per non rallentare o chiudere gli input
                }
            });
            // -----------------------------------------------------------

            // 3. Estrai TUTTI i dati (ora aggiornati)
            var allRowsData = table.rows().data().toArray();

            // Controllo se ci sono dati
            if (allRowsData.length === 0) {
                alert("Nessun dato da salvare.");
                btn.prop('disabled', false).html(originalText);
                return;
            }

            console.log("Invio dati al server (JSON):", allRowsData);

            // 4. Invia via AJAX al Controller
            $.ajax({
                url: 'index.php?r=xtravelrow/saveallajax',
                type: 'POST',
                contentType: 'application/json; charset=utf-8',
                data: JSON.stringify({
                    rows: allRowsData,
                    th_id: "<?= $th_id ?>"
                }),
                success: function(response) {
                    console.log("Risposta server:", response);

                    if (response.success) {
                        alert('Tutti i dati sono stati salvati correttamente!');
                        if (response.data) {
                            // Sostituisce i dati in memoria e ridisegna la tabella
                            table.clear().rows.add(response.data).draw(false);
                        }
                        // Rimuovi le classi di editing e visualizza i valori aggiornati
                        $('td.editing').removeClass('editing');
                        $('tr.row-editing').removeClass('row-editing');
                        $('.save-row-btn').hide();

                        // Ridisegna la tabella per mostrare i dati "fissi" invece degli input
                        table.rows().invalidate().draw(false);

                        $('td.cell-changed').removeClass('cell-changed');
                    } else {
                        alert('Si sono verificati degli errori: \n' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Errore AJAX:", error);
                    var msg = error;
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert('Errore durante il salvataggio: ' + msg);
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });
        // ---------------------------------------------------------
        // GESTIONE SALVA TUTTO (SAVE ALL) - VERSIONE JSON (Safe for >300 rows)
        // ---------------------------------------------------------
        $('#btnsaveSelected___olddddddd').on('click', function(e) {
            e.preventDefault();

            // 1. Chiedi conferma
            if (!confirm("Sei sicuro di voler salvare tutte le righe presenti in tabella?")) {
                return;
            }

            // 2. Mostra un indicatore di caricamento
            var btn = $(this);
            var originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvataggio...');

            // 3. Estrai TUTTI i dati dalla DataTable
            // .rows() prende tutte le righe (anche quelle in altre pagine), .data() i dati, .toArray() array JS
            var allRowsData = table.rows().data().toArray();

            // Controllo se ci sono dati
            if (allRowsData.length === 0) {
                alert("Nessun dato da salvare.");
                btn.prop('disabled', false).html(originalText);
                return;
            }

            console.log("Invio dati al server (JSON):", allRowsData);

            // 4. Invia via AJAX al Controller
            $.ajax({
                url: 'index.php?r=xtravelrow/saveallajax',
                type: 'POST',

                // --- MODIFICHE FONDAMENTALI PER GRANDI QUANTITÀ DI DATI ---
                contentType: 'application/json; charset=utf-8', // Avvisa il server che arriva JSON
                data: JSON.stringify({ // Converte tutto l'oggetto in una stringa unica
                    rows: allRowsData,
                    th_id: "<?= $th_id ?>" // ID della testata
                }),
                // -----------------------------------------------------------

                success: function(response) {
                    console.log("Risposta server:", response);

                    if (response.success) {
                        alert('Tutti i dati sono stati salvati correttamente!');

                        // Rimuovi le evidenziazioni di modifica (le celle gialle)
                        $('td.cell-changed').removeClass('cell-changed');

                        // Opzionale: Ricarica la pagina se preferisci
                        // location.reload(); 
                    } else {
                        alert('Si sono verificati degli errori: \n' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Errore AJAX:", error);
                    // Prova a recuperare un messaggio più specifico se il server lo manda
                    var msg = error;
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert('Errore durante il salvataggio: ' + msg);
                },
                complete: function() {
                    // Ripristina il bottone allo stato originale
                    btn.prop('disabled', false).html(originalText);
                }
            });
        });








        // SOLUZIONE 1: Usare CSS personalizzato per fissare l'ultima colonna
        // Aggiungi questo CSS prima dell'inizializzazione della tabella
        const customFixedColumnCSS = `
<style>
/* Stile per tabella con colonna fissa */
.dataTables_scrollBody {
    position: relative;
}

/* Fissa l'ultima colonna */
.fixed-actions-column {
    position: sticky !important;
    left: 0 !important;
    background-color: white !important;
    border-left: 2px solid #dee2e6 !important;
    z-index: 10 !important;
    box-shadow: -2px 0 5px rgba(0,0,0,0.1) !important;
}

/* Assicura che l'header sia anche fisso */
.dataTables_scrollHead th.fixed-actions-column {
    position: sticky !important;
    left: 0 !important;
    background-color: #f8f9fa !important;
    border-left: 2px solid #dee2e6 !important;
    z-index: 11 !important;
    box-shadow: -2px 0 5px rgba(0,0,0,0.1) !important;
}

/* Stile per i bottoni nella colonna fissa */
.fixed-actions-column .btn {
    margin: 1px 2px;
    font-size: 12px;
    padding: 4px 8px;
}
</style>
`;

        $('head').append(customFixedColumnCSS);



        const table = $(tableId).DataTable({

            data: tableData,
            scrollCollapse: true,
            scrollY: '500px',
            scrollX: true,
            paging: false,
            searching: true,
            ordering: true,
            select: true,

            //responsive: true,
            columns: [

                {

                    data: "tr_id",
                    title: "Azioni",
                    orderable: false,
                    className: 'fixed-actions-column text-center', // Aggiungi la classe CSS personalizzata
                    width: '200px', // Larghezza fissa
                    render: function(data, type, row) {
                        return ` <button type="button" class="save-row-btn btn btn-success btn-sm" style="display:none;" >Salva</button> <button type="button" class="btn-delete btn btn-danger btn-sm" data-id="${row.tr_id}" >Elimina</button> <button type="button" class="btn-duplicate btn btn-info btn-sm mr-1" data-id="${row.tr_id}" >Duplica</button> `;
                    }
                },

                {
                    data: null,
                    title: '<input type="checkbox" id="select-all" /> Sel', // checkbox per selezionare/deselezionare tutte
                    orderable: false,
                    className: 'select-checkbox text-center',
                    render: function(data, type, row) {
                        return '<input type="checkbox" class="row-select" value="' + row.tr_id + '">';
                    }
                },
                {
                    data: 'tr_id',
                    title: 'Riga'
                }

                ,
                {
                    data: "guest",
                    title: "Ospite",
                    className: 'editable', // Cella editabile
                    render: function(data, type, row) {
                        if (!data) return "";

                        const label = OspitidataOptions[data] ? OspitidataOptions[data] : data;

                        if (type === 'display' || type === 'type') {
                            return `<span title="${label}">${label}</span>`;
                        }

                        if (type === 'filter' || type === 'sort') {
                            return `<span title="${label}">${label}</span>`;
                        }

                        return data;
                    }
                }

                ,
                {
                    data: "ruolo",
                    title: "Ruolo",
                    className: 'editable' // Cella editabile
                }

                ,
                {
                    data: "party",
                    title: "Party",
                    className: 'editable' // Cella editabile
                },
                {
                    data: "cd_cf_ft",
                    title: "Cliente",
                    className: 'editable', // Cella editabile
                    render: function(data, type, row) {
                        if (!data) return "";

                        const label = ClidataOptions[data] ? ClidataOptions[data] : data;

                        if (type === 'display' || type === 'type') {
                            //return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        if (type === 'filter' || type === 'sort') {
                            // return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        return data;
                    }
                },
                {
                    data: "sottocommessa",
                    title: "Commessa",
                    className: 'editable', // Cella editabile
                    render: function(data, type, row) {
                        if (!data) return "";

                        const label = commessaOptions[data] ?
                            commessaOptions[data] : data;

                        if (type === 'display' || type === 'type') {
                            //return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        if (type === 'filter' || type === 'sort') {
                            // return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        return data;
                    }
                }

                ,
                {
                    data: "struttura",
                    title: "Struttura",
                    className: 'editable struttura-column', // Aggiunta la classe personalizzata
                    width: '200px', // Puoi forzare la larghezza anche qui
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'type') {
                            if (!data) return "";

                            const isNumeric = !isNaN(data);

                            if (isNumeric) {
                                // Controlla se abbiamo già i dati in cache
                                if (window.strutturaCache && window.strutturaCache[data]) {
                                    // return window.strutturaCache[data];
                                    const label = window.strutturaCache[data];
                                    return `<span title="${label}">${label}</span>`;
                                }

                                if (!window.strutturaCache) {
                                    window.strutturaCache = {};
                                }

                                const cellId = 'struttura-' + row.tr_id + '-' + Math.random().toString(36).substr(2, 9);

                                setTimeout(function() {
                                    $.ajax({
                                        url: 'index.php?r=xtravelhead/getstruttura',
                                        type: 'GET',
                                        data: {
                                            id: data
                                        },
                                        success: function(response) {
                                            if (response.success && response.data) {
                                                window.strutturaCache[data] = response.data.nome;
                                                $('#' + cellId)
                                                    .text(response.data.nome)
                                                    .attr("title", response.data.nome);
                                                // Aggiorna anche i dati per la ricerca
                                                table.cell('#' + cellId).invalidate();
                                            } else {
                                                $('#' + cellId).text('Struttura non trovata per ID ' + data);
                                            }
                                        },
                                        error: function() {
                                            $('#' + cellId).text('Errore nel caricamento struttura');
                                        }
                                    });
                                }, 0);

                                return '<span id="' + cellId + '">Caricamento...</span>';
                            } else {
                                return data;
                            }
                        }

                        // Per il tipo 'filter' e 'sort', restituisce il nome se disponibile in cache
                        if (type === 'filter' || type === 'sort') {
                            if (!data) return "";

                            const isNumeric = !isNaN(data);
                            if (isNumeric && window.strutturaCache && window.strutturaCache[data]) {
                                return window.strutturaCache[data];
                            }
                            return data;
                        }

                        return data;
                    }
                },
                // Aggiungi questo oggetto nell'array columns: []
                {
                    data: "stato",
                    title: "Stato",
                    className: 'editable col-stato',
                    render: function(data, type, row) {
                        return data ? data : "Da Prenotare"; // Valore di default se vuoto
                    }
                },

                {
                    data: "fornitore",
                    title: "Fornitore",
                    className: 'editable', // Cella editabile
                    render: function(data, type, row) {
                        if (!data) return "";

                        const label = FordataOptions[data] ? FordataOptions[data] : data;

                        if (type === 'display' || type === 'type') {
                            // return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        if (type === 'filter' || type === 'sort') {
                            //return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        return data;
                    }

                },
                {
                    data: "citta",
                    title: "Venue",
                    className: 'editable', // Cella editabile,
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'type') {
                            if (!data) return "";

                            const uuidRegex = /^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/;

                            if (uuidRegex.test(data)) {
                                if (window.venueCache && window.venueCache[data]) {
                                    const venue = window.venueCache[data];
                                    // return venue.venue + '-' + venue.citta;
                                    const label = venue.venue + '-' + venue.citta;
                                    return `<span title="${label}">${label}</span>`;
                                }

                                if (!window.venueCache) {
                                    window.venueCache = {};
                                }

                                const cellId = 'venue-' + row.tr_id + '-' + Math.random().toString(36).substr(2, 9);

                                setTimeout(function() {
                                    $.ajax({
                                        url: 'index.php?r=xtravelhead/getvenue',
                                        type: 'GET',
                                        data: {
                                            id: data
                                        },
                                        success: function(response) {
                                            if (response.success && response.data) {
                                                window.venueCache[data] = response.data;
                                                $('#' + cellId).text(response.data.venue + '-' + response.data.citta)
                                                    .attr("title", response.data.venue + '-' + response.data.citta);

                                                // Aggiorna anche i dati per la ricerca
                                                table.cell('#' + cellId).invalidate();
                                            } else {
                                                $('#' + cellId).text('Città non trovata per ID ' + data);
                                            }
                                        },
                                        error: function() {
                                            $('#' + cellId).text('Errore nel caricamento venue');
                                        }
                                    });
                                }, 0);

                                return '<span id="' + cellId + '">Caricamento...</span>';
                            } else {
                                return data;
                            }
                        }

                        // Per il tipo 'filter' e 'sort', restituisce il nome se disponibile in cache
                        if (type === 'filter' || type === 'sort') {
                            if (!data) return "";

                            const uuidRegex = /^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/;
                            if (uuidRegex.test(data) && window.venueCache && window.venueCache[data]) {
                                const venue = window.venueCache[data];
                                //return venue.venue + '-' + venue.citta;
                                const label = venue.venue + '-' + venue.citta;
                                return `<span title="${label}">${label}</span>`
                            }
                            return data;
                        }

                        return data;
                    }
                }

                ,
                {
                    data: "citta_da",
                    title: "DA",
                    className: 'editable' // Cella editabile
                }

                ,
                {
                    data: "citta_a",
                    title: "A",
                    className: 'editable' // Cella editabile
                }

                ,
                {
                    data: "check_in",
                    title: "Check-in",
                    className: 'editable', // Cella editabile

                    render: function(data, type, row) {
                        if (!data) return "";

                        if (type === 'display' || type === 'filter') {
                            let date = new Date(data);
                            return date.toLocaleDateString('it-IT');
                        }

                        return data; // Mantieni il formato originale per l'editing
                    }
                }

                ,
                {
                    data: "check_out",
                    title: "Check-Out",
                    className: 'editable', // Cella editabile

                    render: function(data, type, row) {
                        if (!data) return "";

                        if (type === 'display' || type === 'filter') {
                            let date = new Date(data);
                            return date.toLocaleDateString('it-IT');
                        }

                        return data; // Mantieni il formato originale per l'editing
                    }
                }

                ,
                {
                    data: "qta",
                    title: "Notti",
                    className: 'editable' // Cella editabile
                }

                ,

                {
                    data: "cd_Ar",
                    title: "Articolo",
                    className: 'editable' // Cella editabile
                }

                ,
                {
                    data: "prezzo",
                    title: "Prezzo",
                    className: 'editable' // Cella editabile
                }

                ,
                {
                    data: "tax_unit",
                    title: "City Tax",
                    className: 'editable' // Cella editabile
                }

                ,
                {
                    data: "pnr",
                    title: "PNR",
                    className: 'editable', // Cella editabile
                    render: function(data, type, row) {
                        if (!data) return "";

                        const label = FordataOptions[data] ? FordataOptions[data] : data;

                        if (type === 'display' || type === 'type') {
                            // return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        if (type === 'filter' || type === 'sort') {
                            //return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        return data;
                    }
                }

                ,
                {
                    data: "nr_biglietto",
                    title: "Biglietto",
                    className: 'editable', // Cella editabile
                    render: function(data, type, row) {
                        if (!data) return "";

                        const label = FordataOptions[data] ? FordataOptions[data] : data;

                        if (type === 'display' || type === 'type') {
                            // return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        if (type === 'filter' || type === 'sort') {
                            //return label;
                            return `<span title="${label}">${label}</span>`;
                        }

                        return data;
                    }

                }

                ,
                {

                    data: "data_pg",
                    title: "Data Pag",
                    className: 'editable', // Cella editabile
                    render: function(data, type, row) {
                        if (!data) return "";

                        if (type === 'display' || type === 'filter') {
                            let date = new Date(data);
                            return date.toLocaleDateString('it-IT');
                        }

                        return data; // Mantieni il formato originale per l'editing
                    }
                }

                ,
                {
                    data: "cd_pg",
                    title: "Strum. Pag",
                    className: 'editable', // Cella editabile
                    render: function(data, type, row) {
                        if (!data) return "";

                        const label = CreditdataOptions[data] ? CreditdataOptions[data] : data;

                        if (type === 'display' || type === 'type') {
                            return `<span title="${label}">${label}</span>`;
                        }

                        if (type === 'filter' || type === 'sort') {
                            return `<span title="${label}">${label}</span>`;
                        }

                        return data;
                    }

                }

                ,
                {

                    data: null,
                    title: "Tot Costo",
                    render: function(data, type, row) {
                        let totale = parseFloat(row.prezzo) * parseFloat(row.qta);
                        return totale ? totale.toFixed(2) : "0.00";
                    }
                }

                ,
                {

                    data: null,
                    title: "Tot City Tax",
                    render: function(data, type, row) {
                        let totale = parseFloat(row.tax_unit) * parseFloat(row.qta);
                        return totale ? totale.toFixed(2) : "0.00";
                    }
                }

                ,
                {

                    data: null,
                    title: "Totale",
                    render: function(data, type, row) {
                        let totale = (parseFloat(row.tax_unit) * parseFloat(row.qta)) + (parseFloat(row.prezzo) * parseFloat(row.qta));
                        return totale ? totale.toFixed(2) : "0.00";
                    }
                }

                ,
                {
                    data: "fee_perc",
                    title: "Fee %",
                    className: 'editable', // Cella editabile
                    defaultContent: "N/D"
                }

                ,
                {
                    data: "fee",
                    title: "Fee",
                    className: 'editable' // Cella editabile
                }

                ,
                {
                    data: "codiva",
                    title: "IVA",
                    // className: 'editable', // Cella editabile
                    //      defaultContent: "N/A"
                },




            ],

            rowCallback: function(row, data) {
                // Rimuovi classi vecchie
                $(row).removeClass('stato-chiuso stato-da-prenotare stato-prenotato stato-cancellato stato-in-penale');

                // Applica classe in base allo stato
                if (data.stato === 'Chiuso') $(row).addClass('stato-chiuso');
                else if (data.stato === 'Da Prenotare') $(row).addClass('stato-da-prenotare');
                else if (data.stato === 'Prenotato') $(row).addClass('stato-prenotato');
                else if (data.stato === 'Cancellato') $(row).addClass('stato-cancellato');
                else if (data.stato === 'In penale') $(row).addClass('stato-in-penale');
            },
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'copy',
                    text: 'Copia'
                }

                ,
                {
                    extend: 'csv',
                    text: 'CSV'
                }

                ,
                {
                    extend: 'excel',
                    text: 'Excel'
                }

            ],
            drawCallback: function(settings) {
                var api = this.api();

                // Funzione per pulire i numeri
                var intVal = function(i) {
                    if (typeof i === 'string') return i.replace(/[\$,]/g, '') * 1;
                    if (typeof i === 'number') return i;
                    return 0;
                };

                // Otteniamo solo le righe filtrate (visibili)
                var rowsData = api.rows({
                    search: 'applied'
                }).data();

                var somme = {
                    costo: 0,
                    tax: 0,
                    totale: 0,
                    fee: 0
                };

                rowsData.each(function(rowData) {
                    // Estraiamo i valori (attenzione alla corrispondenza dei nomi nel tuo oggetto JSON)
                    var p = intVal(rowData.prezzo);
                    var q = intVal(rowData.qta);
                    var t = intVal(rowData.tax_unit);
                    var f = intVal(rowData.fee);

                    somme.costo += (p * q);
                    somme.tax += (t * q);
                    somme.totale += (p * q) + (t * q);
                    somme.fee += f;
                });

                // Scriviamo i risultati nei widget esterni con animazione di aggiornamento
                $('#widget-tot-costo').text(somme.costo.toFixed(2));
                $('#widget-tot-tax').text(somme.tax.toFixed(2));
                $('#widget-tot-generale').text(somme.totale.toFixed(2));
                $('#widget-tot-fee').text(somme.fee.toFixed(2));
            },
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();

                // Funzione per pulire i numeri
                var intVal = function(i) {
                    if (typeof i === 'string') return i.replace(/[\$,]/g, '') * 1;
                    if (typeof i === 'number') return i;
                    return 0;
                };

                // Ottieni i dati delle righe attualmente filtrate/visibili
                var rowsData = api.rows({
                    search: 'applied'
                }).data();

                // Inizializziamo i contenitori per le somme
                var somme = {
                    costo: 0,
                    tax: 0,
                    totale: 0,
                    fee: 0
                };

                // Ciclo sui dati delle righe
                rowsData.each(function(rowData) {
                    // Usiamo i nomi esatti delle proprietà del tuo oggetto JSON
                    var p = intVal(rowData.prezzo);
                    var q = intVal(rowData.qta);
                    var t = intVal(rowData.tax_unit);
                    var f = intVal(rowData.fee);

                    somme.costo += (p * q);
                    somme.tax += (t * q);
                    somme.totale += (p * q) + (t * q);
                    somme.fee += f;
                });

                // --- AGGIORNAMENTO DELLE CELLE ---
                // Invece di usare numeri fissi (24, 25...), cerchiamo la colonna per Titolo
                // Questo metodo è molto più sicuro.

                api.columns().every(function() {
                    var column = this;
                    var title = $(column.header()).text().trim();

                    if (title === "Tot Costo") {
                        $(column.footer()).html(somme.costo.toFixed(2) + ' €');
                    } else if (title === "Tot City Tax") {
                        $(column.footer()).html(somme.tax.toFixed(2) + ' €');
                    } else if (title === "Totale") {
                        $(column.footer()).html(somme.totale.toFixed(2) + ' €');
                    } else if (title === "Fee") {
                        $(column.footer()).html(somme.fee.toFixed(2) + ' €');
                    }
                });
            },
            // colReorder: true,
            language: {

                search: "Cerca:",
                lengthMenu: "Mostra _MENU_ elementi",
                info: "Mostra da _START_ a _END_ di _TOTAL_ elementi",
                infoEmpty: "Nessun dato disponibile",
                infoFiltered: "(filtrato da _MAX_ elementi totali)",
                zeroRecords: "Nessun risultato trovato",
                paginate: {
                    first: "Primo",
                    last: "Ultimo",
                    next: "Successivo",
                    previous: "Precedente"
                }
            }

            ,

        });



        // Dopo l'inizializzazione della tabella, aggiungi questo per gestire la ricerca personalizzata
        table.on('draw', function() {
            // Forza l'aggiornamento delle celle che potrebbero aver cambiato contenuto
            setTimeout(function() {
                //  table.columns([5, 6]).invalidate('data').draw(false); // Colonne struttura e venue
            }, 1000);
        });


        // Inizializza dopo che la tabella è stata creata
        table.on('init.dt', function() {
            setTimeout(initCustomFixedColumn, 100);
        });



        // Funzione per scorrere alla riga specificata
        function scrollToRow(row) {
            // Ottieni l'elemento DOM della riga
            const rowNode = row.node();

            if (!rowNode) {
                console.error('Nodo della riga non trovato');
                return;
            }

            // Trova il contenitore di scorrimento in modo più affidabile
            // Prima cerca il contenitore standard di DataTables
            let scrollContainer = $(rowNode).closest('.dataTables_scrollBody');

            // Se non trovato, cerca il genitore più vicino che ha scorrimento
            if (scrollContainer.length === 0) {
                // Trova la tabella DataTable a cui appartiene la riga
                const dataTable = $(rowNode).closest('table.dataTable');

                // Trova il primo genitore che ha overflow: auto o overflow: scroll
                scrollContainer = dataTable.parents().filter(function() {
                    const overflow = $(this).css('overflow');
                    const overflowY = $(this).css('overflow-y');
                    return overflow === 'auto' || overflow === 'scroll' || overflowY === 'auto' || overflowY === 'scroll';
                }).first();
            }

            // Se ancora non trovato, prova con la finestra del browser
            if (scrollContainer.length === 0) {
                console.log('Contenitore di scorrimento specifico non trovato, uso la finestra del browser');
                scrollContainer = $(window);
            }

            console.log("Contenitore di scorrimento trovato:", scrollContainer.length > 0);

            // Metodo 1: Utilizza scrollIntoView nativo se possibile
            try {
                rowNode.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                console.log("Scrolling con scrollIntoView");
            } catch (e) {
                console.log("scrollIntoView non disponibile o ha fallito, uso jQuery", e);

                // Metodo 2: Fallback con jQuery
                try {

                    // Se il contenitore è la finestra, usa scrollTop specifico
                    if (scrollContainer.is($(window))) {
                        $('html, body').animate({
                                scrollTop: $(rowNode).offset().top - ($(window).height() / 2)
                            }

                            , 500);
                    } else {

                        // Per altri contenitori
                        scrollContainer.animate({
                                scrollTop: scrollContainer.scrollTop() + $(rowNode).position().top - (scrollContainer.height() / 2) + ($(rowNode).height() / 2)
                            }

                            , 500);
                    }

                    console.log("Scrolling con jQuery animation");
                } catch (e) {
                    console.error("Anche il fallback jQuery è fallito", e);
                }
            }

            // Evidenzia temporaneamente la riga indipendentemente dal metodo di scorrimento
            highlightRow(row);
        }

        // Funzione per evidenziare temporaneamente una riga
        function highlightRow(row) {
            // Ottieni l'elemento DOM della riga
            const rowNode = row.node();

            if (!rowNode) return;

            // Rimuovi prima qualsiasi evidenziazione precedente
            $('.highlight-new-row').removeClass('highlight-new-row');

            // Aggiungi classe per evidenziare
            $(rowNode).addClass('highlight-new-row');

            // Evidenzia con un colore ben visibile
            $(rowNode).css('background-color', '#fff700');

            // Rimuovi la classe e lo stile dopo un po' di tempo
            setTimeout(function() {
                    $(rowNode).removeClass('highlight-new-row');
                    $(rowNode).css('background-color', ''); // Rimuovi lo stile inline
                }

                , 3000); // Rimuovi dopo 3 secondi
        }

        // Assicurati che lo stile CSS per l'evidenziazione sia presente
        if ($('style#highlight-style').length === 0) {
            $("<style id='highlight-style'>").prop("type", "text/css").html(` .highlight-new-row {
                transition: background-color 0.5s ease;
                animation: pulse-highlight 1.5s ease-in-out infinite;
            }

            @keyframes pulse-highlight {
                0% {
                    background-color: #fff700;
                }

                50% {
                    background-color: #ffffcc;
                }

                100% {
                    background-color: #fff700;
                }
            }

            `).appendTo("head");
        }


        $('#btnAddNewstr').on('click', function(e) {
            e.preventDefault();
            // Carica il contenuto della form via AJAX
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['xstruttura/create']) ?>',
                type: 'GET',
                success: function(data) {
                    $('#modalContentStr').html(data);
                    $('#strutturaModal').modal('show');
                },
                error: function() {
                    alert('Errore nel caricamento della form');
                }
            });
        });



        $('#btnAddNewosp').on('click', function(e) {
            e.preventDefault();

            $.ajax({
                url: '<?= \yii\helpers\Url::to(["xroomlist/createaj"]) ?>',
                data: {
                    th_id: <?= (int)$th_id ?>
                },
                type: 'GET',
                success: function(data) {
                    // Popola il contenitore della modale
                    $('#modalContent2a').html(data);

                    // Mostra la modale
                    $('#xroomlistModal').modal('show');

                    // Inizializza Select2 all'interno della modale
                    $('#modalContent2a .select2').select2({
                        dropdownParent: $('#xroomlistModal'),
                        allowClear: true,
                        width: '100%'
                    });
                },
                error: function() {
                    alert('Errore nel caricamento della form');
                }
            });
        });







        $('#btnAddNewrl').on('click', function(e) {
            e.preventDefault();

            // Carica il contenuto della form via AJAX
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['xruoli/createaj']) ?>',
                type: 'GET',
                success: function(data) {
                    $('#modalContentRl').html(data);
                    $('#ruoloModal').modal('show');
                },
                error: function() {
                    alert('Errore nel caricamento della form');
                }
            });
        });
        $('#btnAddNewprt').on('click', function(e) {
            e.preventDefault();

            // Carica il contenuto della form via AJAX
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['xparty/createaj']) ?>',
                type: 'GET',
                success: function(data) {
                    $('#modalContentPrt').html(data);
                    $('#partyModal').modal('show');
                },
                error: function() {
                    alert('Errore nel caricamento della form');
                }
            });
        });
        // Handler per il click sul pulsante "Aggiungi Nuova Riga"
        $('#btnAddNewRow').on('click', function() {

            // Mostra una finestra di dialogo per confermare l'aggiunta
            if (confirm("Sei sicuro di voler aggiungere una nuova riga?")) {
                // 1. Recupera il parametro 'tappa' dall'URL corrente del browser
                const urlParams = new URLSearchParams(window.location.search);
                const tappaValue = urlParams.get('tappa');

                // Prepara i dati per la nuova riga
                const newRowData = {
                    tr_head: tappaId, // ID della prenotazione
                    data: tappaData, // Data della tappa
                    // Inizializza gli altri campi con valori vuoti o predefiniti
                    guest: "",
                    ruolo: "",
                    party: "",
                    struttura: "",
                    citta: xtappa,
                    citta_da: "",
                    citta_a: "",
                    check_in: null,
                    check_out: null,
                    qta: 1, // Valore predefinito
                    cd_Ar: "",
                    prezzo: 0.00, // Valore predefinito
                    tax_unit: 0.00, // Valore predefinito
                    pnr: "",
                    nr_biglietto: "",
                    data_pg: null,
                    cd_pg: "",
                    fee_perc: 0, // Valore predefinito
                    fee: 0.00, // Valore predefinito
                    codiva: "",
                    sottocommessa: "",
                    stato: "Prenotato"
                }

                ;
                // 2. Costruisci l'URL aggiungendo il parametro tappa
                // Usiamo un template literal per inserire la variabile nell'URL
                const ajaxUrl = 'index.php?r=xtravelrow/create-ajax&tappa=' + encodeURIComponent(tappaValue);
                // Invia richiesta AJAX per creare la nuova riga
                $.ajax({

                    url: ajaxUrl,
                    type: 'POST',
                    data: {
                        data: newRowData
                    }

                    ,
                    success: function(response) {
                            if (response.success) {
                                // Aggiungi la riga alla tabella con l'ID ritornato dal server
                                console.log('Nuova riga aggiunta con ID:', response);

                                const rowWithId = {
                                    ...newRowData,
                                    tr_id: response.data.tr_id // ID della nuova riga restituito dal server
                                }

                                ;

                                // Aggiungi la riga alla tabella
                                const newRow = table.row.add(rowWithId).draw();
                                // Scorrere alla riga appena duplicata
                                console.log('Nuova riga:', newRow);
                                scrollToRow(newRow);

                                // Evidenzia temporaneamente la riga duplicata
                                highlightRow(newRow);

                                // Mostra messaggio di successo
                                //  alert('Nuova riga aggiunta con successo');
                            } else {
                                alert('Errore durante l\'aggiunta della riga: ' + response.message);
                            }
                        }

                        ,
                    error: function(xhr, status, error) {
                        console.error('Errore durante la creazione della riga', error);
                        alert('Errore durante la creazione della riga: ' + error);
                    }
                });
            }
        });


        // Aggiungiamo l'evento click per il pulsante "Duplica"
        $(document).on('click', '.btn-duplicate', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Otteniamo l'ID della riga da duplicare
            const rowId = $(this).data('id');

            // Troviamo i dati della riga
            const rowIndex = table.rows().indexes().filter(function(value, index) {
                return table.row(value).data().tr_id == rowId;
            });

            if (rowIndex.length === 0) {
                alert('Riga non trovata!');
                return;
            }

            const rowData = table.row(rowIndex[0]).data();

            // Chiediamo conferma
            if (confirm("Sei sicuro di voler duplicare questa riga?")) {
                const urlParams = new URLSearchParams(window.location.search);
                const tappaValue = urlParams.get('tappa');




                // Prepariamo i dati per la duplicazione (escludiamo l'ID)
                const duplicateData = {
                    // Manteniamo l'ID della tappa e la data
                    tappa_id: rowData.tappa_id || tappaId,
                    data: rowData.data || tappaData,

                    // Copiamo tutti gli altri campi dalla riga originale
                    guest: rowData.guest || "",
                    ruolo: rowData.ruolo || "",
                    party: rowData.party || "",
                    struttura: rowData.struttura || "",
                    citta: rowData.citta || "",
                    citta_da: rowData.citta_da || "",
                    citta_a: rowData.citta_a || "",
                    check_in: rowData.check_in,
                    check_out: rowData.check_out,
                    qta: rowData.qta || 0,
                    cd_Ar: rowData.cd_Ar || "",
                    prezzo: rowData.prezzo || 0,
                    tax_unit: rowData.tax_unit || 0,
                    pnr: rowData.pnr || "",
                    nr_biglietto: rowData.nr_biglietto || "",
                    data_pg: rowData.data_pg,
                    cd_pg: rowData.cd_pg || "",
                    fee_perc: rowData.fee_perc || 0,
                    fee: rowData.fee || 0,
                    codiva: rowData.codiva || "",
                    sottocommessa: rowData.sottocommessa || "",
                    cd_cf_ft: rowData.cd_cf_ft || "",
                    fornitore: rowData.fornitore || "",
                    stato: "Prenotato"
                }

                ;
                const ajaxUrl2 = 'index.php?r=xtravelrow/duplicate-ajax&tappa=' + encodeURIComponent(tappaValue);

                // Inviamo la richiesta AJAX per duplicare la riga
                $.ajax({

                    url: ajaxUrl2,
                    type: 'POST',
                    data: {
                        id: rowId, // ID della riga originale (per riferimento)
                        data: duplicateData
                    }

                    ,
                    success: function(response) {
                            if (response.success) {
                                // Aggiungiamo la nuova riga duplicata alla tabella
                                const dtRow = table.row.add(response.data).draw(false); // ✅
                                scrollToRow(dtRow); // ✅
                                highlightRow(dtRow);
                                // alert('Riga duplicata con successo!');
                                // Scorrere alla riga appena duplicata
                                // scrollToRow(newRow);

                                // Evidenzia temporaneamente la riga duplicata
                                // highlightRow(newRow);
                            } else {
                                alert('Errore durante la duplicazione della riga: ' + response.message);
                            }
                        }

                        ,
                    error: function(xhr, status, error) {
                        console.error('Errore durante la duplicazione della riga', error);
                        alert('Errore durante la duplicazione della riga: ' + error);
                    }
                });
            }
        });

        // Gestione editing delle celle
        $(tableId + ' tbody').on('click', 'td.editable', function() {
            const cell = table.cell(this);
            const rowIdx = table.row($(this).closest('tr')).index();
            const colIdx = table.column($(this)).index();
            const rowData = table.row(rowIdx).data();
            const cellData = cell.data();
            // Ottieni il nome della colonna usando l'API di DataTables
            const columnName = table.column(colIdx).dataSrc();
            // Se già in modalità editing, non creare un altro input
            if ($(this).hasClass('editing')) return;

            // Marca la riga come in fase di editing
            $(this).closest('tr').addClass('row-editing');
            $(this).closest('tr').find('.save-row-btn').show();

            // Input diversi per tipi di colonne diversi
            let inputElement;

            // Seleziona il tipo di input in base alla colonna
            if (['check_in', 'check_out', 'data_pg'].includes(columnName)) {
                // Date fields (check_in, check_out, data_pg)
                const dateValue = cellData ? new Date(cellData) : new Date();
                const year = dateValue.getFullYear();
                const month = String(dateValue.getMonth() + 1).padStart(2, '0');
                const day = String(dateValue.getDate()).padStart(2, '0');

                const formattedDate = `${year}-${month}-${day}`;
                inputElement = `<input type="date" value="${formattedDate}" class="form-control">`;
            } else if (['qta', 'prezzo', 'tax_unit', 'fee_perc', 'fee'].includes(columnName)) {
                // Numeric fields
                inputElement = `<input type="number" value="${cellData}" step="0.01" min="0" class="form-control" data-column-name="${columnName}">`;
            } else if (columnName === 'ruolo') {
                // Ruolo - dropdown dal database xruoli
                inputElement = `<select class="form-control dropdown-select2">`;

                // Aggiungi opzione vuota
                inputElement += `<option value=""${!cellData ? " selected" : ""}></option>`;

                // Aggiungi le opzioni dal database
                Object.entries(ruoliOptions).forEach(([value, text]) => {
                    inputElement += `<option value="${value}"${cellData === value ? " selected" : ""}>${text}</option>`;
                });
                inputElement += `</select>`;
            } else if (columnName === 'struttura') {
                // Ruolo - dropdown dal database xruoli
                inputElement = `<select class="form-control dropdown-select2" data-placeholder="Seleziona Struttura">`;

                // Aggiungi opzione vuota
                inputElement += `<option value=""${!cellData ? " selected" : ""}></option>`;

                // Aggiungi le opzioni dal database
                Object.entries(strutturaOptions).forEach(([value, text]) => {
                    inputElement += `<option value="${value}"${cellData === value ? " selected" : ""}>${text}</option>`;
                });
                inputElement += `</select>`;

            } else if (columnName === 'party') {
                // Party - dropdown dal database party
                inputElement = `<select class="form-control dropdown-select2" data-placeholder="Seleziona Party">`;

                // Aggiungi opzione vuota
                inputElement += `<option value=""${!cellData ? " selected" : ""}></option>`;

                // Aggiungi le opzioni dal database
                Object.entries(partyOptions).forEach(([value, text]) => {
                    inputElement += `<option value="${value}"${cellData === value ? " selected" : ""}>${text}</option>`;
                });
                inputElement += `</select>`;
            } else if (columnName === 'sottocommessa') {
                // Party - dropdown dal database party
                inputElement = `<select class="form-control dropdown-select2" data-placeholder="Seleziona Commessa">`;

                // Aggiungi opzione vuota
                inputElement += `<option value=""${!cellData ? " selected" : ""}></option>`;

                // Aggiungi le opzioni dal database
                Object.entries(commessaOptions).forEach(([value, text]) => {
                    inputElement += `<option value="${value}"${cellData === value ? " selected" : ""}>${text}</option>`;
                });
                inputElement += `</select>`;
            } else if (columnName === 'cd_cf_ft') {
                // Party - dropdown dal database party
                inputElement = `<select class="form-control dropdown-select2">`;

                // Aggiungi opzione vuota
                inputElement += `<option value=""${!cellData ? " selected" : ""}></option>`;

                // Aggiungi le opzioni dal database
                Object.entries(ClidataOptions).forEach(([value, text]) => {
                    inputElement += `<option value="${value}"${cellData === value ? " selected" : ""}>${text}</option>`;
                });
                inputElement += `</select>`;
            } else if (columnName === 'fornitore') {
                // Party - dropdown dal database party
                inputElement = `<select class="form-control dropdown-select2" data-placeholder="Seleziona Fornitore">`;

                // Aggiungi opzione vuota
                inputElement += `<option value=""${!cellData ? " selected" : ""}></option>`;

                // Aggiungi le opzioni dal database
                Object.entries(FordataOptions).forEach(([value, text]) => {
                    inputElement += `<option value="${value}"${cellData === value ? " selected" : ""}>${text}</option>`;
                });
                inputElement += `</select>`;
            } else if (columnName === 'guest') {
                // Party - dropdown dal database party
                inputElement = `<select class="form-control dropdown-select2" data-placeholder="Seleziona Ospite">`;

                // Aggiungi opzione vuota
                inputElement += `<option value=""${!cellData ? " selected" :
                 ""}></option>`;

                // Aggiungi le opzioni dal database
                Object.entries(OspitidataOptions).forEach(([value, text]) => {
                    inputElement += `<option value="${value}"$
                    {cellData === value ? " selected" : ""}>${text}</option>`;
                });
                inputElement += `</select>`;
            } else if (columnName === 'cd_pg') {
                // Party - dropdown dal database party
                inputElement = `<select class="form-control dropdown-select2" data-placeholder="Seleziona Pag.">`;

                // Aggiungi opzione vuota
                inputElement += `<option value=""${!cellData ? " selected" : ""}></option>`;

                // Aggiungi le opzioni dal database
                Object.entries(CreditdataOptions).forEach(([value, text]) => {
                    inputElement += `<option value="${value}"${cellData === value ? " selected" : ""}>${text}</option>`;
                });
                inputElement += `</select>`;
            } else if (columnName === 'citta') {
                // Party - dropdown dal database party
                inputElement = `<select class="form-control dropdown-select2" data-placeholder="Seleziona Città">`;

                // Aggiungi opzione vuota
                inputElement += `<option value=""${!cellData ? " selected" : ""}></option>`;

                // Aggiungi le opzioni dal database
                Object.entries(venueOptions).forEach(([value, text]) => {
                    inputElement += `<option value="${value}"${cellData === value ? " selected" : ""}>${text}</option>`;
                });
                inputElement += `</select>`;
            } else if (['citta_da', 'citta_a'].includes(columnName)) {
                // City dropdowns with AJAX
                inputElement = `<select class="form-control dropdown-select2-ajax" data-column="${columnName}">`;

                if (cellData) {
                    inputElement += `<option value="${cellData}" selected>${cellData}</option>`;
                }

                inputElement += `</select>`;
            } else if (columnName === 'cd_Ar') {
                // Nuova implementazione per cd_Ar (Articolo) con AJAX
                inputElement = `<select class="form-control dropdown-select2-article">`;

                if (cellData) {
                    inputElement += `<option value="${cellData}" selected>${cellData}</option>`;
                }

                inputElement += `</select>`;

            } else if (columnName === 'codiva') {
                // Nuova implementazione per aliquota con AJAX
                inputElement = `<select class="form-control dropdown-select2-aliquota">`;

                if (cellData) {
                    inputElement += `<option value="${cellData}" selected>${cellData}</option>`;
                }

                inputElement += `</select>`;
            } else if (columnName === 'stato') {
                const stati = ["Da Prenotare", "Prenotato", "Cancellato", "Chiuso", "In penale"];
                inputElement = `<select class="form-control">`;
                stati.forEach(s => {
                    inputElement += `<option value="${s}"${cellData === s ? " selected" : ""}>${s}</option>`;
                });
                inputElement += `</select>`;
            } else {
                inputElement = `<input type="text" value="${cellData || ''}" class="form-control" >`;
            }

            // Aggiungi elemento input alla cella
            $(this).addClass('editing').html(inputElement);

            // Inizializza Select2 per i dropdown se presente
            $(this).find('.dropdown-select2').select2({
                width: '100%',

                dropdownParent: $('body'), // Attacca il dropdown al body per evitare problemi di z-index
                dropdownCssClass: 'select2-dropdown-above-table',
                // --- AGGIUNTE PER ALLOW CLEAR ---
                allowClear: true,
                placeholder: function() {
                    // Usa il placeholder specifico definito nell'HTML, 
                    // o uno generico se manca
                    return $(this).data('placeholder');
                }
            });

            // Initialize AJAX-based Select2 for city dropdowns
            $(this).find('.dropdown-select2-ajax').select2({
                width: '100%',
                // dropdownParent: $(this),
                //   dropdownCssClass: 'select2-dropdown-above',
                dropdownParent: $('body'), // Attacca il dropdown al body per evitare problemi di z-index
                dropdownCssClass: 'select2-dropdown-above-table',
                minimumInputLength: 2, // Start searching after 2 characters

                ajax: {
                    url: 'index.php?r=xtravelhead/getcities', // Path to your controller action
                    dataType: 'json',
                    delay: 250, // Wait 250ms after typing stops before sending the request

                    data: function(params) {
                            return {
                                q: params.term // Search term
                            }

                            ;
                        }

                        ,
                    processResults: function(data) {
                            return data;
                        }

                        ,
                    cache: true
                }

                ,
                placeholder: 'Cerca città...',
                allowClear: true,
                language: {
                    inputTooShort: function(args) {
                            const remainingChars = args.minimum - args.input.length;
                            return 'Inserisci almeno ' + remainingChars + ' caratter' + (remainingChars === 1 ? 'e' : 'i');
                        }

                        ,
                    noResults: function() {
                            return 'Nessun risultato trovato';
                        }

                        ,
                    searching: function() {
                        return 'Ricerca in corso...';
                    }
                }

            });

            // Initialize AJAX-based Select2 for articles (cd_Ar)
            $(this).find('.dropdown-select2-article').select2({

                width: '100%',
                dropdownParent: $('body'),
                dropdownCssClass: 'select2-dropdown-above-table',
                minimumInputLength: 2,
                ajax: {

                    url: 'index.php?r=xtravelhead/getart',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                            return {
                                q: params.term, // Parametro fisso 'acc' come richiesto

                            }

                            ;
                        }

                        ,
                    processResults: function(data) {
                            return data;
                        }

                        ,
                    cache: true
                }

                ,
                placeholder: 'Cerca articolo...',
                allowClear: true,
                language: {
                    inputTooShort: function(args) {
                            const remainingChars = args.minimum - args.input.length;
                            return 'Inserisci almeno ' + remainingChars + ' caratter' + (remainingChars === 1 ? 'e' : 'i');
                        }

                        ,
                    noResults: function() {
                            return 'Nessun risultato trovato';
                        }

                        ,
                    searching: function() {
                        return 'Ricerca in corso...';
                    }
                }
            });

            $(this).find('.dropdown-select2-aliquota').select2({

                width: '100%',
                dropdownParent: $('body'),
                dropdownCssClass: 'select2-dropdown-above-table',
                minimumInputLength: 2,
                ajax: {

                    url: 'index.php?r=xtravelhead/getali',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                            return {
                                q: params.term, // Parametro fisso 'acc' come richiesto

                            }

                            ;
                        }

                        ,
                    processResults: function(data) {
                            return data;
                        }

                        ,
                    cache: true
                }

                ,
                placeholder: 'Cerca aliquota...',
                allowClear: true,
                language: {
                    inputTooShort: function(args) {
                            const remainingChars = args.minimum - args.input.length;
                            return 'Inserisci almeno ' + remainingChars + ' caratter' + (remainingChars === 1 ? 'e' : 'i');
                        }

                        ,
                    noResults: function() {
                            return 'Nessun risultato trovato';
                        }

                        ,
                    searching: function() {
                        return 'Ricerca in corso...';
                    }
                }
            });


            // Focus sull'elemento
            if ($(this).find('select.dropdown-select2').length) {
                $(this).find('select.dropdown-select2').select2('focus');
            } else {
                $(this).find('input, select').focus();
            }

            // Gestione keydown sull'input o select
            $(this).find('input, select').on('keydown', function(e) {
                if (e.key === 'Enter') {
                    // Salva con il tasto Enter
                    $(this).closest('tr').find('.save-row-btn').trigger('click');
                } else if (e.key === 'Escape') {

                    // Annulla con il tasto Escape
                    if ($(this).is('select.dropdown-select2') || $(this).is('select.dropdown-select2-ajax') || $(this).is('select.dropdown-select2-article') || $(this).is('select.dropdown-select2-aliquota')) {
                        $(this).select2('destroy');
                    }

                    $(this).closest('td').removeClass('editing').html(cellData || '');
                    $(this).closest('tr').removeClass('row-editing');
                    $(this).closest('tr').find('.save-row-btn').hide();
                }
            });
        });

        // Gestione click sul bottone Salva
        $(tableId + ' tbody').on('click', '.save-row-btn', function() {
            const row = $(this).closest('tr');
            const rowIdx = table.row(row).index();
            const rowData = table.row(rowIdx).data();
            let dataChanged = false;
            // Processa tutte le celle modificate nella riga
            row.find('td.editing').each(function() {
                const cell = table.cell(this);
                const colIdx = table.column($(this)).index();

                // Ottieni il nome della colonna
                const columnName = table.column(colIdx).dataSrc();

                // ALTERNATIVA: usa anche l'attributo data-column-name se presente
                const inputColumnName = $(this).find('input, select').attr('data-column-name');


                const oldValue = cell.data();
                let newValue;

                // Recupera il valore in base al tipo di elemento
                if ($(this).find('select').length > 0) {
                    if ($(this).find('select').hasClass('dropdown-select2') ||
                        $(this).find('select').hasClass('dropdown-select2-ajax') ||
                        $(this).find('select').hasClass('dropdown-select2-article') ||
                        $(this).find('select').hasClass('dropdown-select2-aliquota')) {
                        // Usa direttamente .val() invece di .select2('val')
                        newValue = $(this).find('select').val();
                        console.log("Select2 value for column " + colIdx + ": ", newValue);

                        // Distruggi l'istanza di Select2 se esiste
                        if ($.fn.select2 && $(this).find('select').data('select2')) {
                            $(this).find('select').select2('destroy');
                        }
                    } else {
                        newValue = $(this).find('select').val();
                    }
                } else {
                    newValue = $(this).find('input').val();
                }

                console.log('Old Value:', oldValue);
                console.log('New Value:', newValue);

                // Usa il nome della colonna più affidabile
                const finalColumnName = inputColumnName || columnName;
                // Converti valori se necessario
                if (['check_in', 'check_out', 'data_pg'].includes(finalColumnName)) {
                    console.log('Processing date field:', finalColumnName, 'Original value:', newValue);

                    // Date fields
                    if (newValue) {
                        // Converti formato data per il database
                        const dateParts = newValue.split('-');

                        if (dateParts.length === 3) {
                            const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
                            if (!dateRegex.test(newValue)) {
                                console.warn('Formato data non valido:', newValue);
                                // Opzionalmente, potresti convertire qui se necessario
                            }
                        }
                    }
                } else if (['qta', 'prezzo', 'tax_unit', 'fee_perc', 'fee'].includes(finalColumnName)) {
                    // Numeric fields
                    newValue = parseFloat(newValue) || 0;
                }
                // Aggiorna dati solo se cambiati
                if (oldValue != newValue) {
                    console.log('UPDATING field:', finalColumnName, 'from', oldValue, 'to', newValue);

                    // Aggiorna il modello dati usando il nome della colonna
                    rowData[finalColumnName] = newValue;

                    // Evidenzia modifica con animazione
                    $(this).addClass('cell-changed');
                    dataChanged = true;
                } else {
                    console.log('NO CHANGE for field:', finalColumnName);
                }

                // Rimuovi la classe editing e ripristina la visualizzazione
                $(this).removeClass('editing');
            });

            // Aggiorna dati della riga e ridisegna
            if (dataChanged) {
                // Qui dovresti inviare i dati aggiornati al server
                console.log("Dati prefunzione:", rowData);
                calcolaQtaAutomatico(rowIdx);

                aggiornaCampiDerivati(rowData);
                mettiiva(rowIdx);
                console.log("Dati aggiornati:", rowData);
                // Chiamata AJAX per salvare le modifiche
                $.ajax({

                    url: 'index.php?r=xtravelrow/update-ajax',
                    type: 'POST',
                    data: {
                        id: rowData.tr_id,
                        data: rowData
                    }

                    ,
                    success: function(response) {
                            console.log('Dati salvati con successo', response);
                            // Mostra messaggio di successo
                            // AGGIORNAMENTO FONDAMENTALE:
                            // response.data contiene gli attributi ricalcolati dal server (incluso il fornitore)
                            //  table.row(rowIdx).data(response.data).draw(false);
                            //var rowSelector = table.row(rowIdx);
                            //rowSelector.data(response.model); // Aggiorna i dati in memoria
                            //table.draw(false); // Ridisegna la tabella

                            if (response.success || response.model) {
                                // Aggiorniamo i dati della riga con quelli tornati dal server
                                // Usiamo l'istanza corretta della riga tramite l'ID per sicurezza
                                const currentRow = table.row(function(idx, data, node) {
                                    return data.tr_id === rowData.tr_id;
                                });

                                if (currentRow.any()) {
                                    // Aggiorna i dati e ridisegna solo questa riga
                                    currentRow.data(response.model || response.data).draw(false);

                                    // Rimuovi eventuali classi residue di editing
                                    $(currentRow.node()).removeClass('row-editing');
                                    $(currentRow.node()).find('td').removeClass('editing cell-changed');
                                    $(currentRow.node()).find('.save-row-btn').hide();
                                }

                                // Piccolo feedback visivo invece dell'alert che blocca l'esecuzione
                                console.log('Riga ' + rowData.tr_id + ' salvata.');
                            } else {
                                alert('Errore nel salvataggio: ' + (response.message || 'Errore generico'));
                            }


                            alert('Modifiche salvate con successo');

                            //  alert('Modifiche salvate con successo');


                        }

                        ,
                    error: function(xhr, status, error) {
                        console.error('Errore durante il salvataggio', error);
                        alert('Errore durante il salvataggio: ' + error);
                    }
                });

                // Ridisegna la riga con i nuovi dati
                table.row(rowIdx).data(rowData).draw(false);
            }

            // Resetta lo stato di editing della riga
            row.removeClass('row-editing');
            $(this).hide();
        });

        $(document).on('click', '.btn-delete', function(e) {
            // Ferma qualsiasi propagazione dell'evento
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            // Ottieni l'ID del record
            var trId = $(this).data('id');

            // Prima conferma
            var conferma1 = confirm("Sei sicuro di voler eliminare questo record?");

            if (conferma1 === false) {
                console.log("Prima conferma annullata");
                return false;
            }
            /*
                        // Seconda conferma
                        var conferma2 = confirm("Sei DAVVERO sicuro?");

                        if (conferma2 === false) {
                            console.log("Seconda conferma annullata");
                            return false;
                        }

                        // Terza conferma
                        var conferma3 = confirm("Eliminazione IRREVERSIBILE. Procedere?");

                        if (conferma3 === false) {
                            console.log("Terza conferma annullata");
                            return false;
                        }
            */
            // Se arriviamo qui, tutte le conferme sono state accettate
            console.log("Tutte le conferme accettate, procedo con l'eliminazione");

            // Esegui la richiesta AJAX per l'eliminazione
            $.ajax({

                url: 'index.php?r=xtravelhead/eliminarecord',
                type: 'post',
                data: {
                    id: trId
                }

                ,
                success: function(response) {
                        console.log(response);
                        alert("Record eliminato con successo");
                        location.reload();
                    }

                    ,

                error: function(xhr, status, error) {
                    alert("Errore durante l'eliminazione: " + error);
                }
            });

            // Assicurati che nulla venga eseguito dopo questa funzione
            return false;
        });

        function aggiornaCampiDerivati_old(rowData) {
            const qta = parseFloat(rowData.qta) || 0;
            const prezzo = parseFloat(rowData.prezzo) || 0;
            const tax = parseFloat(rowData.tax_unit * rowData.qta) || 0;
            const tax_unit = parseFloat(rowData.tax_unit) || 0;
            const fee_perc = parseFloat(rowData.fee_perc) || 0;
            const fee = parseFloat(rowData.fee) || 0;

            // Fai tutto dopo aver recuperato l'aliquota
            getAliquotaByCodiva(rowData.codiva, function(aliquota) {
                if (isNaN(aliquota)) aliquota = 22;

                // Totale parziale
                const totale = prezzo * qta + tax;
                rowData.totale = totale;
                console.log('Totale parziale calcolato:', totale);
                // Fee
                let vfee = fee;
                //  rowData.tax = tax_unit * qta;
                if (fee_perc > 0) {
                    vfee = (prezzo * qta + (tax_unit * qta)) * fee_perc / 100;
                } else {
                    vfee = fee;
                }
                rowData.fee = vfee;
                console.log('Fee calcolata:', vfee);
                // Imponibile + fee
                const imponibile = (totale * 100) / (100 + aliquota);
                rowData.imponibile = imponibile + vfee;
                console.log('Imponibile calcolato:', imponibile);
                // IVA (su imponibile - fee)
                const iva = ((rowData.imponibile * aliquota) / 100) - vfee;
                rowData.iva = iva;
                console.log('IVA calcolata:', iva);
                // Totale generale
                rowData.totalegenerale = rowData.imponibile + iva + tax;
                console.log('Totale generale calcolato:', rowData.totalegenerale);
                // Totale fattura
                rowData.totfattura = rowData.imponibile + tax;
                console.log('Totale fattura calcolato:', rowData.totfattura);
                // ✅ A questo punto puoi aggiornare la tabella o riga se serve
                // es: table.row(index).data(rowData).draw(false);
            });
        }

        function aggiornaCampiDerivati(rowData) {
            const qta = parseFloat(rowData.qta) || 0;
            const prezzo = parseFloat(rowData.prezzo) || 0;
            const tax = parseFloat(rowData.tax_unit * rowData.qta) || 0;
            const tax_unit = parseFloat(rowData.tax_unit) || 0;
            const fee_perc = parseFloat(rowData.fee_perc) || 0;
            const fee = parseFloat(rowData.fee) || 0;

            // Fai tutto dopo aver recuperato l'aliquota
            getAliquotaByCodiva(rowData.codiva, function(aliquota) {
                if (isNaN(aliquota)) aliquota = 22;

                // Totale parziale
                const totale = prezzo * qta + tax;
                rowData.totale = totale;
                console.log('Totale parziale calcolato:', totale);

                // Calcoliamo il totale del servizio (senza fee) che ci serve per le percentuali
                const totale_servizio = (prezzo * qta) + (tax_unit * qta);

                // Facciamo una chiamata veloce per scoprire la classe dell'articolo
                $.ajax({
                    // Adatta l'URL se necessario (puoi usare la variabile $ajaxUrl se l'hai definita in PHP)
                    url: 'index.php?r=xtravelrow/get-classe-articolo',
                    type: 'GET',
                    data: {
                        cd_ar: rowData.cd_Ar
                    },
                    success: function(response) {

                        let classeArticolo = response.success ? response.classe : '';

                        // Inizializziamo le variabili per sicurezza
                        let vfee = parseFloat(fee) || 0;
                        let vfee_perc = parseFloat(fee_perc) || 0;

                        // --- ECCO LA LOGICA CORRETTA ---
                        if (classeArticolo === 'TRVACC') {
                            if (rowData.cd_Ar === 'ACC_FEE_FUORIORA') {
                                // Eccezione: La fee è manuale, ricalcoliamo la percentuale al volo
                                vfee_perc = totale_servizio > 0 ? (vfee * 100) / totale_servizio : 0;
                            } else {
                                // Regola standard TRVACC: Calcoliamo la fee dalla percentuale
                                vfee = totale_servizio * (vfee_perc / 100);
                            }
                        } else if (classeArticolo === 'TRVBIG' || rowData.cd_Ar === 'BIG_FEE_FUORIORA') {
                            // Regola standard TRVBIG: La fee è manuale, ricalcoliamo la percentuale al volo
                            vfee_perc = totale_servizio > 0 ? (vfee * 100) / totale_servizio : 0;
                        } else {
                            // Fallback (se per qualche motivo non trova la classe, usa la tua vecchia logica)
                            if (vfee_perc > 0) {
                                vfee = totale_servizio * (vfee_perc / 100);
                            }
                        }

                        // Aggiorniamo i valori nella riga arrotondati a due decimali
                        rowData.fee = parseFloat(vfee.toFixed(2));
                        rowData.fee_perc = parseFloat(vfee_perc.toFixed(2));

                        console.log('Fee ricalcolata:', rowData.fee, '| Perc:', rowData.fee_perc);

                        // --- PROSEGUIAMO CON I TUOI CALCOLI ORIGINALI ---

                        // Imponibile + fee
                        const imponibile = (totale * 100) / (100 + aliquota);
                        rowData.imponibile = imponibile + rowData.fee;
                        console.log('Imponibile calcolato:', imponibile);

                        // IVA (su imponibile - fee)
                        const iva = ((rowData.imponibile * aliquota) / 100) - rowData.fee;
                        rowData.iva = iva;
                        console.log('IVA calcolata:', iva);

                        // Totale generale
                        rowData.totalegenerale = rowData.imponibile + iva + tax;
                        console.log('Totale generale calcolato:', rowData.totalegenerale);

                        // Totale fattura
                        rowData.totfattura = rowData.imponibile + tax;
                        console.log('Totale fattura calcolato:', rowData.totfattura);

                        // ✅ A questo punto puoi aggiornare la tabella o riga se serve
                        // es: table.row(index).data(rowData).draw(false);
                    },
                    error: function(xhr, status, error) {
                        console.error("Errore AJAX nel calcolo fee: ", error);
                    }
                }); // Fine chiamata AJAX per la classe
            });
        }
        // Cache per evitare chiamate duplicate
        const aliquotaCache = {};

        function getAliquotaByCodiva(codiva, callback) {
            if (!codiva) {
                callback(22); // fallback se mancante
                return;
            }

            // Se già in cache
            if (aliquotaCache[codiva]) {
                callback(aliquotaCache[codiva]);
                return;
            }

            // Altrimenti AJAX
            $.ajax({
                url: 'index.php?r=xtravelhead/get-aliquota',
                method: 'GET',
                data: {
                    codiva: codiva
                },
                success: function(response) {
                    if (response.success) {
                        aliquotaCache[codiva] = parseFloat(response.aliquota);
                        callback(aliquotaCache[codiva]);
                    } else {
                        console.warn("Aliquota non trovata:", codiva);
                        callback(22); // fallback
                    }
                },
                error: function() {
                    console.error("Errore nel recupero dell'aliquota per", codiva);
                    callback(22); // fallback
                }
            });
        }













        // Seleziona/deseleziona tutte le righe
        $(document).on('click', '#select-all', function() {
            const checked = this.checked;
            $('.row-select').prop('checked', checked);
        });

        // Pulsante elimina selezionati
        $('#btnDeleteSelected').on('click', function() {
            let selectedIds = [];
            $('.row-select:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                alert('Seleziona almeno una riga da eliminare');
                return;
            }

            if (!confirm('Sei sicuro di voler eliminare le righe selezionate?')) {
                return;
            }

            $.ajax({
                url: 'index.php?r=xtravelrow/delete-multiple', // azione Yii2 che dovrai creare
                type: 'POST',
                data: {
                    ids: selectedIds
                },
                success: function(response) {
                    if (response.success) {
                        // Rimuovi le righe dalla tabella
                        $('.row-select:checked').each(function() {
                            table.row($(this).closest('tr')).remove().draw(false);
                        });
                        alert('Righe eliminate con successo');
                        console.log(response);
                    } else {
                        alert('Errore durante l\'eliminazione: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Errore AJAX: ' + error);
                }
            });
        });






        // --- LOGICA DI RICERCA SPECIFICA CONCATENATA ---

        // Ricerca per Ospite (Colonna 3)
        $('#cercaOspiteSpeciale').on('keyup', function() {
            table
                .column(3) // Indice della colonna 'guest'
                .search(this.value)
                .draw();
        });

        // Ricerca per Struttura (Colonna 7)
        $('#cercaStrutturaSpeciale').on('keyup', function() {
            table
                .column(8) // Indice della colonna 'struttura'
                .search(this.value)
                .draw();
        });


    });
</script>


<script>
    $(document).ready(function() {

        // 1. CREIAMO UNA FUNZIONE CENTRALIZZATA PER BLOCCARE/SBLOCCARE
        function applicaRegoleBlocco(rigaElement, codiceArticolo) {
            if (!codiceArticolo) return;

            var table = $('#tappe-table').DataTable(); // Sostituisci con l'ID della tua tabella
            var $riga = $(rigaElement);

            // Cerchiamo le celle tramite il loro indice
            var colFeeIdx = table.column(':contains(fee)').index();
            var colFeePercIdx = table.column(':contains(fee_perc)').index();

            var $tdFeePerc = $riga.find('td').eq(colFeePercIdx);
            var $tdFee = $riga.find('td').eq(colFeeIdx);

            // Chiamata AJAX per conoscere la classe
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['xtravelrow/get-classe-articolo']) ?>',
                type: 'GET',
                data: {
                    cd_ar: codiceArticolo
                },
                success: function(response) {
                    if (response.success) {
                        var classeArticolo = response.classe;

                        // Di base sblocchiamo tutto
                        $tdFeePerc.addClass('editable');
                        $tdFee.addClass('editable');

                        // Applichiamo le restrizioni
                        if (classeArticolo === 'TRVACC') {
                            if (codiceArticolo === 'ACC_FEE_FUORIORA') {
                                $tdFeePerc.removeClass('editable');
                            } else {
                                $tdFee.removeClass('editable');
                            }
                        } else if (classeArticolo === 'TRVBIG') {
                            $tdFeePerc.removeClass('editable');
                        }
                    }
                }
            });
        }

        // 2. QUANDO L'UTENTE CAMBIA L'ARTICOLO NELLA TENDINA (Modifica in linea)
        $(document).on('change', 'select[name="cd_Ar"], .select2-hidden-accessible[name="cd_Ar"]', function() {
            var $select = $(this);
            var codiceArticolo = $select.val();
            var $riga = $select.closest('tr');

            applicaRegoleBlocco($riga, codiceArticolo);
        });

        // 3. QUANDO UNA NUOVA RIGA VIENE AGGIUNTA A DATATABLES O LA TABELLA VIENE DISEGNATA
        // DataTables mette a disposizione l'evento 'draw.dt' che scatta quando la tabella viene aggiornata
        $('#tappe-table').on('draw.dt', function() {
            var table = $('#tappe-table').DataTable();

            // Cicliamo tutte le righe visibili per applicare la regola
            table.rows({
                page: 'current'
            }).every(function(rowIdx, tableLoop, rowLoop) {
                var rowData = this.data();
                var rowNode = this.node(); // Otteniamo l'elemento HTML <tr>

                // rowData potrebbe essere un array o un oggetto, recuperiamo cd_Ar
                //var codiceArticolo = rowData.cd_Ar || (rowData[ /*indice colonna cd_Ar*/ ] || null);
                var codiceArticolo = (rowData && rowData.cd_Ar) ? rowData.cd_Ar : null;
                if (codiceArticolo) {
                    applicaRegoleBlocco(rowNode, codiceArticolo);
                }
            });
        });

    });
</script>