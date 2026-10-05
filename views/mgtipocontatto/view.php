<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoContatto */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Tipi contatto', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$campo = function ($label, $valore, $col = 'col-md-3') {
    echo '<div class="' . $col . ' mb-3">'
        . '<label class="text-muted small mb-0 d-block">' . Html::encode($label) . '</label>'
        . '<div class="font-weight-bold">' . ($valore === '' || $valore === null ? '<span class="text-muted">—</span>' : $valore) . '</div>'
        . '</div>';
};
?>
<div class="mgtipocontatto-view">

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= Html::encode(Yii::$app->session->getFlash('error')) ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
        <div>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Eliminare questo tipo di contatto?', 'method' => 'post'],
            ]) ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Dati tipo contatto</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('ID', (int) $model->id, 'col-md-2');
                $campo('Codice', Html::encode($model->codice), 'col-md-3');
                $campo('Ordine', (int) $model->ordine, 'col-md-2');
                $campo('Attivo', $model->attivo ? 'Sì' : 'No', 'col-md-2');
                ?>
            </div>
            <div class="row">
                <?php $campo('Descrizione', Html::encode($model->descrizione), 'col-md-12'); ?>
            </div>
            <div class="row">
                <?php $campo('Icona', '<i class="' . Html::encode($model->icona ?: 'fas fa-address-card') . '"></i> <span class="text-muted">' . Html::encode($model->icona) . '</span>', 'col-md-6'); ?>
            </div>
        </div>
    </div>
</div>
