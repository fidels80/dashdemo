<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\models\MgCrmAllegato;

/* @var $this yii\web\View */
/* @var $model app\models\MgCrmTicket */
/* @var $stati array */
/* @var $operatori array */
/* @var $messaggio app\models\MgCrmTicketMessaggio */
/* @var $articoli array */
/* @var $sottocommesse array */

$this->title = $model->etichetta . ' - ' . $model->oggetto;
$this->params['breadcrumbs'][] = ['label' => 'Ticket CRM', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->etichetta;

$chiuso = $model->isChiuso();
$allegati = MgCrmAllegato::perTicket($model->id);

$renderAllegati = function ($lista) {
    foreach ($lista as $a) {
        echo '<div class="d-flex align-items-center border rounded p-1 mb-1">';
        echo '<i class="' . Html::encode($a->icona) . ' mr-2"></i>';
        echo Html::a(Html::encode($a->nome_originale), ['allegato', 'id' => $a->id], ['target' => '_blank']);
        echo ' <small class="text-muted ml-1">(' . Html::encode($a->dimensioneLabel) . ')</small>';
        echo '<span class="ml-auto no-export">';
        echo Html::a('<i class="fas fa-download"></i>', ['allegato', 'id' => $a->id, 'download' => 1], ['class' => 'btn btn-sm btn-outline-secondary mr-1', 'title' => 'Scarica']);
        echo Html::a('<i class="fas fa-trash"></i>', ['elimina-allegato', 'id' => $a->id], [
            'class' => 'btn btn-sm btn-outline-danger',
            'title' => 'Elimina',
            'data' => ['confirm' => 'Eliminare l\'allegato?', 'method' => 'post'],
        ]);
        echo '</span></div>';
    }
};
?>
<div class="crmticket-view">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h4 class="mb-0">
            <?= Html::encode($model->etichetta) ?>
            <span class="badge badge-<?= Html::encode($model->statoColore) ?>"><?= Html::encode($model->stato->descrizione ?? '') ?></span>
            <span class="badge badge-<?= Html::encode($model->prioritaColore) ?>"><?= Html::encode($model->priorita->descrizione ?? '—') ?></span>
            <?php if ($model->evaso): ?><span class="badge badge-dark">Evasi in rapportino</span><?php endif; ?>
        </h4>
        <div>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
            <?= Html::a('<i class="fas fa-list"></i> Elenco', ['index'], ['class' => 'btn btn-secondary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Eliminare questo ticket?', 'method' => 'post'],
            ]) ?>
        </div>
    </div>

    <?php if ($chiuso && !$model->faq): ?>
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-lightbulb fa-lg mr-2"></i>
            <div class="mr-auto">Questo ticket è chiuso: puoi pubblicare una <strong>FAQ</strong> per riusare la soluzione.</div>
            <?= Html::a('<i class="fas fa-question-circle"></i> Crea FAQ', ['crea-faq', 'id' => $model->id], [
                'class' => 'btn btn-info',
                'data' => ['method' => 'post'],
            ]) ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-info-circle"></i> Dettagli richiesta</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">Tipo</dt><dd class="col-sm-9"><?= Html::encode($model->tipo->descrizione ?? '') ?></dd>
                        <dt class="col-sm-3">Cliente</dt><dd class="col-sm-9"><?= Html::encode($model->anagrafica->ragione_sociale ?? '—') ?></dd>
                        <dt class="col-sm-3">Aperto da</dt><dd class="col-sm-9"><?= Html::encode($model->utente->username ?? '—') ?></dd>
                        <dt class="col-sm-3">Articolo</dt><dd class="col-sm-9"><?= Html::encode($model->articolo ? $model->articolo->codice . ' - ' . $model->articolo->descrizione : '—') ?></dd>
                        <dt class="col-sm-3">Matricola</dt><dd class="col-sm-9"><?= Html::encode($model->matricola->etichetta ?? '—') ?></dd>
                        <dt class="col-sm-3">Documento</dt><dd class="col-sm-9"><?= Html::encode($model->documento->etichetta ?? '—') ?></dd>
                        <dt class="col-sm-3">Apertura</dt><dd class="col-sm-9"><?= $model->data_apertura ? Html::encode(date('d/m/Y H:i', strtotime($model->data_apertura))) : '—' ?></dd>
                        <dt class="col-sm-3">Scadenza</dt><dd class="col-sm-9"><?= $model->data_scadenza ? Html::encode(date('d/m/Y', strtotime($model->data_scadenza))) : '—' ?></dd>
                        <dt class="col-sm-3">Presa in carico</dt><dd class="col-sm-9"><?= $model->data_presa_carico ? Html::encode(date('d/m/Y H:i', strtotime($model->data_presa_carico))) : '—' ?></dd>
                        <dt class="col-sm-3">Chiusura</dt><dd class="col-sm-9"><?= $model->data_chiusura ? Html::encode(date('d/m/Y H:i', strtotime($model->data_chiusura))) : '—' ?></dd>
                        <dt class="col-sm-3">Tempo impiegato</dt><dd class="col-sm-9"><?= $model->tempo_impiegato_ore !== null ? number_format((float) $model->tempo_impiegato_ore, 2, ',', '.') . ' ore' : '—' ?></dd>
                        <dt class="col-sm-3">Descrizione</dt><dd class="col-sm-9"><?= nl2br(Html::encode($model->descrizione ?? '')) ?></dd>
                    </dl>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-paperclip"></i> Allegati del ticket</div>
                <div class="card-body">
                    <?php if (empty($allegati)): ?>
                        <p class="text-muted">Nessun allegato.</p>
                    <?php else: ?>
                        <?php $renderAllegati($allegati); ?>
                    <?php endif; ?>

                    <?php $f = ActiveForm::begin(['action' => ['allega', 'id' => $model->id], 'options' => ['enctype' => 'multipart/form-data', 'class' => 'mt-2']]); ?>
                        <div class="input-group">
                            <input type="file" name="files[]" class="form-control-file" multiple>
                            <div class="input-group-append">
                                <?= Html::submitButton('<i class="fas fa-upload"></i> Carica', ['class' => 'btn btn-outline-primary']) ?>
                            </div>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-comments"></i> Conversazione</div>
                <div class="card-body">
                    <?php if (empty($model->messaggi)): ?>
                        <p class="text-muted">Nessun messaggio.</p>
                    <?php else: ?>
                        <?php foreach ($model->messaggi as $msg): ?>
                            <div class="border rounded p-2 mb-2 <?= $msg->is_nota_interna ? 'bg-light-warning' : '' ?> <?= $msg->is_soluzione ? 'border-success' : '' ?>">
                                <div class="d-flex justify-content-between">
                                    <strong><?= Html::encode($msg->autore ?? '—') ?></strong>
                                    <small class="text-muted">
                                        <?= $msg->created_at ? Html::encode(date('d/m/Y H:i', strtotime($msg->created_at))) : '' ?>
                                        <?php if ($msg->is_nota_interna): ?><span class="badge badge-dark">Nota interna</span><?php endif; ?>
                                        <?php if ($msg->is_soluzione): ?><span class="badge badge-success"><i class="fas fa-check"></i> Soluzione</span><?php endif; ?>
                                    </small>
                                </div>
                                <div><?= nl2br(Html::encode($msg->messaggio)) ?></div>
                                <?php if (!empty($msg->allegati)): ?>
                                    <div class="mt-2"><?php $renderAllegati($msg->allegati); ?></div>
                                <?php endif; ?>
                                <?php if (!$msg->is_soluzione): ?>
                                    <div class="mt-2 no-export">
                                        <?= Html::a('<i class="fas fa-check-circle"></i> Segna come soluzione', ['soluzione', 'id' => $model->id], [
                                            'class' => 'btn btn-sm btn-outline-success',
                                            'data' => ['method' => 'post', 'params' => ['messaggio_id' => $msg->id]],
                                        ]) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php $form = ActiveForm::begin([
                        'action' => ['rispondi', 'id' => $model->id],
                        'options' => ['enctype' => 'multipart/form-data'],
                    ]); ?>
                        <?= $form->field($messaggio, 'messaggio')->textarea(['rows' => 3, 'placeholder' => 'Scrivi una risposta...'])->label(false) ?>
                        <div class="form-row align-items-center">
                            <div class="col-md-4">
                                <?= $form->field($messaggio, 'is_nota_interna')->checkbox(['label' => 'Nota interna'])->label(false) ?>
                                <?= $form->field($messaggio, 'is_soluzione')->checkbox(['label' => 'Questa è la soluzione'])->label(false) ?>
                            </div>
                            <div class="col-md-5">
                                <input type="file" name="files[]" class="form-control-file" multiple>
                            </div>
                            <div class="col-md-3 text-right">
                                <?= Html::submitButton('<i class="fas fa-paper-plane"></i> Invia', ['class' => 'btn btn-primary']) ?>
                            </div>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-user-check"></i> Assegnazione</div>
                <div class="card-body">
                    <p>Attualmente: <strong><?= Html::encode($model->assegnato->username ?? 'non assegnato') ?></strong></p>
                    <?php $form = ActiveForm::begin(['action' => ['assegna', 'id' => $model->id]]); ?>
                        <?= Html::dropDownList('assegnato_a', $model->assegnato_a, $operatori, ['class' => 'form-control mb-2', 'prompt' => 'Seleziona operatore...']) ?>
                        <?= Html::submitButton('<i class="fas fa-check"></i> Assegna', ['class' => 'btn btn-primary btn-sm']) ?>
                    <?php ActiveForm::end(); ?>
                    <?php if ((int) $model->assegnato_a !== (int) Yii::$app->user->id): ?>
                        <?= Html::a('<i class="fas fa-hand-paper"></i> Prendi in carico', ['prendi', 'id' => $model->id], [
                            'class' => 'btn btn-outline-primary btn-sm mt-2',
                            'data' => ['method' => 'post'],
                        ]) ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-exchange-alt"></i> Stato</div>
                <div class="card-body">
                    <?php if ($chiuso): ?>
                        <p class="mb-0">Ticket <strong><?= Html::encode($model->stato->descrizione ?? '') ?></strong>.</p>
                    <?php else: ?>
                        <?php $form = ActiveForm::begin(['action' => ['cambia-stato', 'id' => $model->id]]); ?>
                            <?= Html::dropDownList('id_stato', $model->id_stato, $stati, ['class' => 'form-control mb-2']) ?>
                            <?= Html::submitButton('<i class="fas fa-sync"></i> Aggiorna stato', ['class' => 'btn btn-primary btn-sm']) ?>
                        <?php ActiveForm::end(); ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-check-double"></i> Chiusura</div>
                <div class="card-body">
                    <?php if ($chiuso): ?>
                        <p class="mb-1">Tempo impiegato: <strong><?= $model->tempo_impiegato_ore !== null ? number_format((float) $model->tempo_impiegato_ore, 2, ',', '.') . ' ore' : '—' ?></strong></p>
                        <p class="mb-1">Soluzione: <?= nl2br(Html::encode($model->soluzione ?? '—')) ?></p>
                        <?php if (!$model->faq): ?>
                            <?= Html::a('<i class="fas fa-question-circle"></i> Crea FAQ', ['crea-faq', 'id' => $model->id], [
                                'class' => 'btn btn-sm btn-info', 'data' => ['method' => 'post'],
                            ]) ?>
                        <?php else: ?>
                            <?= Html::a('<i class="fas fa-question-circle"></i> Vedi FAQ', ['crmfaq/view', 'id' => $model->faq->id], ['class' => 'btn btn-sm btn-outline-info']) ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php $form = ActiveForm::begin(['action' => ['chiudi', 'id' => $model->id]]); ?>
                            <div class="form-group">
                                <label>Tempo impiegato (ore)</label>
                                <?= Html::textInput('tempo_impiegato_ore', $model->tempo_impiegato_ore, ['class' => 'form-control', 'type' => 'number', 'step' => '0.25', 'min' => 0, 'required' => true]) ?>
                            </div>
                            <div class="form-group">
                                <label>Soluzione</label>
                                <?= Html::textarea('soluzione', $model->messaggioSoluzione->messaggio ?? $model->soluzione, ['class' => 'form-control', 'rows' => 3]) ?>
                            </div>
                            <div class="form-group">
                                <label class="mb-0"><?= Html::checkbox('crea_faq', false) ?> Genera anche una FAQ</label>
                            </div>
                            <?= Html::submitButton('<i class="fas fa-lock"></i> Chiudi ticket', ['class' => 'btn btn-success btn-block']) ?>
                        <?php ActiveForm::end(); ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-clock"></i> Evadi in rapportino</div>
                <div class="card-body">
                    <?php if ($model->evaso): ?>
                        <p class="mb-0">Rapportino: <strong><?= Html::encode($model->id_rapportino) ?></strong></p>
                        <?= Html::a('<i class="fas fa-eye"></i> Apri rapportino', ['rapportini/view', 'id' => $model->id_rapportino], ['class' => 'btn btn-sm btn-outline-secondary mt-2']) ?>
                    <?php else: ?>
                        <?php $form = ActiveForm::begin(['action' => ['evadi', 'id' => $model->id]]); ?>
                            <div class="form-group">
                                <label>Articolo</label>
                                <?= Html::dropDownList('id_articolo', $model->id_articolo, $articoli, ['class' => 'form-control', 'prompt' => 'Seleziona articolo...']) ?>
                            </div>
                            <div class="form-group">
                                <label>Sottocommessa</label>
                                <?= Html::dropDownList('commessa', null, $sottocommesse, ['class' => 'form-control', 'prompt' => 'Seleziona...']) ?>
                            </div>
                            <div class="form-group">
                                <label>Tempo (ore)</label>
                                <?= Html::textInput('tempo_impiegato_ore', $model->tempo_impiegato_ore, ['class' => 'form-control', 'type' => 'number', 'step' => '0.25', 'min' => 0]) ?>
                            </div>
                            <div class="form-group">
                                <label>Data</label>
                                <?= Html::textInput('data', date('Y-m-d'), ['class' => 'form-control', 'type' => 'date']) ?>
                            </div>
                            <?= Html::submitButton('<i class="fas fa-clock"></i> Genera rapportino', ['class' => 'btn btn-primary btn-block']) ?>
                        <?php ActiveForm::end(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
