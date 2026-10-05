<?php

use yii\helpers\Url;

/* Modale riutilizzabile per il dettaglio del rapportino collegato a una riga. */
?>
<div class="modal fade" id="modal-rap-dettaglio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white"><i class="fas fa-file-alt"></i> Dettaglio rapportino</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="rap-dettaglio-body">
                <div class="text-muted">Caricamento...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    var rapUrl = '<?= Url::to(['mgdocumento/rapportino']) ?>';
    $(document).on('click', '.riga-rap-dettaglio', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id-rap');
        if (!id) { return; }
        $('#rap-dettaglio-body').html('<div class="text-muted">Caricamento...</div>');
        $('#modal-rap-dettaglio').modal('show');
        $.get(rapUrl, { id: id }, function (html) {
            $('#rap-dettaglio-body').html(html);
        }).fail(function () {
            $('#rap-dettaglio-body').html('<div class="text-danger">Errore di caricamento.</div>');
        });
    });
})();
</script>
