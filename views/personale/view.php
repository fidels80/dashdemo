<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\SubEntitaLookup;
use yii\bootstrap5\Modal;
use app\assets\DataTablesAsset;
$categorie = SubEntitaLookup::find()->where(['riferimento' => 'PERSONALE'])->orderBy('descrizione')->all();
$this->title = '';

// Assets DataTables (già presenti nel tuo codice)
$this->registerCssFile('https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css');
$this->registerCssFile('https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css');

$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

$this->registerCss("
    .section-title { border-left: 5px solid #007bff; padding-left: 15px; margin-bottom: 20px; margin-top: 30px; font-weight: bold; }
    .badge-lg { font-size: 1rem; padding: 8px 12px; }
    .info-table th { background-color: #f8f9fa; width: 15%; }
    .dataTables_filter { text-align: left !important; float: left !important; }
    .dt-buttons { float: right !important; margin-bottom: 10px; }
");
?>

<div class="personale-view card p-4 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Scheda Dipendente: <?= Html::encode($model->nome . ' ' . $model->cognome) ?></h1>
        <div>
       <?= Html::button('<i class="fa fa-upload"></i> Carica Documento', [
                'class' => 'btn btn-success btn-lg apri-upload-btn',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-caricamento-personale',
                'data-url' => Url::to(['upload-file', 'id' => $model->id]),
            ]); ?>       <?= Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-lg']) ?>
            <?= Html::a('Torna alla Lista', ['index'], ['class' => 'btn btn-secondary btn-lg']) ?>
        </div>
    </div>

<div class="row mb-4">
        <div class="col-md-12">
            <h3 class="section-title text-primary">Informazioni Generali e Contatti</h3>
            <div class="table-responsive">
                <table class="table table-bordered info-table" style="font-size: 1.1rem;">
                    <tr>
                        <th>Documento</th>
                        <td><?= Html::encode($model->codice_fiscale) ?></td>
                        <th>Stato</th>
                        <td>
                            <?= $model->stato_attivo
                                ? '<span class="badge bg-success badge-lg">ATTIVO</span>'
                                : '<span class="badge bg-danger badge-lg">INATTIVO</span>' ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= Html::mailto(Html::encode($model->email), $model->email) ?></td>
                        <th>Cellulare</th>
                        <td><?= Html::encode($model->cellulare) ?></td>
                    </tr>
                    <tr>
                        <th>Altro Telefono</th>
                        <td><?= Html::encode($model->telefono_secondario) ?></td>
                        <th>Tariffa Oraria</th>
                        <td><strong>€ <?= number_format($model->tariffa_oraria, 2, ',', '.') ?></strong></td>
                    </tr>
                    <tr>
                        <th>Ruolo / Reparto</th>
                        <td><?= Html::encode($model->ruolo) ?> / <?= Html::encode($model->reparto) ?></td>
                        <th>Mansione</th>
                        <td><?= Html::encode($model->mansione) ?></td>
                    </tr>
                    <tr>
                        <th>Indirizzo</th>
                        <td colspan="3">
                            <?= Html::encode($model->indirizzo) ?>,
                            <?= Html::encode($model->cap) ?>
                            <?= Html::encode($model->citta) ?>
                            (<?= Html::encode($model->provincia) ?>)
                        </td>
                    </tr>
                    <tr>
                        <th>Data Inserimento</th>
                        <td colspan="3"><?= Yii::$app->formatter->asDatetime($model->data_inserimento, 'php:d/m/Y H:i') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="section-title text-success">Registro Presenze</h3>
            <table id="table-presenze" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Ore</th>
                        <th>Costo (€)</th>
                        <th>Tipo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->presenzes as $presenza): ?>
                        <tr>
                            <td><?= Yii::$app->formatter->asDate($presenza->data_presenza, 'php:d/m/Y') ?></td>
                            <td><?= $presenza->ore_lavorate ?></td>
                            <td class="text-end"><strong><?= number_format($presenza->ore_lavorate * $model->tariffa_oraria, 2, ',', '.') ?></strong></td>
                            <td><?= Html::encode($presenza->tipo_assenza) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h3 class="section-title text-info">Planning Attività</h3>
            <table id="table-planning" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Descrizione</th>
                        <th>Mezzo</th>
                        <th>Stato</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->plannings as $attivita): ?>
                        <tr>
                            <td><?= Yii::$app->formatter->asDate($attivita->data_attivita, 'php:d/m/Y') ?></td>
                            <td><?= Html::encode($attivita->descrizione) ?></td>
                            <td><?= $attivita->veicolo ? Html::encode($attivita->veicolo->targa) : '-' ?></td>
                            <td>
                                <span class="badge bg-<?= $attivita->stato_completamento == 'Pianificato' ? 'info' : 'success' ?>">
                                    <?= Html::encode($attivita->stato_completamento) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="section-title text-warning">Documenti Dipendente (Contratti, Documenti Identità, Corsi Sicurezza)</h3>
           <?php /* <table id="table-files" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nome File</th>
                        <th style="width: 50px;">Estensione</th>
                        <th>Nota</th>
                        <th style="width: 150px;">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->files as $file): ?>
                        <tr>
                            <td><?= Html::encode($file->nomefile) ?></td>
                            <td class="text-center"><span class="badge bg-secondary"><?= strtoupper($file->estensione) ?></span></td>
                            <td><?= Html::encode($file->nota) ?></td>
                            <td class="text-center">
                                <a href="<?= Url::to(['/allfiles/download', 'id' => $file->id]) ?>" class="btn btn-xs btn-info">
                                    <i class="fa fa-download"></i>
                                </a>
                                <?= Html::a('<i class="fa fa-trash"></i>', ['/allfiles/delete', 'id' => $file->id], [
                                    'class' => 'btn btn-xs btn-danger',
                                    'data-method' => 'post',
                                    'data-confirm' => 'Eliminare il documento?'
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            */ 
            ?> 
        </div>
    </div>

   


<?php
$js = <<<JS
$(document).ready(function() {
    var dtLanguage = {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json'
    };

    var dtButtons = [
        { extend: 'copy', className: 'btn btn-sm btn-secondary', text: 'Copia' },
        { extend: 'excel', className: 'btn btn-sm btn-success', text: 'Excel' },
        { extend: 'print', className: 'btn btn-sm btn-primary', text: 'Stampa' }
    ];

    $('#table-presenze').DataTable({
        pageLength: 5,
        dom: '<"row"<"col-md-6"f><"col-md-6"B>>rtip',
        language: dtLanguage,
        buttons: dtButtons
    });

    $('#table-planning').DataTable({
        pageLength: 5,
        dom: '<"row"<"col-md-6"f><"col-md-6"B>>rtip',
        language: dtLanguage,
        buttons: dtButtons
    });

    // Inizializzazione nuova tabella file
    $('#table-files').DataTable({
        language: dtLanguage,
        pageLength: 5,
        dom: 'frtip'
    });
});
JS;
$this->registerJs($js);
?>

<?php
Modal::begin([
    'id' => 'modal-caricamento-personale',
    'title' => '<h4 class="text-primary m-0">Nuovo Caricamento Documento Personale</h4>',
    'size' => 'modal-lg',
    'options' => ['tabindex' => false],
]);
echo '<div id="area-form-upload-personale" class="text-center p-5">
        <div class="spinner-border text-primary"></div>
        <p class="mt-2">Inizializzazione modulo...</p>
      </div>';
Modal::end();
?>

<?php
$js = <<<JS
$(document).ready(function() {
    // 1. Gestione Modale AJAX
    $(document).on('show.bs.modal', '#modal-caricamento-personale', function (event) {
        var pulsante = $(event.relatedTarget); 
        var urlAzione = pulsante.data('url');
        $('#area-form-upload-personale').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div><p>Caricamento...</p></div>');

        $.ajax({
            url: urlAzione,
            type: 'GET',
            success: function(risposta) {
                $('#area-form-upload-personale').html(risposta);
            },
            error: function() {
                $('#area-form-upload-personale').html('<div class="alert alert-danger">Impossibile caricare il modulo.</div>');
            }
        });
    });

    // 2. Inizializzazione DataTables Documenti
    $('.table-documenti-auto').each(function() {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                dom: '<"row"<"col-sm-12 col-md-6"f><"col-sm-12 col-md-6"l>>t<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json' },
                pageLength: 5,
                responsive: true,
                autoWidth: false
            });
        }
    });

    // Correzione larghezza Tab
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
    // 2. Inizializzazione DataTables Documenti
    $('.table-documenti-auto').each(function() {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                dom: '<"row"<"col-sm-12 col-md-6"f><"col-sm-12 col-md-6"l>>t<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json' },
                pageLength: 5,
                responsive: true,
                autoWidth: false
            });
        }
    });

    // Correzione larghezza Tab
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
});
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>

