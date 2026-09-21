<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\models\MgArticolo;

/* @var $this yii\web\View */
/* @var $model app\models\MgDocumento */
/* @var $tipi array */
/* @var $anagrafiche array */
/* @var $righe app\models\MgDocumentoRiga[] */

$articoliModels = MgArticolo::find()->orderBy(['descrizione' => SORT_ASC])->all();
$stati = ['bozza' => 'Bozza', 'confermato' => 'Confermato', 'chiuso' => 'Chiuso', 'annullato' => 'Annullato'];
$isNew = $model->isNewRecord;
?>
<div class="mgdocumento-form">

    <?php $form = ActiveForm::begin(['id' => 'mgdocumento-form']); ?>

    <div class="card mb-3">
        <div class="card-header">Dati documento</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'id_tipo')->dropDownList($tipi, ['prompt' => 'Seleziona tipo...']) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'data')->textInput(['type' => 'date']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'anno')->textInput(['type' => 'number']) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'numero')->textInput(['type' => 'number']) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'suffisso')->textInput(['placeholder' => 'es. bis']) ?>
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="btn-proponi"
                            title="Proponi il primo numero libero">
                        <i class="fas fa-magic"></i>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'id_anagrafica')->dropDownList($anagrafiche, ['prompt' => 'Seleziona cliente/fornitore...']) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'stato')->dropDownList($stati) ?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'note')->textarea(['rows' => 2]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Righe documento</span>
            <button type="button" class="btn btn-sm btn-primary" id="btn-add-riga">
                <i class="fas fa-plus"></i> Aggiungi riga
            </button>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0" id="righe-table">
                <thead>
                <tr>
                    <th style="width:18%">Articolo</th>
                    <th style="width:10%">Codice</th>
                    <th>Descrizione</th>
                    <th style="width:8%">Q.tà</th>
                    <th style="width:9%">Prezzo</th>
                    <th style="width:7%">Sc. %</th>
                    <th style="width:7%">IVA %</th>
                    <th style="width:9%">Totale</th>
                    <th style="width:4%"></th>
                </tr>
                </thead>
                <tbody id="righe-body">
                <?php foreach ($righe as $i => $r): ?>
                    <?= $this->render('_riga', ['index' => $i, 'model' => $r, 'articoliModels' => $articoliModels]) ?>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="7" class="text-right">Totale documento</th>
                    <th class="text-right" id="totale-documento">0,00</th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <!-- template riga nascosta -->
    <table style="display:none;">
        <tbody>
        <?= $this->render('_riga', ['index' => '__INDEX__', 'model' => null, 'articoliModels' => $articoliModels]) ?>
        </tbody>
    </table>
</div>

<script>
(function () {
    var isNew = <?= $isNew ? 'true' : 'false' ?>;
    var proponiUrl = '<?= \yii\helpers\Url::to(['mgdocumento/proponi-numero']) ?>';

    function formatTotale(v) {
        return v.toFixed(2).replace('.', ',');
    }

    function ricalcolaRiga($row) {
        var qta = parseFloat($row.find('.riga-qta').val()) || 0;
        var prezzo = parseFloat($row.find('.riga-prezzo').val()) || 0;
        var sconto = parseFloat($row.find('.riga-sconto').val()) || 0;
        var tot = qta * prezzo * (1 - sconto / 100);
        $row.find('.riga-totale').val(formatTotale(tot));
        ricalcolaTotale();
    }

    function ricalcolaTotale() {
        var tot = 0;
        $('#righe-body .riga-row').each(function () {
            var v = parseFloat($(this).find('.riga-totale').val().replace('.', '').replace(',', '.')) || 0;
            tot += v;
        });
        $('#totale-documento').text(formatTotale(tot));
    }

    function aggiornaIndici() {
        $('#righe-body .riga-row').each(function (i) {
            $(this).find('select, input').each(function () {
                var name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/righe\[[^\]]*\]/, 'righe[' + i + ']'));
                }
            });
        });
    }

    $(document).on('input change', '.riga-qta, .riga-prezzo, .riga-sconto', function () {
        ricalcolaRiga($(this).closest('.riga-row'));
    });

    $(document).on('change', '.riga-articolo', function () {
        var opt = $(this).find('option:selected');
        var $row = $(this).closest('.riga-row');
        if (opt.val()) {
            $row.find('.riga-codice').val(opt.data('codice') || '');
            $row.find('.riga-desc').val(opt.data('descrizione') || '');
            $row.find('.riga-prezzo').val(opt.data('prezzo') || 0);
            $row.find('.riga-iva').val(opt.data('iva') || 0);
            if (!$row.find('.riga-qta').val()) {
                $row.find('.riga-qta').val(1);
            }
        }
        ricalcolaRiga($row);
    });

    $('#btn-add-riga').on('click', function () {
        var tpl = $('#righe-table').closest('.mgdocumento-form').find('table[style="display:none;"] tbody').html();
        var idx = $('#righe-body .riga-row').length;
        var html = tpl.replace(/__INDEX__/g, idx);
        $('#righe-body').append(html);
        ricalcolaTotale();
    });

    $(document).on('click', '.riga-remove', function () {
        $(this).closest('.riga-row').remove();
        aggiornaIndici();
        ricalcolaTotale();
    });

    function proponiNumero() {
        var idTipo = $('#mgdocumento-id_tipo').val();
        var anno = $('#mgdocumento-anno').val();
        var data = $('#mgdocumento-data').val();
        if (!idTipo) { return; }
        $.getJSON(proponiUrl, { id_tipo: idTipo, anno: anno, data: data }, function (res) {
            if (res && res.success && res.numero) {
                $('#mgdocumento-numero').val(res.numero);
            }
        });
    }

    $('#btn-proponi').on('click', proponiNumero);
    $('#mgdocumento-id_tipo, #mgdocumento-data').on('change', function () {
        if (isNew) { proponiNumero(); }
    });

    ricalcolaTotale();
})();
</script>
