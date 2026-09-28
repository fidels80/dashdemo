<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $stato array */
/* @var $modello app\models\MgAttributoArticolo */
/* @var $voci array */

$this->title = 'Wizard prodotti - Colori';
$this->params['breadcrumbs'][] = ['label' => 'Articoli', 'url' => ['mgarticolo/index']];
$this->params['breadcrumbs'][] = ['label' => 'Wizard prodotti', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Colori';
?>
<div class="mgwizard-colori card p-3 shadow-sm">
    <?= $this->render('_steps', ['attivo' => 3]) ?>

    <h5 class="mb-1">3. Colori</h5>
    <p class="text-muted">Modello: <strong><?= Html::encode($modello ? $modello->etichetta : '') ?></strong>.
        Seleziona i colori da generare (puoi crearne di nuovi al volo).</p>

    <?= Html::beginForm(['colori'], 'post') ?>
    <?= $this->render('_selezione', [
        'campo' => 'colori',
        'tipo' => 'colore',
        'singolare' => 'Colore',
        'voci' => $voci,
        'selezionati' => $stato['colori'] ?? [],
    ]) ?>

    <div class="form-group mb-0">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Indietro', ['tessuti'], ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::submitButton('<i class="fas fa-arrow-right"></i> Avanti', ['class' => 'btn btn-success']) ?>
    </div>
    <?= Html::endForm() ?>
</div>

<?= $this->render('_script') ?>
