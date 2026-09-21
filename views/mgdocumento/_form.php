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
            <div>
                <button type="button" class="btn btn-sm btn-outline-success" id="btn-nuovo-articolo">
                    <i class="fas fa-box"></i> Nuovo articolo
                </button>
                <button type="button" class="btn btn-sm btn-primary" id="btn-add-riga">
                    <i class="fas fa-plus"></i> Aggiungi riga
                </button>
            </div>
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

    <!-- Modale nuovo articolo -->
    <div class="modal fade" id="modal-nuovo-articolo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white"><i class="fas fa-box"></i> Nuovo articolo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Codice *</label>
                        <input type="text" id="na-codice" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Descrizione *</label>
                        <input type="text" id="na-descrizione" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-4"><div class="form-group"><label>U.M.</label><input type="text" id="na-um" class="form-control"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>Prezzo</label><input type="number" step="0.0001" id="na-prezzo" class="form-control" value="0"></div></div>
                        <div class="col-md-4"><div class="form-group"><label>IVA %</label><input type="number" step="0.01" id="na-iva" class="form-control" value="0"></div></div>
                    </div>
                    <div id="na-error" class="text-danger small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-success" id="btn-salva-articolo"><i class="fas fa-save"></i> Crea articolo</button>
                </div>
            </div>
        </div>
    </div>
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

    // --- NUOVO ARTICOLO IN LINEA ---
    var lastRiga = null;
    $(document).on('focus click', '.riga-articolo', function () {
        lastRiga = $(this).closest('.riga-row');
    });

    function aggiungiRiga() {
        var tpl = $('#righe-table').closest('.mgdocumento-form').find('table[style="display:none;"] tbody').html();
        var idx = $('#righe-body .riga-row').length;
        $('#righe-body').append(tpl.replace(/__INDEX__/g, idx));
        ricalcolaTotale();
        return $('#righe-body .riga-row').last();
    }

    $('#btn-nuovo-articolo').on('click', function () {
        $('#na-error').text('');
        $('#na-codice, #na-descrizione, #na-um').val('');
        $('#na-prezzo, #na-iva').val(0);
        $('#modal-nuovo-articolo').modal('show');
    });

    $('#btn-salva-articolo').on('click', function () {
        var codice = $('#na-codice').val().trim();
        var descrizione = $('#na-descrizione').val().trim();
        if (!codice || !descrizione) {
            $('#na-error').text('Codice e Descrizione sono obbligatori.');
            return;
        }
        var data = {
            codice: codice,
            descrizione: descrizione,
            um: $('#na-um').val(),
            prezzo: $('#na-prezzo').val(),
            iva: $('#na-iva').val(),
            _csrf: (typeof yii !== 'undefined' ? yii.getCsrfToken() : '')
        };
        var btn = $(this).prop('disabled', true);
        $.post('index.php?r=mgarticolo/create-ajax', data, function (res) {
            btn.prop('disabled', false);
            if (res && res.success) {
                var a = res.articolo;
                var opt = $('<option>')
                    .attr('value', a.id)
                    .attr('data-codice', a.codice)
                    .attr('data-prezzo', a.prezzo)
                    .attr('data-iva', a.iva)
                    .attr('data-descrizione', a.descrizione)
                    .text(a.codice + ' - ' + a.descrizione);
                $('.riga-articolo').append(opt);

                var $row = (lastRiga && $.contains(document, lastRiga[0])) ? lastRiga : aggiungiRiga();
                $row.find('.riga-articolo').val(String(a.id)).trigger('change');
                $('#modal-nuovo-articolo').modal('hide');
            } else {
                $('#na-error').text(res && res.errors ? JSON.stringify(res.errors) : (res.error || 'Errore di creazione.'));
            }
        }, 'json').fail(function () {
            btn.prop('disabled', false);
            $('#na-error').text('Errore di rete.');
        });
    });

    ricalcolaTotale();
})();
</script>
