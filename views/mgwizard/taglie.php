<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $stato array */
/* @var $modello app\models\MgAttributoArticolo */
/* @var $voci array */

$this->title = 'Wizard prodotti - Taglie';
$this->params['breadcrumbs'][] = ['label' => 'Articoli', 'url' => ['mgarticolo/index']];
$this->params['breadcrumbs'][] = ['label' => 'Wizard prodotti', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Taglie';
?>
<div class="mgwizard-taglie card p-3 shadow-sm">
    <?= $this->render('_steps', ['attivo' => 4]) ?>

    <h5 class="mb-1">4. Taglie</h5>
    <p class="text-muted">Modello: <strong><?= Html::encode($modello ? $modello->etichetta : '') ?></strong>.
        Le taglie sono illimitate: seleziona quelle necessarie oppure creane di nuove.
        Se non selezioni taglie, verrà creato un articolo senza taglia per ogni combinazione tessuto/colore.</p>

    <?= Html::beginForm(['taglie'], 'post') ?>
    <?= $this->render('_selezione', [
        'campo' => 'taglie',
        'tipo' => 'taglia',
        'singolare' => 'Taglia',
        'voci' => $voci,
        'selezionati' => $stato['taglie'] ?? [],
    ]) ?>

    <div class="form-group mb-0">
        <?= Html::a('<i class="fas fa-arrow-left"></i> Indietro', ['colori'], ['class' => 'btn btn-outline-secondary']) ?>
        <?= Html::submitButton('<i class="fas fa-arrow-right"></i> Avanti', ['class' => 'btn btn-success']) ?>
    </div>
    <?= Html::endForm() ?>
</div>

<?= $this->render('_script') ?>
