<?php

use yii\helpers\Html;
use app\models\MgAnagrafica;
use app\models\MgSottocommessa;

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */

$numero = (int) $model->numero;
$data = $model->data ? date('d/m/Y', strtotime((string) $model->data)) : '';

$this->title = 'Rapportino n. ' . $numero . ($data ? ' del ' . $data : '');
$this->params['breadcrumbs'][] = ['label' => 'Rapportini', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$cliente = $model->cd_cli ? MgAnagrafica::findOne(['codice' => $model->cd_cli]) : null;
$altCliente = $model->altcli ? MgAnagrafica::findOne(['codice' => trim((string) $model->altcli)]) : null;
$sottocommessa = $model->commessa ? MgSottocommessa::findOne(['codice' => $model->commessa]) : null;
$utente = $model->userid
    ? (new \yii\db\Query())->select('username')->from('user')->where(['id' => $model->userid])->scalar()
    : null;
$evaso = ((int) $model->evaso === 1);

$ora = function ($v) {
    return ($v === null || $v === '') ? '' : substr((string) $v, 0, 5);
};
$campo = function ($label, $valore, $col = 'col-md-3') {
    echo '<div class="' . $col . ' mb-3">'
        . '<label class="text-muted small mb-0 d-block">' . Html::encode($label) . '</label>'
        . '<div class="font-weight-bold">' . ($valore === '' || $valore === null ? '<span class="text-muted">—</span>' : Html::encode($valore)) . '</div>'
        . '</div>';
};
?>
<div class="rapportini-view">

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= Html::encode(Yii::$app->session->getFlash('error')) ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <?= Html::a('<i class="fas fa-arrow-left"></i> Torna alla lista', ['index'], ['class' => 'btn btn-secondary']) ?>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-print"></i> Stampa', ['stampa', 'id' => $model->id], [
                'class' => 'btn btn-outline-dark',
                'target' => '_blank',
            ]) ?>
            <?php if ($evaso): ?>
                <?= Html::a('<i class="fas fa-trash"></i> Elimina', '#', [
                    'class' => 'btn btn-danger disabled',
                    'title' => 'Rapportino evaso in un documento: non eliminabile',
                ]) ?>
            <?php else: ?>
                <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Eliminare questo rapportino?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif; ?>
        </div>
        <div>
            <?php if ($evaso): ?>
                <span class="badge badge-success px-3 py-2"><i class="fas fa-check"></i> Evaso</span>
            <?php else: ?>
                <span class="badge badge-secondary px-3 py-2"><i class="fas fa-clock"></i> Disponibile</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Dati rapportino</span>
            <span class="text-muted small">Rapportino n. <?= $numero ?></span>
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('Numero', $numero, 'col-md-2');
                $campo('Data', $data, 'col-md-3');
                $campo('Operatore', $utente ?: $model->userid, 'col-md-3');
                $campo('Stato', $evaso ? 'Evaso' : 'Disponibile', 'col-md-2');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Cliente', $cliente ? $cliente->codice . ' - ' . $cliente->ragione_sociale : $model->cd_cli, 'col-md-6');
                $campo('Cliente alternativo', $altCliente ? $altCliente->codice . ' - ' . $altCliente->ragione_sociale : trim((string) $model->altcli), 'col-md-6');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Sottocommessa', $sottocommessa ? $sottocommessa->codice . ' - ' . $sottocommessa->descrizione : $model->commessa, 'col-md-12');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Codice articolo', $model->cd_art, 'col-md-4');
                $campo('Descrizione articolo', $model->des_art, 'col-md-6');
                $campo('Quantità', $model->qta, 'col-md-2');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Ora inizio', $ora($model->ora_in), 'col-md-3');
                $campo('Ora fine', $ora($model->ora_out), 'col-md-3');
                $campo('Pausa inizio', $ora($model->pausa_in), 'col-md-3');
                $campo('Pausa fine', $ora($model->pausa_out), 'col-md-3');
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Note</div>
        <div class="card-body">
            <?php if (!empty($model->note)): ?>
                <div style="white-space:pre-wrap;"><?= Html::encode($model->note) ?></div>
            <?php else: ?>
                <span class="text-muted">Nessuna nota.</span>
            <?php endif; ?>
        </div>
    </div>

</div>
