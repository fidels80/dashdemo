<?php

use yii\helpers\Url;
?>
<script>
(function () {
    var createUrl = '<?= Url::to(['mgattributo/create-ajax']) ?>';

    $(document).on('keyup change', '.mgw-filtro', function () {
        var q = ($(this).val() || '').toLowerCase();
        $('#' + $(this).data('target') + ' .mgw-voce').each(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(q) !== -1);
        });
    });

    $(document).on('click', '.mgw-toggle', function () {
        var $lista = $('#' + $(this).data('target'));
        var check = $(this).data('mode') === 'all';
        $lista.find('.mgw-voce:visible input[type=checkbox]').prop('checked', check);
    });

    $(document).on('click', '.btn-attributo-wizard', function () {
        var campo = $(this).data('campo');
        var $m = $('#modal-' + campo);
        $m.find('.mgw-nuovo-desc, .mgw-nuovo-cod').val('');
        $m.find('.mgw-nuovo-error').text('');
        $m.modal('show');
    });

    $(document).on('click', '.mgw-nuovo-salva', function () {
        var $b = $(this);
        var descrizione = $('#modal-' + $b.data('campo') + ' .mgw-nuovo-desc').val().trim();
        var codice = $('#modal-' + $b.data('campo') + ' .mgw-nuovo-cod').val().trim();
        var $err = $('#modal-' + $b.data('campo') + ' .mgw-nuovo-error');
        if (!descrizione) {
            $err.text('La descrizione è obbligatoria.');
            return;
        }
        $b.prop('disabled', true);
        $.post(createUrl, {
            tipo: $b.data('tipo'),
            descrizione: descrizione,
            codice: codice,
            _csrf: (typeof yii !== 'undefined' ? yii.getCsrfToken() : '')
        }, function (res) {
            $b.prop('disabled', false);
            if (res && res.success) {
                var a = res.attributo;
                var campo = $b.data('campo');
                var $lista = $('#' + $b.data('lista'));
                $lista.find('.text-muted.small').remove();
                var html = '<div class="custom-control custom-checkbox mgw-voce">'
                    + '<input type="checkbox" class="custom-control-input" name="' + campo + '[]" id="' + campo + '-' + a.id + '" value="' + a.id + '" checked>'
                    + '<label class="custom-control-label" for="' + campo + '-' + a.id + '">' + $('<div>').text(a.etichetta).html() + '</label>'
                    + '</div>';
                $lista.append(html);
                $('#modal-' + campo).modal('hide');
            } else {
                $err.text(res && res.errors ? JSON.stringify(res.errors) : (res.error || 'Errore di creazione.'));
            }
        }, 'json').fail(function () {
            $b.prop('disabled', false);
            $err.text('Errore di rete.');
        });
    });
})();
</script>
