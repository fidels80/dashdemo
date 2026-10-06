<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $tipi array */
/* @var $anagrafiche array */
/* @var $filtroQ string */
/* @var $filtroTipo string|null */
/* @var $filtroAnagrafica string|null */

$this->title = 'Contatti';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgcontatto-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::button('<i class="fas fa-plus"></i> Nuovo contatto', [
                'class' => 'btn btn-success btn-contatto-nuovo',
                'type' => 'button',
            ]) ?>
            <?= Html::a('<i class="fas fa-users"></i> Anagrafica', ['mganagrafica/index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="fas fa-address-book"></i> Tipi contatto', ['mgtipocontatto/index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <?= Html::beginForm(['index'], 'get', ['class' => 'card card-body mb-3']) ?>
        <div class="row">
            <div class="col-md-4 mb-2">
                <?= Html::textInput('q', $filtroQ, [
                    'class' => 'form-control',
                    'placeholder' => 'Cerca anagrafica o contatto...',
                ]) ?>
            </div>
            <div class="col-md-3 mb-2">
                <?= Html::dropDownList('id_tipo_contatto', $filtroTipo, $tipi, [
                    'prompt' => 'Tutti i tipi...',
                    'class' => 'form-control',
                ]) ?>
            </div>
            <div class="col-md-3 mb-2">
                <?= Html::dropDownList('id_anagrafica', $filtroAnagrafica, $anagrafiche, [
                    'prompt' => 'Tutte le anagrafiche...',
                    'class' => 'form-control',
                ]) ?>
            </div>
            <div class="col-md-2 mb-2">
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i> Filtra</button>
            </div>
        </div>
        <?php if ($filtroQ !== '' || !empty($filtroTipo) || !empty($filtroAnagrafica)): ?>
            <div><?= Html::a('<i class="fas fa-times"></i> Azzera filtri', ['index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?></div>
        <?php endif; ?>
    <?= Html::endForm() ?>

    <table id="mgcontatto-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Codice</th>
            <th>Anagrafica</th>
            <th>Tipo</th>
            <th>Contatto</th>
            <th>Etichetta</th>
            <th>Note</th>
            <th class="text-center">Predefinito</th>
            <th class="text-center">Attivo</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= Html::encode($m->anagrafica ? $m->anagrafica->codice : '') ?></td>
                <td><?= Html::a(
                    Html::encode($m->anagrafica ? $m->anagrafica->ragione_sociale : ''),
                    ['mganagrafica/view', 'id' => $m->id_anagrafica]
                ) ?></td>
                <td><i class="<?= Html::encode($m->icona) ?>"></i> <?= Html::encode($m->tipoLabel) ?></td>
                <td class="font-weight-bold"><?= Html::encode($m->valore) ?></td>
                <td><?= Html::encode($m->etichetta) ?></td>
                <td class="text-muted small"><?= Html::encode($m->note) ?></td>
                <td class="text-center"><?= $m->predefinito ? '<i class="fas fa-star text-warning"></i>' : '' ?></td>
                <td class="text-center"><?= $m->attivo ? 'Sì' : 'No' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::button('<i class="fas fa-pen"></i>', [
                        'class' => 'btn btn-sm btn-warning btn-contatto-modifica',
                        'type' => 'button',
                        'title' => 'Modifica',
                        'data' => [
                            'id' => $m->id,
                            'idAnagrafica' => $m->id_anagrafica,
                            'idTipoContatto' => $m->id_tipo_contatto,
                            'valore' => $m->valore,
                            'etichetta' => $m->etichetta,
                            'note' => $m->note,
                            'predefinito' => $m->predefinito ? 1 : 0,
                            'attivo' => $m->attivo ? 1 : 0,
                        ],
                    ]) ?>
                    <?= Html::button('<i class="fas fa-trash"></i>', [
                        'class' => 'btn btn-sm btn-danger btn-contatto-elimina',
                        'type' => 'button',
                        'title' => 'Elimina',
                        'data' => ['id' => $m->id],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?= $this->render('_form', [
    'anagrafiche' => $anagrafiche,
    'tipi' => $tipi,
    'anagraficaFissa' => null,
]) ?>

<?php DataTables::render('mgcontatto-table', 2, 'asc'); ?>
