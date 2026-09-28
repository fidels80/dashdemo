<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $stato array */
/* @var $modello app\models\MgAttributoArticolo */
/* @var $voci array */

$this->title = 'Wizard prodotti - Tessuti';
$this->params['breadcrumbs'][] = ['label' => 'Articoli', 'url' => ['mgarticolo/index']];
$this->params['breadcrumbs'][] = ['label' => 'Wizard prodotti', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Tessuti';
?>
<div class="mgwizard-tessuti card p-3 shadow-sm">
    <?= $this->render('_steps', ['attivo' => 2]) ?>

    <h5 class="mb-1">2. Tessuti disponibili per il modello</h5>
    <p class="text-muted">Modello: <strong><?= Html::encode($modello ? $modello->etichetta : '') ?></strong>.
        Seleziona i tessuti utilizzabili (verranno salvati come tessuti del modello).</p>

    <?= Html::beginForm(['tessuti'], 'post') ?>
    <?= $this->render('_selezione', [
        'campo' => 'tessuti',
        'tipo' => 'tessuto',
        'singolare' => 'Tessuto',
        'voci' => $voci,
        'selezionati' => $stato['tessuti'] ?? [],
    ]) ?>

    <div class="form-group mb-0">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Indietro', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::submitButton('<i class="fas fa-arrow-right"></i> Avanti', ['class' => 'btn btn-success']) ?>
    </div>
    <?= Html::endForm() ?>
</div>

<?= $this->render('_script') ?>
