<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\models\MgCrmAllegato;

/* @var $this yii\web\View */
/* @var $model app\models\MgCrmTicket */
/* @var $messaggio app\models\MgCrmTicketMessaggio */

$renderAllegati = function ($lista) {
    foreach ($lista as $a) {
        echo '<div class="d-flex align-items-center border rounded p-1 mb-1">';
        echo '<i class="' . Html::encode($a->icona) . ' mr-2"></i>';
        echo Html::a(Html::encode($a->nome_originale), ['allegato', 'id' => $a->id], ['target' => '_blank']);
        echo ' <small class="text-muted ml-1">(' . Html::encode($a->dimensioneLabel) . ')</small>';
        echo '</div>';
    }
};
$allegatiTicket = MgCrmAllegato::perTicket($model->id);

$this->title = $model->etichetta;
?>
<div class="portale-richiesta">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <?= Html::encode($model->etichetta) ?>
            <span class="badge badge-<?= Html::encode($model->statoColore) ?>"><?= Html::encode($model->stato->descrizione ?? '') ?></span>
        </h4>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Indietro', ['richieste'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fas fa-info-circle"></i> Dettagli</div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Tipo</dt><dd class="col-sm-9"><?= Html::encode($model->tipo->descrizione ?? '') ?></dd>
                <dt class="col-sm-3">Azienda</dt><dd class="col-sm-9"><?= Html::encode($model->anagrafica->ragione_sociale ?? '—') ?></dd>
                <dt class="col-sm-3">Oggetto</dt><dd class="col-sm-9"><?= Html::encode($model->oggetto) ?></dd>
                <dt class="col-sm-3">Articolo</dt><dd class="col-sm-9"><?= Html::encode($model->articolo ? $model->articolo->codice . ' - ' . $model->articolo->descrizione : '—') ?></dd>
                <dt class="col-sm-3">Matricola</dt><dd class="col-sm-9"><?= Html::encode($model->matricola->etichetta ?? '—') ?></dd>
                <dt class="col-sm-3">Apertura</dt><dd class="col-sm-9"><?= $model->data_apertura ? Html::encode(date('d/m/Y H:i', strtotime($model->data_apertura))) : '—' ?></dd>
                <dt class="col-sm-3">Descrizione</dt><dd class="col-sm-9"><?= nl2br(Html::encode($model->descrizione ?? '')) ?></dd>
            </dl>
        </div>
    </div>

    <?php if (!empty($allegatiTicket)): ?>
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-paperclip"></i> Allegati</div>
            <div class="card-body"><?php $renderAllegati($allegatiTicket); ?></div>
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-header"><i class="fas fa-comments"></i> Conversazione</div>
        <div class="card-body">
            <?php $pubblici = $model->messaggiPubblici; ?>
            <?php if (empty($pubblici)): ?>
                <p class="text-muted">Nessun messaggio.</p>
            <?php else: ?>
                <?php foreach ($pubblici as $msg): ?>
                    <div class="border rounded p-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <strong><?= Html::encode($msg->autore ?? '—') ?></strong>
                            <small class="text-muted"><?= $msg->created_at ? Html::encode(date('d/m/Y H:i', strtotime($msg->created_at))) : '' ?></small>
                        </div>
                        <div><?= nl2br(Html::encode($msg->messaggio)) ?></div>
                        <?php if (!empty($msg->allegati)): ?>
                            <div class="mt-2"><?php $renderAllegati($msg->allegati); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($model->isChiuso()): ?>
                <div class="alert alert-info mb-0"><i class="fas fa-lock"></i> La richiesta è chiusa; non è possibile aggiungere altri messaggi.</div>
            <?php else: ?>
                <?php $form = ActiveForm::begin([
                    'action' => ['richiesta-rispondi', 'id' => $model->id],
                    'options' => ['enctype' => 'multipart/form-data'],
                ]); ?>
                    <?= $form->field($messaggio, 'messaggio')->textarea(['rows' => 3, 'placeholder' => 'Scrivi un messaggio...'])->label(false) ?>
                    <div class="form-row align-items-center">
                        <div class="col-md-8">
                            <input type="file" name="files[]" class="form-control-file" multiple>
                        </div>
                        <div class="col-md-4 text-right">
                            <?= Html::submitButton('<i class="fas fa-paper-plane"></i> Invia', ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
