<?php

use yii\helpers\Html;
use app\models\MgAnagraficaContatto;
use app\models\MgTipoContatto;
use app\components\FatturaElettronica;

/* @var $this yii\web\View */
/* @var $model app\models\MgAnagrafica */

$this->title = $model->ragione_sociale;
$this->params['breadcrumbs'][] = ['label' => 'Anagrafica', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$siNo = function ($v) {
    return $v ? 'Sì' : 'No';
};
$campo = function ($label, $valore, $col = 'col-md-3') {
    echo '<div class="' . $col . ' mb-3">'
        . '<label class="text-muted small mb-0 d-block">' . Html::encode($label) . '</label>'
        . '<div class="font-weight-bold">' . ($valore === '' || $valore === null ? '<span class="text-muted">—</span>' : Html::encode($valore)) . '</div>'
        . '</div>';
};

$contatti = MgAnagraficaContatto::find()
    ->alias('c')
    ->with('tipoContatto')
    ->joinWith(['tipoContatto t'])
    ->where(['c.id_anagrafica' => $model->id])
    ->orderBy(['t.ordine' => SORT_ASC, 't.descrizione' => SORT_ASC, 'c.valore' => SORT_ASC])
    ->all();
$tipiContatto = MgTipoContatto::mapAttivi();
?>
<div class="mganagrafica-view">

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
        <div>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Eliminare questa anagrafica?', 'method' => 'post'],
            ]) ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Dati anagrafica</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('ID', $model->id, 'col-md-2');
                $campo('Codice', $model->codice, 'col-md-3');
                $campo('Attivo', $siNo($model->attivo), 'col-md-2');
                ?>
            </div>
            <div class="row">
                <?php $campo('Ragione sociale', $model->ragione_sociale, 'col-md-12'); ?>
            </div>
            <div class="row">
                <?php
                $campo('Partita IVA', $model->partita_iva, 'col-md-4');
                $campo('Codice fiscale', $model->codice_fiscale, 'col-md-4');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Indirizzo', $model->indirizzo, 'col-md-6');
                $campo('CAP', $model->cap, 'col-md-2');
                $campo('Città', $model->citta, 'col-md-3');
                $campo('Provincia', $model->provincia, 'col-md-1');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Telefono', $model->telefono, 'col-md-4');
                $campo('Email', $model->email, 'col-md-4');
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Ruoli e condizioni</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('Cliente', $siNo($model->is_cliente), 'col-md-2');
                $campo('Fornitore', $siNo($model->is_fornitore), 'col-md-2');
                $campo('Agente', $siNo($model->is_agente), 'col-md-2');
                $campo('Provvigione', number_format((float) $model->perc_provvigione, 2, ',', '.') . ' %', 'col-md-3');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Metodo di pagamento', $model->metodoPagamento->descrizione ?? '', 'col-md-6');
                $campo('Aliquota IVA', $model->aliquotaIva
                    ? $model->aliquotaIva->descrizione . ' (' . number_format((float) $model->aliquotaIva->percentuale, 2, ',', '.') . '%)'
                    : '', 'col-md-6');
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fas fa-file-invoice"></i> Documenti elettronici</div>
        <div class="card-body">
            <div class="row">
                <?php
                $tipiSoggetto = FatturaElettronica::opzioniTipoSoggetto();
                $campo('Tipo soggetto', $tipiSoggetto[$model->fe_tipo_soggetto] ?? $model->fe_tipo_soggetto, 'col-md-4');
                $campo('Codice destinatario', $model->fe_codice_destinatario, 'col-md-4');
                $campo('PEC', $model->fe_pec, 'col-md-4');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Nome', $model->fe_nome, 'col-md-4');
                $campo('Cognome', $model->fe_cognome, 'col-md-4');
                $campo('Paese', $model->fe_id_paese, 'col-md-2');
                $campo('Nazione', $model->fe_nazione, 'col-md-2');
                ?>
            </div>
            <div class="row">
                <?php
                $regimi = FatturaElettronica::opzioniRegimeFiscale();
                $campo('Regime fiscale', $model->fe_regime_fiscale ? ($regimi[$model->fe_regime_fiscale] ?? $model->fe_regime_fiscale) : '', 'col-md-6');
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Contatti</span>
            <?= Html::button('<i class="fas fa-plus"></i> Nuovo contatto', [
                'class' => 'btn btn-sm btn-success btn-contatto-nuovo',
                'type' => 'button',
            ]) ?>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
                <thead>
                <tr>
                    <th style="width:180px">Tipo</th>
                    <th>Contatto</th>
                    <th>Etichetta</th>
                    <th>Note</th>
                    <th class="text-center" style="width:110px">Predefinito</th>
                    <th class="text-center" style="width:80px">Attivo</th>
                    <th class="text-center text-nowrap" style="width:110px">Azioni</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($contatti as $c): ?>
                    <tr>
                        <td><i class="<?= Html::encode($c->icona) ?>"></i> <?= Html::encode($c->tipoLabel) ?></td>
                        <td class="font-weight-bold"><?= Html::encode($c->valore) ?></td>
                        <td><?= Html::encode($c->etichetta) ?></td>
                        <td class="text-muted small"><?= Html::encode($c->note) ?></td>
                        <td class="text-center"><?= $c->predefinito ? '<i class="fas fa-star text-warning"></i>' : '' ?></td>
                        <td class="text-center"><?= $c->attivo ? 'Sì' : 'No' ?></td>
                        <td class="text-center text-nowrap">
                            <?= Html::button('<i class="fas fa-pen"></i>', [
                                'class' => 'btn btn-sm btn-warning btn-contatto-modifica',
                                'type' => 'button',
                                'title' => 'Modifica',
                                'data' => [
                                    'id' => $c->id,
                                    'idTipoContatto' => $c->id_tipo_contatto,
                                    'valore' => $c->valore,
                                    'etichetta' => $c->etichetta,
                                    'note' => $c->note,
                                    'predefinito' => $c->predefinito ? 1 : 0,
                                    'attivo' => $c->attivo ? 1 : 0,
                                ],
                            ]) ?>
                            <?= Html::button('<i class="fas fa-trash"></i>', [
                                'class' => 'btn btn-sm btn-danger btn-contatto-elimina',
                                'type' => 'button',
                                'title' => 'Elimina',
                                'data' => ['id' => $c->id],
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($contatti)): ?>
                    <tr><td colspan="7" class="text-muted p-3">Nessun contatto registrato.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->render('//mgcontatto/_form', [
    'anagrafiche' => [$model->id => $model->ragione_sociale],
    'tipi' => $tipiContatto,
    'anagraficaFissa' => $model->id,
]) ?>