<div class="row mb-4">
    <ul class="nav nav-tabs" id="docTabs" role="tablist">
            <?php foreach ($categorie as $index => $cat): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $index === 0 ? 'active' : '' ?>" 
                            id="tab-<?= $cat->codice ?>" data-bs-toggle="tab" 
                            data-bs-target="#content-<?= $cat->codice ?>" 
                            type="button" role="tab"><?= Html::encode($cat->descrizione) ?></button>
                </li>
            <?php endforeach; ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-generico" data-bs-toggle="tab" 
                        data-bs-target="#content-generico" 
                        type="button" role="tab">Senza Categoria / Altro</button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 p-3 bg-white shadow-sm" id="docTabsContent">
            <?php foreach ($categorie as $index => $cat): ?>
                <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" 
                     id="content-<?= $cat->codice ?>" role="tabpanel">
                    <?= $this->render('_tab_documenti_table', ['files' => $model->files, 'sub_entita' => $cat->codice]) ?>
                </div>
            <?php endforeach; ?>

            <div class="tab-pane fade" id="content-generico" role="tabpanel">
                <?= $this->render('_tab_documenti_table', ['files' => $model->files, 'sub_entita' => null]) ?>
            </div>
        </div>
    </div>
</div>

 <div class="text-end">
        <?= Html::a('<i class="fa fa-trash"></i> Elimina questa scheda personale', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-outline-danger',
            'data' => [
                'confirm' => 'Sei sicuro di voler eliminare definitivamente questo dipendente?',
                'method' => 'post',
            ],
        ]) ?>
    </div>




    </div>