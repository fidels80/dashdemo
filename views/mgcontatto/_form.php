<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $anagrafiche array */
/* @var $tipi array */
/* @var $anagraficaFissa int|null */

$anagraficaFissa = $anagraficaFissa ?? null;
?>
<div class="modal fade" id="contatto-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white" id="contatto-modal-title">Nuovo contatto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <?php $form = ActiveForm::begin(['id' => 'contatto-form']); ?>
                <input type="hidden" name="id" id="contatto-id" value="">
                <?php if ($anagraficaFissa): ?>
                    <input type="hidden" name="MgAnagraficaContatto[id_anagrafica]" id="contatto-anagrafica" value="<?= (int) $anagraficaFissa ?>">
                    <div class="form-group">
                        <label>Anagrafica</label>
                        <div class="form-control-plaintext font-weight-bold"><?= Html::encode($anagrafiche[$anagraficaFissa] ?? '') ?></div>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label for="contatto-anagrafica">Anagrafica *</label>
                        <?= Html::dropDownList('MgAnagraficaContatto[id_anagrafica]', null, $anagrafiche, [
                            'id' => 'contatto-anagrafica',
                            'class' => 'form-control',
                            'prompt' => 'Seleziona anagrafica...',
                        ]) ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="contatto-tipo">Tipo *</label>
                    <?= Html::dropDownList('MgAnagraficaContatto[id_tipo_contatto]', null, $tipi, [
                        'id' => 'contatto-tipo',
                        'class' => 'form-control',
                        'prompt' => 'Seleziona tipo...',
                    ]) ?>
                </div>
                <div class="form-group">
                    <label for="contatto-valore">Contatto *</label>
                    <input type="text" name="MgAnagraficaContatto[valore]" id="contatto-valore" class="form-control" maxlength="200">
                </div>
                <div class="form-group">
                    <label for="contatto-etichetta">Etichetta</label>
                    <input type="text" name="MgAnagraficaContatto[etichetta]" id="contatto-etichetta" class="form-control" maxlength="100" placeholder="es. Referente amministrazione">
                </div>
                <div class="form-group">
                    <label for="contatto-note">Note</label>
                    <textarea name="MgAnagraficaContatto[note]" id="contatto-note" class="form-control" rows="2" maxlength="500"></textarea>
                </div>
                <div class="form-check">
                    <input type="hidden" name="MgAnagraficaContatto[predefinito]" value="0">
                    <input type="checkbox" class="form-check-input" name="MgAnagraficaContatto[predefinito]" id="contatto-predefinito" value="1">
                    <label class="form-check-label" for="contatto-predefinito">Predefinito</label>
                </div>
                <div class="form-check">
                    <input type="hidden" name="MgAnagraficaContatto[attivo]" value="0">
                    <input type="checkbox" class="form-check-input" name="MgAnagraficaContatto[attivo]" id="contatto-attivo" value="1" checked>
                    <label class="form-check-label" for="contatto-attivo">Attivo</label>
                </div>
                <div id="contatto-errori" class="mt-2"></div>
                <?php ActiveForm::end(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-success" id="btn-salva-contatto"><i class="fas fa-save"></i> Salva</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var saveUrl = '<?= Url::to(['mgcontatto/save-ajax']) ?>';
    var deleteUrl = '<?= Url::to(['mgcontatto/delete-ajax']) ?>';
    var anagraficaFissa = '<?= $anagraficaFissa ? (int) $anagraficaFissa : '' ?>';

    function resetForm() {
        $('#contatto-id').val('');
        $('#contatto-tipo').val('');
        $('#contatto-valore').val('');
        $('#contatto-etichetta').val('');
        $('#contatto-note').val('');
        $('#contatto-predefinito').prop('checked', false);
        $('#contatto-attivo').prop('checked', true);
        $('#contatto-errori').html('');
        if (anagraficaFissa === '') {
            $('#contatto-anagrafica').val('');
        }
    }

    function mostraErrori(msgs) {
        $('#contatto-errori').html(
            $('<div class="alert alert-danger mb-0">').append(
                $('<ul class="mb-0 pl-3">').append(
                    $.map(msgs, function (m) {
                        return $('<li>').text(m);
                    })
                )
            )
        );
    }

    $('.btn-contatto-nuovo').on('click', function () {
        resetForm();
        $('#contatto-modal-title').text('Nuovo contatto');
        $('#contatto-modal').modal('show');
    });

    $(document).on('click', '.btn-contatto-modifica', function () {
        resetForm();
        var $b = $(this);
        $('#contatto-id').val($b.data('id'));
        if (anagraficaFissa === '') {
            $('#contatto-anagrafica').val(String($b.data('idAnagrafica')));
        }
        $('#contatto-tipo').val(String($b.data('idTipoContatto')));
        $('#contatto-valore').val($b.data('valore'));
        $('#contatto-etichetta').val($b.data('etichetta'));
        $('#contatto-note').val($b.data('note'));
        $('#contatto-predefinito').prop('checked', String($b.data('predefinito')) === '1');
        $('#contatto-attivo').prop('checked', String($b.data('attivo')) === '1');
        $('#contatto-modal-title').text('Modifica contatto');
        $('#contatto-modal').modal('show');
    });

    $('#btn-salva-contatto').on('click', function () {
        var anagrafica = $('#contatto-anagrafica').val();
        var tipo = $('#contatto-tipo').val();
        var valore = $.trim($('#contatto-valore').val());
        if (!anagrafica || !tipo || !valore) {
            mostraErrori(['Anagrafica, tipo e contatto sono obbligatori.']);
            return;
        }

        var $btn = $(this).prop('disabled', true);
        $.post(saveUrl, $('#contatto-form').serialize())
            .done(function (res) {
                $btn.prop('disabled', false);
                if (res && res.success) {
                    window.location.reload();
                    return;
                }
                var msgs = (res && res.errors) ? res.errors : [res && res.error ? res.error : 'Salvataggio non riuscito.'];
                mostraErrori(msgs);
            })
            .fail(function () {
                $btn.prop('disabled', false);
                mostraErrori(['Errore durante il salvataggio.']);
            });
    });

    $(document).on('click', '.btn-contatto-elimina', function () {
        if (!window.confirm('Eliminare questo contatto?')) {
            return;
        }
        var $b = $(this);
        $.post(deleteUrl, {id: $b.data('id'), _csrf: yii.getCsrfToken()})
            .done(function (res) {
                if (res && res.success) {
                    window.location.reload();
                    return;
                }
                window.alert(res && res.error ? res.error : 'Eliminazione non riuscita.');
            })
            .fail(function () {
                window.alert('Errore durante l\'eliminazione.');
            });
    });
})();
</script>
