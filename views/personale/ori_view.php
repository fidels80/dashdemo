<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->nome . ' ' . $model->cognome;

// Assets DataTables
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
        <h1>Scheda Dipendente: <?= Html::encode($this->title) ?></h1>
        <div>
            <?= Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-lg']) ?>
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

    <div class="row">
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
    <div class="text-end">
        <?= Html::a('<i class="fa fa-trash"></i> Elimina questa registrazione', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-outline-danger',
            'data' => [
                'confirm' => 'Sei sicuro di voler eliminare definitivamente questa registrazione?',
                'method' => 'post',
            ],
        ]) ?>
    </div>
</div>

<?php
$js = <<<JS
$(document).ready(function() {
    var dtLanguage = {
        search: 'Cerca:',
        lengthMenu: 'Mostra _MENU_ record per pagina',
        info: 'Visualizzati da _START_ a _END_ di _TOTAL_ record',
        infoEmpty: 'Nessun record disponibile',
        infoFiltered: '(filtrati da _MAX_ record totali)',
        zeroRecords: 'Nessun risultato trovato',
        paginate: { first: 'Inizio', last: 'Fine', next: 'Successivo', previous: 'Precedente' },
        buttons: { copyTitle: 'Copiato', copySuccess: { _: '%d righe copiate', 1: '1 riga copiata' } }
    };

    var dtButtons = [
        { extend: 'copy', className: 'btn btn-sm btn-secondary', text: 'Copia' },
        { extend: 'excel', className: 'btn btn-sm btn-success', text: 'Excel' },
        { extend: 'print', className: 'btn btn-sm btn-primary', text: 'Stampa' }
    ];

    $('#table-presenze').DataTable({
        pageLength: 5,
        lengthMenu: [5, 10, 25],
        dom: '<"row"<"col-md-6"f><"col-md-6"B>>rtip',
        language: dtLanguage,
        buttons: dtButtons
    });

    $('#table-planning').DataTable({
        pageLength: 5,
        lengthMenu: [5, 10, 25],
        dom: '<"row"<"col-md-6"f><"col-md-6"B>>rtip',
        language: dtLanguage,
        buttons: dtButtons
    });
});
JS;
$this->registerJs($js);
?>