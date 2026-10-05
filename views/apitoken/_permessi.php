<?php

use yii\helpers\Html;
use app\components\ApiAccess;

/* @var $this yii\web\View */
/* @var $entita app\models\DashApiEntita[] */
/* @var $operazioni array<string, string> */
/* @var $selezionati array<string, string[]> */

$ops = array_keys($operazioni);
?>
<div class="alert alert-light border">
    <div class="mb-2">
        <strong>Permessi per entità</strong> — spunta le operazioni consentite su ciascuna tabella.
        Un token senza spunte non può fare nulla.
    </div>
    <div class="d-flex flex-wrap align-items-center" style="gap:.5rem;">
        <button type="button" class="btn btn-sm btn-outline-secondary" id="perm-pieno">
            <i class="fas fa-key"></i> Pieno accesso (tutto su tutto)
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="perm-lettura">
            <i class="fas fa-eye"></i> Solo lettura su tutto
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="perm-svuota">
            <i class="fas fa-eraser"></i> Svuota tutto
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-sm table-striped table-bordered mb-0" id="tabella-permessi">
        <thead class="thead-light">
        <tr>
            <th>Entità (tabella)</th>
            <?php foreach ($operazioni as $op => $etichetta): ?>
                <th class="text-center" style="min-width:7rem;">
                    <?= Html::encode($etichetta) ?>
                    <br>
                    <button type="button" class="btn btn-link btn-sm p-0 small text-muted toggle-colonna"
                            data-op="<?= Html::encode($op) ?>" title="Seleziona/deseleziona tutto">
                        <i class="fas fa-check-double"></i> tutto
                    </button>
                </th>
            <?php endforeach; ?>
            <th class="text-center no-export" style="min-width:9rem;">Preset</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($entita as $e): ?>
            <?php
            $attive = isset($selezionati[$e->codice]) ? $selezionati[$e->codice] : [];
            $vincoli = \app\models\DashApiRel::perEntita($e->codice);
            ?>
            <tr data-codice="<?= Html::encode($e->codice) ?>">
                <td>
                    <strong><?= Html::encode($e->descrizione) ?></strong>
                    <br>
                    <small class="text-muted">
                        <code><?= Html::encode($e->codice) ?></code> · <?= Html::encode($e->tabella) ?>
                    </small>
                    <?php if ((int) $e->sola_lettura): ?>
                        <span class="badge badge-secondary">sola lettura</span>
                    <?php endif; ?>
                    <?php if (!empty($vincoli)): ?>
                        <br>
                        <small class="text-muted" title="<?= Html::encode(
                            implode(' · ', array_map(function ($r) {
                                return ($r->isFiglio() ? 'ha ' : 'citato da ') . $r->etichetta;
                            }, $vincoli))
                        ) ?>">
                            <i class="fas fa-link"></i> <?= count($vincoli) ?> vincoli di integrità
                        </small>
                    <?php endif; ?>
                </td>
                <?php foreach ($operazioni as $op => $etichetta): ?>
                    <?php
                    $bloccata = ($op !== ApiAccess::OP_READ && (int) $e->sola_lettura)
                        || ($op === ApiAccess::OP_DELETE && !$e->cancellabile);
                    ?>
                    <td class="text-center align-middle">
                        <input type="checkbox" class="form-check-input casella-permesso"
                               data-op="<?= Html::encode($op) ?>"
                               name="permessi[<?= Html::encode($e->codice) ?>][]"
                               value="<?= Html::encode($op) ?>"
                               <?= in_array($op, $attive, true) ? 'checked' : '' ?>
                               <?= $bloccata ? 'disabled' : '' ?>>
                    </td>
                <?php endforeach; ?>
                <td class="text-center text-nowrap no-export">
                    <button type="button" class="btn btn-sm btn-outline-secondary riga-sola-lettura"
                            title="Solo lettura su questa entità">L</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary riga-senza-cancellazione"
                            title="Lettura, inserimento e modifica">L+I+M</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary riga-tutto"
                            title="Tutte le operazioni consentite">T</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$js = <<<'JS'
(function () {
    var tabella = document.getElementById('tabella-permessi');
    if (!tabella) { return; }

    function caselle() {
        return Array.prototype.slice.call(tabella.querySelectorAll('.casella-permesso:not([disabled])'));
    }

    function imposta(op, stato) {
        caselle().forEach(function (c) {
            if (!op || c.dataset.op === op) { c.checked = stato; }
        });
    }

    function perRiga(riga, ops) {
        riga.querySelectorAll('.casella-permesso').forEach(function (c) {
            if (c.disabled) { c.checked = false; return; }
            c.checked = ops.indexOf(c.dataset.op) >= 0;
        });
    }

    var b = document.getElementById('perm-pieno');
    if (b) { b.addEventListener('click', function () { imposta(null, true); }); }

    var l = document.getElementById('perm-lettura');
    if (l) { l.addEventListener('click', function () { imposta(null, false); imposta('read', true); }); }

    var s = document.getElementById('perm-svuota');
    if (s) { s.addEventListener('click', function () { imposta(null, false); }); }

    tabella.querySelectorAll('.toggle-colonna').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var op = btn.dataset.op;
            var attive = tabella.querySelectorAll('.casella-permesso[data-op="' + op + '"]:not([disabled]):checked');
            imposta(op, attive.length === 0);
        });
    });

    tabella.querySelectorAll('tbody tr').forEach(function (riga) {
        var ro = riga.querySelector('.riga-sola-lettura');
        if (ro) { ro.addEventListener('click', function () { perRiga(riga, ['read']); }); }

        var lim = riga.querySelector('.riga-senza-cancellazione');
        if (lim) { lim.addEventListener('click', function () { perRiga(riga, ['read', 'insert', 'update']); }); }

        var tutto = riga.querySelector('.riga-tutto');
        if (tutto) { tutto.addEventListener('click', function () { perRiga(riga, ['read', 'insert', 'update', 'delete']); }); }
    });
})();
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>
