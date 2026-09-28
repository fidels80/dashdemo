<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgArticolo */
/* @var $aliquote array */
/* @var $unita array */
/* @var $marche array */
/* @var $modelli array */
/* @var $tessuti array */
/* @var $taglie array */
/* @var $colori array */
/* @var $unitaArticolo app\models\MgArticoloUm[] */
/* @var $form yii\bootstrap4\ActiveForm */

$renderUnitaRiga = function ($index, $umRiga) use ($unita) {
    $idUm = $umRiga ? (string) $umRiga->id_unita_misura : '';
    $fattore = $umRiga ? $umRiga->fattore : '1';
    $predef = $umRiga ? (bool) $umRiga->predefinita : false;
    ob_start(); ?>
    <tr class="um-row">
        <td>
            <select name="unita[<?= $index ?>][id_unita_misura]" class="form-control form-control-sm um-select">
                <option value="">--</option>
                <?php foreach ($unita as $uid => $ulabel): ?>
                    <option value="<?= $uid ?>" <?= ((string) $idUm === (string) $uid) ? 'selected' : '' ?>><?= Html::encode($ulabel) ?></option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" step="any" min="0" name="unita[<?= $index ?>][fattore]"
                   class="form-control form-control-sm um-fattore text-right" value="<?= Html::encode($fattore) ?>">
        </td>
        <td class="text-center">
            <input type="radio" name="um_predef_radio" value="1"
                   class="um-predefinita" <?= $predef ? 'checked' : '' ?>>
            <input type="hidden" name="unita[<?= $index ?>][predefinita]" class="um-predef-hidden" value="<?= $predef ? '1' : '0' ?>">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger um-remove"><i class="fas fa-times"></i></button>
        </td>
    </tr>
    <?php
    return ob_get_clean();
};
?>
<div class="mgarticolo-form">
    <?php $form = ActiveForm::begin(['id' => 'mgarticolo-form']); ?>

    <ul class="nav nav-tabs mb-3" id="articoloTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab-dati-link" data-toggle="tab" href="#tab-dati" role="tab">Dati articolo</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-um-link" data-toggle="tab" href="#tab-um" role="tab">Unità di misura</a>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-3 bg-white shadow-sm mb-3" id="articoloTabsContent">
        <div class="tab-pane fade show active" id="tab-dati" role="tabpanel">
            <div class="row">
                <div class="col-md-3"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-6"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
                <div class="col-md-3"><?= $form->field($model, 'um')->textInput(['maxlength' => true, 'placeholder' => 'U.M. predefinita'])->hint('Sincronizzata con l\'unità predefinita') ?></div>
            </div>
            <div class="row">
                <div class="col-md-3"><?= $form->field($model, 'prezzo')->textInput(['type' => 'number', 'step' => '0.0001']) ?></div>
                <div class="col-md-4"><?= $form->field($model, 'id_iva_vendita')->dropDownList($aliquote, ['prompt' => 'Seleziona aliquota...']) ?></div>
                <div class="col-md-4"><?= $form->field($model, 'id_iva_acquisto')->dropDownList($aliquote, ['prompt' => 'Seleziona aliquota...']) ?></div>
                <div class="col-md-1"><?= $form->field($model, 'attivo')->checkbox() ?></div>
            </div>

            <hr>
            <h6 class="text-muted mb-3">Varianti</h6>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'id_marca')->dropDownList($marche, ['prompt' => 'Nessuna...']) ?>
                    <button type="button" class="btn btn-sm btn-outline-success btn-attributo" data-tipo="marca" data-target="mgarticolo-id_marca"><i class="fas fa-plus"></i> Nuova marca</button>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'id_modello')->dropDownList($modelli, ['prompt' => 'Nessuno...']) ?>
                    <button type="button" class="btn btn-sm btn-outline-success btn-attributo" data-tipo="modello" data-target="mgarticolo-id_modello"><i class="fas fa-plus"></i> Nuovo modello</button>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'id_tessuto')->dropDownList($tessuti, ['prompt' => 'Nessuno...']) ?>
                    <button type="button" class="btn btn-sm btn-outline-success btn-attributo" data-tipo="tessuto" data-target="mgarticolo-id_tessuto"><i class="fas fa-plus"></i> Nuovo tessuto</button>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'id_taglia')->dropDownList($taglie, ['prompt' => 'Nessuna...']) ?>
                    <button type="button" class="btn btn-sm btn-outline-success btn-attributo" data-tipo="taglia" data-target="mgarticolo-id_taglia"><i class="fas fa-plus"></i> Nuova taglia</button>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'id_colore')->dropDownList($colori, ['prompt' => 'Nessuno...']) ?>
                    <button type="button" class="btn btn-sm btn-outline-success btn-attributo" data-tipo="colore" data-target="mgarticolo-id_colore"><i class="fas fa-plus"></i> Nuovo colore</button>
                </div>
            </div>
            <?php if (!$model->isNewRecord && !empty($model->guid)): ?>
                <div class="text-muted small">GUID: <code><?= Html::encode($model->guid) ?></code></div>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="tab-um" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="text-muted small">Il fattore indica quante unità base sono contenute nell'unità (es. Pallet = 100 pezzi).</div>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success" id="btn-nuova-um"><i class="fas fa-ruler"></i> Nuova unità</button>
                    <button type="button" class="btn btn-sm btn-primary" id="btn-add-um"><i class="fas fa-plus"></i> Aggiungi unità</button>
                </div>
            </div>
            <table class="table table-sm" id="um-table">
                <thead>
                <tr>
                    <th style="width:45%">Unità di misura</th>
                    <th style="width:25%" class="text-right">Fattore conversione</th>
                    <th style="width:15%" class="text-center">Predefinita</th>
                    <th style="width:15%"></th>
                </tr>
                </thead>
                <tbody id="um-body">
                <?php if (!empty($unitaArticolo)): ?>
                    <?php foreach ($unitaArticolo as $i => $u): ?>
                        <?= $renderUnitaRiga($i, $u) ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <table style="display:none;">
        <tbody id="um-template">
        <?= $renderUnitaRiga('__INDEX__', null) ?>
        </tbody>
    </table>

    <!-- Modale nuovo attributo -->
    <div class="modal fade" id="modal-attributo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white" id="modal-attributo-title">Nuovo attributo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="attr-tipo">
                    <input type="hidden" id="attr-target">
                    <div class="form-group">
                        <label>Descrizione *</label>
                        <input type="text" id="attr-descrizione" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Codice</label>
                        <input type="text" id="attr-codice" class="form-control">
                    </div>
                    <div id="attr-error" class="text-danger small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-success" id="btn-salva-attributo"><i class="fas fa-save"></i> Crea</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modale nuova unità di misura -->
    <div class="modal fade" id="modal-um" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title text-white"><i class="fas fa-ruler"></i> Nuova unità di misura</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Codice *</label>
                        <input type="text" id="um-codice" class="form-control" maxlength="10">
                    </div>
                    <div class="form-group">
                        <label>Descrizione *</label>
                        <input type="text" id="um-descrizione" class="form-control">
                    </div>
                    <div id="um-error" class="text-danger small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                    <button type="button" class="btn btn-success" id="btn-salva-um"><i class="fas fa-save"></i> Crea</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var createAttributoUrl = '<?= Url::to(['mgattributo/create-ajax']) ?>';
    var createUmUrl = '<?= Url::to(['mgunitamisura/create-ajax']) ?>';

    function aggiornaIndiciUm() {
        $('#um-body .um-row').each(function (i) {
            $(this).find('select, input').each(function () {
                var name = $(this).attr('name');
                if (name && name.indexOf('unita[') === 0) {
                    $(this).attr('name', name.replace(/unita\[[^\]]*\]/, 'unita[' + i + ']'));
                }
            });
            $(this).find('.um-predefinita').attr('data-index', i);
        });
    }

    function aggiornaPredefiniti() {
        $('#um-body .um-row').each(function () {
            var checked = $(this).find('.um-predefinita').is(':checked');
            $(this).find('.um-predef-hidden').val(checked ? '1' : '0');
        });
    }

    $('#btn-add-um').on('click', function () {
        var idx = $('#um-body .um-row').length;
        var html = $('#um-template').html().replace(/__INDEX__/g, idx);
        $('#um-body').append(html);
        aggiornaIndiciUm();
    });

    $(document).on('click', '.um-remove', function () {
        $(this).closest('.um-row').remove();
        aggiornaIndiciUm();
        aggiornaPredefiniti();
    });

    $(document).on('change', '.um-predefinita', function () {
        // una sola predefinita
        $('.um-predefinita').not(this).prop('checked', false);
        aggiornaPredefiniti();
    });

    $('#mgarticolo-form').on('submit', function () {
        aggiornaPredefiniti();
    });

    // --- Creazione inline attributi ---
    $(document).on('click', '.btn-attributo', function () {
        var $b = $(this);
        $('#attr-tipo').val($b.data('tipo'));
        $('#attr-target').val($b.data('target'));
        $('#modal-attributo-title').text('Nuovo ' + $b.data('tipo'));
        $('#attr-descrizione, #attr-codice').val('');
        $('#attr-error').text('');
        $('#modal-attributo').modal('show');
    });

    $('#btn-salva-attributo').on('click', function () {
        var descrizione = $('#attr-descrizione').val().trim();
        if (!descrizione) {
            $('#attr-error').text('La descrizione è obbligatoria.');
            return;
        }
        var data = {
            tipo: $('#attr-tipo').val(),
            descrizione: descrizione,
            codice: $('#attr-codice').val(),
            _csrf: (typeof yii !== 'undefined' ? yii.getCsrfToken() : '')
        };
        var btn = $(this).prop('disabled', true);
        $.post(createAttributoUrl, data, function (res) {
            btn.prop('disabled', false);
            if (res && res.success) {
                var a = res.attributo;
                var $sel = $('#' + $('#attr-target').val());
                $sel.append($('<option>').attr('value', a.id).text(a.etichetta));
                $sel.val(String(a.id));
                $('#modal-attributo').modal('hide');
            } else {
                $('#attr-error').text(res && res.errors ? JSON.stringify(res.errors) : (res.error || 'Errore di creazione.'));
            }
        }, 'json').fail(function () {
            btn.prop('disabled', false);
            $('#attr-error').text('Errore di rete.');
        });
    });

    // --- Creazione inline unità di misura ---
    $('#btn-nuova-um').on('click', function () {
        $('#um-codice, #um-descrizione').val('');
        $('#um-error').text('');
        $('#modal-um').modal('show');
    });

    $('#btn-salva-um').on('click', function () {
        var codice = $('#um-codice').val().trim();
        var descrizione = $('#um-descrizione').val().trim();
        if (!codice || !descrizione) {
            $('#um-error').text('Codice e Descrizione sono obbligatori.');
            return;
        }
        var data = {
            codice: codice,
            descrizione: descrizione,
            _csrf: (typeof yii !== 'undefined' ? yii.getCsrfToken() : '')
        };
        var btn = $(this).prop('disabled', true);
        $.post(createUmUrl, data, function (res) {
            btn.prop('disabled', false);
            if (res && res.success) {
                var u = res.unita;
                $('.um-select').add($('#um-template .um-select')).each(function () {
                    if (!$(this).find('option[value="' + u.id + '"]').length) {
                        $(this).append($('<option>').attr('value', u.id).text(u.etichetta));
                    }
                });
                $('#modal-um').modal('hide');
            } else {
                $('#um-error').text(res && res.errors ? JSON.stringify(res.errors) : (res.error || 'Errore di creazione.'));
            }
        }, 'json').fail(function () {
            btn.prop('disabled', false);
            $('#um-error').text('Errore di rete.');
        });
    });
})();
</script>
