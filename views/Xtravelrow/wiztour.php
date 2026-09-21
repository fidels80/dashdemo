<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $mth_id int */

$this->title = 'Configurazione Nuovo Tour e Ospiti';

// Registriamo i file necessari per Select2 se non lo hai fatto negli Assets
$this->registerCssFile("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css");
$this->registerCssFile("https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css");
$this->registerJsFile("https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js", ['depends' => [\yii\web\JqueryAsset::class]]);

?>
<style>
    .content {
        width: 95%;
    }
</style>
<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h3 class="mb-0"><?= Html::encode($this->title) ?></h3>
    <?= Html::a('<i class="fa fa-arrow-left"></i> Annulla', ['index'], ['class' => 'btn btn-light btn-sm']) ?>
</div>

<div class="x-tour-create">
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $messages): ?>
        <?php foreach ((array) $messages as $message): ?>
            <div class="alert alert-<?= $type ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= $message ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <div class="card shadow">
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'id' => 'wizard-tappe-form',
                'action' => ['xtravelrow/process-tour', 'th_id' => $mth_id],
                'method' => 'post',
            ]); ?>

            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="font-weight-bold">Quante tappe prevede il tour?</label>
                    <?= Html::input('number', 'num_tappe', null, [
                        'class' => 'form-control form-control-lg',
                        'id' => 'input-generatore-tappe',
                        'min' => 1,
                        'placeholder' => 'Es. 3'
                    ]) ?>
                </div>
            </div>

            <hr>

            <div id="container-tappe" class="row">
            </div>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fa fa-check"></i> Genera Tour e Nominativi', [
                    'class' => 'btn btn-success btn-lg btn-block',
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$cittaUrl = Url::to(['xtravelrow/xcaricatappa']);

// IMPORTANTE: Nota l'uso di \\$ per evitare che PHP interpreti le variabili JS
$js = <<<JS
$(document).on('input change', '#input-generatore-tappe', function() {
    var n = parseInt($(this).val());
    var container = $('#container-tappe');
    container.empty();

    if (isNaN(n) || n < 1) return;

    for (var i = 1; i <= n; i++) {
        var block = `
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card border-primary h-100">
                    <div class="card-header bg-light text-primary font-weight-bold">
                        TAPPA #\${i}
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Data</label>
                            <input type="date" name="Tappe[\${i}][data]" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Città / Venue</label>
                            <select name="Tappe[\${i}][citta]" class="form-control select2-wizard" id="select-citta-\${i}" required></select>
                        </div>
                        <div class="form-group">
                            <label>Numero Ospiti</label>
                            <input type="number" name="Tappe[\${i}][ospiti]" class="form-control" min="1" required>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.append(block);

        // Inizializzazione Select2 con un piccolo delay per assicurarsi che l'elemento sia nel DOM
        (function(index) {
            $('#select-citta-' + index).select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Cerca città...',
                ajax: {
                    url: '{$cittaUrl}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) { return { q: params.term }; },
                    processResults: function(data) { return { results: data.items }; }
                }
            });
        })(i);
    }
});
JS;
$this->registerJs($js);
?>