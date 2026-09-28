<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $campo string nome del campo POST (es. tessuti) */
/* @var $tipo string tipo attributo (es. tessuto) */
/* @var $singolare string etichetta singolare */
/* @var $voci array id => etichetta */
/* @var $selezionati array id selezionati */

$selezionati = array_map('strval', (array) $selezionati);
$modalId = 'modal-' . $campo;
$listaId = 'lista-' . $campo;
?>
<div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
    <div class="mb-2">
        <input type="text" class="form-control form-control-sm mgw-filtro" data-target="<?= $listaId ?>"
               placeholder="Filtra <?= Html::encode(strtolower($singolare)) ?>...">
    </div>
    <div class="mb-2">
        <button type="button" class="btn btn-sm btn-outline-secondary mgw-toggle" data-target="<?= $listaId ?>" data-mode="all">Seleziona tutti</button>
        <button type="button" class="btn btn-sm btn-outline-secondary mgw-toggle" data-target="<?= $listaId ?>" data-mode="none">Deseleziona</button>
        <button type="button" class="btn btn-sm btn-outline-success btn-attributo-wizard"
                data-tipo="<?= Html::encode($tipo) ?>" data-lista="<?= $listaId ?>" data-campo="<?= $campo ?>">
            <i class="fas fa-plus"></i> Nuovo <?= Html::encode(strtolower($singolare)) ?>
        </button>
    </div>
</div>

<div id="<?= $listaId ?>" class="border rounded p-2 mb-3" style="max-height:320px;overflow:auto;">
    <?php if (empty($voci)): ?>
        <div class="text-muted small">Nessun <?= Html::encode(strtolower($singolare)) ?> disponibile: creane uno con il pulsante "Nuovo".</div>
    <?php endif; ?>
    <?php foreach ($voci as $id => $etichetta): ?>
        <div class="custom-control custom-checkbox mgw-voce">
            <input type="checkbox" class="custom-control-input" name="<?= Html::encode($campo) ?>[]"
                   id="<?= Html::encode($campo) ?>-<?= (int) $id ?>" value="<?= (int) $id ?>"
                   <?= in_array((string) $id, $selezionati, true) ? 'checked' : '' ?>>
            <label class="custom-control-label" for="<?= Html::encode($campo) ?>-<?= (int) $id ?>"><?= Html::encode($etichetta) ?></label>
        </div>
    <?php endforeach; ?>
</div>

<div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">Nuovo <?= Html::encode(strtolower($singolare)) ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Descrizione *</label>
                    <input type="text" class="form-control mgw-nuovo-desc">
                </div>
                <div class="form-group">
                    <label>Codice</label>
                    <input type="text" class="form-control mgw-nuovo-cod">
                </div>
                <div class="text-danger small mgw-nuovo-error"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-success mgw-nuovo-salva"
                        data-tipo="<?= Html::encode($tipo) ?>" data-lista="<?= $listaId ?>"
                        data-campo="<?= Html::encode($campo) ?>" data-modal="<?= $modalId ?>">
                    <i class="fas fa-save"></i> Crea
                </button>
            </div>
        </div>
    </div>
</div>
