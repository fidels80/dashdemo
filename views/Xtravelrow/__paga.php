<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;
// Assicurati di avere kartik-v/yii2-widget-select2
/* @var $this yii\web\View */
/* @var $model app\models\Xtravelrow */
/* @var $storicoModelli app\models\Xtravelrow[] */

$maxResiduo = $model->prezzo;
?>
<?php
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
    /* Impedisce che le date e i prezzi vadano a capo, mantenendo la tabella ordinata */
    .nowrap {
        white-space: nowrap;
    }

    .table-paga th {
        background-color: #f8f9fa;
    }
</style>
<style>
    .nowrap {
        white-space: nowrap;
    }

    .table-paga th {
        background-color: #f8f9fa;
    }

    /* Evidenzia i campi obbligatori */
    .required-label:after {
        content: " *";
        color: red;
    }

    #tabella-servizi tfoot td {
        border-top: 2px solid #dee2e6;
        padding: 8px;
    }

    /* Forza l'allineamento delle colonne DataTables */
    .dataTables_scrollFootInner,
    .dataTables_scrollFootInner table {
        width: 100% !important;
    }

    .content {
        width: 95%;
    }
</style>
<br>
 
<div class="xtravelrow-paga">

    <?php if (!empty($storicoModelli)): ?>
       
        <div style="margin-left: 0px;">
            <?php
            $url = Url::to([
                'xtravelhead/tool',
                'id' => $xthid
            ]);

            echo Html::button('<i class="fa fa-arrow-left"></i> Torna indietro', [
                'class' => 'button-base-support button-lift',
                'onclick' => 'window.location.href = "' . $url . '"'
            ]);

            ?>

        </div>
        <?php
        // Supponiamo che questi siano i codici articolo che identificano i pagamenti
        // In un caso reale, potresti volerli passare dal controller
        $codiciPagamento = ['ACCONTO HTL', 'SALDO HTL', 'PAG']; // Sostituisci con i tuoi codici reali

        $servizi = [];
        $pagamenti = [];

        foreach ($storicoModelli as $riga) {
            if (in_array($riga->cd_Ar, $codiciPagamento)) {
                $pagamenti[] = $riga;
            } else {
                $servizi[] = $riga;
            }
        }
        ?>
        <?php
        // Inizializziamo le variabili per i totali
        $totQta = 0;
        $sommaPrezziMedia = 0;
        $totTotale = 0;
        $totTasse = 0;
        $countRighe = count($storicoModelli);

        // Calcolo media prezzo
        ?>

        <h5>Dettagli Servizi</h5>
        <table id="tabella-servizi" class="table table-sm table-bordered table-hover display">
            <thead class="thead-light">
                <tr>
                    <th>Descrizione</th>
                    <th>Ospite</th>
                    <th class="text-center">Q.tà</th>
                    <th class="text-right">Prezzo</th>
                    <th class="text-right">Totale</th>
                    <th class="text-right">Tasse</th>
                    <th>Stato</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($servizi as $riga): ?>
                    <tr>
                        <td><small><?= Html::encode($riga->descrizione) ?></small></td>
                        <td><small><?= Html::encode($riga->guest) ?></small></td>
                        <td class="text-center"><?= $riga->qta ?></td>
                        <td class="text-right"><?= number_format($riga->prezzo, 2, ',', '.') ?> €</td>
                        <td class="text-right"><?= number_format($riga->totale, 2, ',', '.') ?> €</td>
                        <td class="text-right"><?= number_format($riga->tax_unit * $riga->qta, 2, ',', '.') ?> €</td>
                        <td><span class="badge badge-secondary"><?= $riga->stato ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-light font-weight-bold">
                <tr>
                    <td class="text-right">TOTALI:</td>
                    <td></td>
                    <td class="text-center" id="servizi-qta"></td>
                    <td></td>
                    <td class="text-right nowrap" id="servizi-totale"></td>
                    <td class="text-right nowrap" id="servizi-tasse"></td>
                    <td id="servizi-generale" class="bg-success text-white text-center"></td>
                </tr>
            </tfoot>
        </table>

        <hr>

        <h5>Dettagli Pagamenti Realizzati</h5>
        <table id="tabella-pagamenti" class="table table-sm table-bordered table-hover display">
            <thead class="thead-dark">
                <tr>
                    <th>Descrizione</th>
                    <th class="text-right">Importo Pagato</th>
                    <th>Data</th>
                    <th>Metodo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pagamenti as $riga): ?>
                    <tr>
                        <td><?= Html::encode($riga->descrizione) ?></td>
                        <td class="text-right"><?= number_format($riga->totale, 2, ',', '.') ?> €</td>
                        <td><?= Yii::$app->formatter->asDate($riga->data_pg, 'dd/MM/yyyy') ?></td>
                        <td>
                            <?php
                            echo isset($creditData[$riga->cd_pg])
                                ? Html::encode($creditData[$riga->cd_pg])
                                : Html::encode($riga->cd_pg);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-info text-white font-weight-bold">
                <tr>
                    <td class="text-right">TOTALE PAGATO:</td>
                    <td class="text-right" id="pagamenti-somma">0,00 €</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <hr>
    <?php endif; ?>

    <?php if ($maxResiduo <> 0): ?>
        <h3>Nuovo Inserimento / Pagamento</h3>
        <?php $form = ActiveForm::begin(['id' => 'paga-form', 'enableClientValidation' => true,]); ?>
        <?php echo $model->id_tappa; ?>
        <?php echo $model->struttura; ?>


        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'cd_Ar')->widget(Select2::classname(), [
                    'options' => [
                        'placeholder' => 'Seleziona Acconto o Saldo...',
                        'id' => 'articolo-select2',
                        // RIMOSSO required qui
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'minimumInputLength' => 0,
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['xtravelhead/getartpg']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                            'processResults' => new JsExpression('function(data) { return data; }'),
                        ],
                    ],
                ]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'cd_pg')->widget(Select2::classname(), [
                    'data' => $creditData,
                    'options' => [
                        'placeholder' => 'Metodo di pagamento...',
                        'id' => 'pagamento-select2',
                        // RIMOSSO required qui
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'prezzo')->textInput([
                    'type' => 'number',
                    'step' => '0.01',
                    'id' => 'input-prezzo',
                    'max' => $maxResiduo, // Blocca le freccette del browser
                    'required' => true
                ])->label('Importo (Max: ' . number_format($maxResiduo, 2, ',', '.') . ' €)') ?> </div>
            <div class="col-md-2">
                <?= $form->field($model, 'data_pg')->input('date') ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'check_in')->textInput([

                    'readonly' => true
                ]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'check_out')->textInput([

                    'readonly' => true
                ]) ?>
            </div>
        </div>

        <div class="form-group text-right">
            <?= Html::submitButton('Registra Pagamento', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php endif; ?>
</div>
<script>
    // Se non usi il widget Kartik ma vuoi inizializzarlo manualmente via JS:
    $(document).ready(function() {
        $('#select-articolo-paga').select2({
            dropdownParent: $('#modal-xtravel'),
            width: '100%',
            minimumInputLength: 2,
            ajax: {
                url: 'index.php?r=xtravelhead/getartpg',
                dataType: 'json',
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });
    });
</script>

<?php
$maxResiduo = $model->prezzo;
$this->registerJs("
$('#paga-form').on('beforeSubmit', function (e) {
    var prezzoInserito = parseFloat($('#input-prezzo').val());
    var maxConsentito = parseFloat('$maxResiduo');
    var articolo = $('#articolo-select2').val();
    var pagamento = $('#pagamento-select2').val();

    // 1. Controllo Obbligatorietà Articolo
    if (!articolo) {
        alert('L\'articolo è obbligatorio.');
        return false;
    }

    // 2. Controllo Obbligatorietà Pagamento
    if (!pagamento) {
        alert('Il metodo di pagamento è obbligatorio.');
        return false;
    }

    // 3. Controllo Prezzo Massimo
    if (prezzoInserito > maxConsentito) {
        alert('Errore: L\'importo non può superare ' + maxConsentito.toFixed(2) + ' €');
        return false;
    }

    return true; // Tutto ok, procedi al salvataggio nel controller
});

// Autocorrezione prezzo mentre scrive
$('#input-prezzo').on('input', function() {
    var val = parseFloat($(this).val());
    var max = parseFloat('$maxResiduo');
    if (val > max) {
        $(this).val(max);
    }
});
");
?>










<?php
$this->registerJs("
    var intVal = function (i) {
        if (typeof i === 'number') return i;
        if (typeof i !== 'string') return 0;
        var clean = i.replace(/[€\s]/g, '').replace(/\./g, '').replace(',', '.');
        return parseFloat(clean) || 0;
    };

    var euroFormatter = new Intl.NumberFormat('it-IT', { style: 'currency', currency: 'EUR' });

    // Tabella Servizi (7 colonne: 0-6)
    var tableServizi = $('#tabella-servizi').DataTable({
        'language': { 'url': '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json' },
        'paging': false,
        'searching': true,
        'info': true,
        'dom': 'Bfrti',
 'buttons': [
    { 
        extend: 'copy', 
        className: 'btn btn-secondary btn-sm', 
        text: '<i class=\"fas fa-copy\"></i> Copia',
        exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 7] // Escludiamo la colonna 6 (Check-in/out con formattazione complessa) se preferisci
        }
    },
    { extend: 'excel', className: 'btn btn-success btn-sm', text: '<i class=\"fas fa-file-excel\"></i> Excel' },
    { extend: 'pdf', className: 'btn btn-danger btn-sm', text: '<i class=\"fas fa-file-pdf\"></i> PDF' },
    { extend: 'print', className: 'btn btn-info btn-sm', text: '<i class=\"fas fa-print\"></i> Stampa' }
],
        'footerCallback': function (row, data, start, end, display) {
            var api = this.api();

            // Qta (Colonna 2)
            var totQta = api.column(2, {filter: 'applied'}).data().reduce((a, b) => intVal(a) + intVal(b), 0);
            // Prezzo (Colonna 3) per media
            var dataPrezzi = api.column(3, {filter: 'applied'}).data();
            var mediaPrezzo = dataPrezzi.length > 0 ? (dataPrezzi.reduce((a, b) => intVal(a) + intVal(b), 0) / dataPrezzi.length) : 0;
            // Totale (Colonna 4)
            var sommaTotale = api.column(4, {filter: 'applied'}).data().reduce((a, b) => intVal(a) + intVal(b), 0);
            // Tasse (Colonna 5)
            var sommaTasse = api.column(5, {filter: 'applied'}).data().reduce((a, b) => intVal(a) + intVal(b), 0);

            $('#servizi-qta').html(totQta);
            $('#servizi-media').html(euroFormatter.format(mediaPrezzo) + ' (media)');
            $('#servizi-totale').html(euroFormatter.format(sommaTotale));
            $('#servizi-tasse').html(euroFormatter.format(sommaTasse));
            $('#servizi-generale').html('TOT. GEN: ' + euroFormatter.format(sommaTotale + sommaTasse));
        }
    });

// Inizializzazione Tabella PAGAMENTI
$('#tabella-pagamenti').DataTable({
    'language': { 'url': '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json' },
    'paging': false,
    'searching': true,
    'info': true,
    'dom': 'Bfrti',
    'buttons': [
    { 
        extend: 'copy', 
        className: 'btn btn-secondary btn-sm', 
        text: '<i class=\"fas fa-copy\"></i> Copia',
        exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 7] // Escludiamo la colonna 6 (Check-in/out con formattazione complessa) se preferisci
        }
    },
    { extend: 'excel', className: 'btn btn-success btn-sm', text: '<i class=\"fas fa-file-excel\"></i> Excel' },
    { extend: 'pdf', className: 'btn btn-danger btn-sm', text: '<i class=\"fas fa-file-pdf\"></i> PDF' },
    { extend: 'print', className: 'btn btn-info btn-sm', text: '<i class=\"fas fa-print\"></i> Stampa' }
],
    'footerCallback': function (row, data, start, end, display) {
        var api = this.api();

        // Calcoliamo la somma della colonna indice 1 (Importo Pagato)
        var totalePagato = api.column(1, {filter: 'applied'}).data().reduce(function (a, b) {
            return intVal(a) + intVal(b);
        }, 0);

        // Scriviamo il risultato nel footer
        $('#pagamenti-somma').html(euroFormatter.format(totalePagato));
    }
});

    // Forza allineamento colonne dopo il rendering
    setTimeout(function() {
        tableServizi.columns.adjust().draw();
    }, 500);
");
?>