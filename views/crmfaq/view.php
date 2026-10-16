<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgCrmFaq */

$this->title = 'FAQ';
$this->params['breadcrumbs'][] = ['label' => 'FAQ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crmfaq-view card p-3 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><?= Html::encode($model->domanda) ?></h4>
        <div>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
            <?= Html::a('<i class="fas fa-list"></i> Elenco', ['index'], ['class' => 'btn btn-secondary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Eliminare questa FAQ?', 'method' => 'post'],
            ]) ?>
        </div>
    </div>

    <dl class="row">
        <dt class="col-sm-3">Articolo</dt>
        <dd class="col-sm-9"><?= Html::encode($model->articolo ? $model->articolo->codice . ' - ' . $model->articolo->descrizione : ($model->codice_articolo ?: '—')) ?></dd>
        <dt class="col-sm-3">Attivo</dt><dd class="col-sm-9"><?= $model->attivo ? 'Sì' : 'No' ?></dd>
        <dt class="col-sm-3">Ticket di origine</dt>
        <dd class="col-sm-9"><?= $model->ticket ? Html::a($model->ticket->etichetta, ['crmticket/view', 'id' => $model->id_ticket]) : '—' ?></dd>
        <dt class="col-sm-3">Domanda</dt><dd class="col-sm-9"><?= nl2br(Html::encode($model->domanda)) ?></dd>
        <dt class="col-sm-3">Soluzione</dt><dd class="col-sm-9"><?= nl2br(Html::encode($model->soluzione ?? '—')) ?></dd>
    </dl>
</div>
