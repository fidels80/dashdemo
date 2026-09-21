<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Xvenue;

$this->title = 'Conferma Generazione Tour';

// Carichiamo DataTables
$this->registerCssFile("https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css");
$this->registerJsFile("https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js", ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile("https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js", ['depends' => [\yii\web\JqueryAsset::class]]);

?>

<style>
    .content {
        width: 95%;
    }
</style>

<div class="preview-wizard container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white">
            <h3 class="mb-0">Anteprima Configurazione Tour</h3>
        </div>
        <div class="card-body">
            <div class="alert alert-custom bg-light border-info">
                <i class="fa fa-info-circle text-info"></i>
                Verranno creati <strong><?= Html::encode($anteprima['guest_totali_nuovi']) ?></strong> nominativi unici e
                generate <strong><?= count($anteprima['righe_da_creare']) ?></strong> righe di viaggio.
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <h5 class="text-primary"><i class="fa fa-map-marker-alt"></i> Riepilogo Tappe</h5>
                    <table class="table table-bordered table-sm mt-3">
                        <thead class="thead-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Città/ID</th>
                                <th class="text-center">Ospiti</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($anteprima['tappe'] as $i => $tappa): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td class="small text-truncate" style="max-width: 150px;">
                                        <?= Html::encode($tappa['citta']) ?>
                                    </td>
                                    <td class="text-center font-weight-bold"><?= $tappa['ospiti'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="col-lg-8">
                    <h5 class="text-success"><i class="fa fa-list"></i> Dettaglio Righe (XTravelrow)</h5>
                    <div class="table-responsive">
                        <table id="table-anteprima-righe" class="table table-striped table-bordered table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>Guest</th>
                                    <th>Città (ID)</th>
                                    <th>Data Check-in</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($anteprima['righe_da_creare'] as $riga): ?>
                                    <tr>
                                        <td style="<?= $riga['is_new'] ? 'color: green; font-weight: bold;' : 'color: #666;' ?>">
                                            <?= Html::encode($riga['guest']) ?>
                                            <?= $riga['is_new'] ? '(Nuovo)' : '(Esistente)' ?>
                                        </td>
                                        <td><?= Html::encode($riga['citta_completa']) ?></td>
                                        <td><span class="badge badge-secondary"><?= Html::encode($riga['data']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <hr>

            <div class="d-flex justify-content-between mt-4">
                <?= Html::a('<i class="fa fa-chevron-left"></i> Torna Indietro', ['wizard-tour', 'th_id' => $th_id], ['class' => 'btn btn-outline-secondary btn-lg']) ?>

                <?php $form = ActiveForm::begin(['action' => ['confirm-save', 'th_id' => $th_id]]); ?>
                <?= Html::hiddenInput('data_to_save', json_encode($originalData)) ?>
                <?= Html::submitButton('Conferma e Salva nel Database <i class="fa fa-database"></i>', ['class' => 'btn btn-success btn-lg shadow-sm']) ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
// Definiamo il JS e lo registriamo subito
$jsCode = <<<JS
$('#table-anteprima-righe').DataTable({
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json"
    },
    "pageLength": 10,
    "order": [[ 2, "asc" ]],
    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
});

$('form').on('beforeSubmit', function() {
    var btn = $(this).find('button[type="submit"]');
    btn.html('<i class="fa fa-spinner fa-spin"></i> Salvataggio in corso...');
    btn.prop('disabled', true);
});
JS;

$this->registerJs($jsCode);
?>