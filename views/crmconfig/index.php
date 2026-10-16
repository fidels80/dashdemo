<?php

use yii\helpers\Html;
use app\models\MgCrmTipo;
use app\models\MgCrmStato;
use app\models\MgCrmPriorita;

/* @var $this yii\web\View */
/* @var $tipi app\models\MgCrmTipo[] */
/* @var $stati app\models\MgCrmStato[] */
/* @var $priorita app\models\MgCrmPriorita[] */
/* @var $edit yii\db\ActiveRecord|null */

$this->title = 'Configurazione CRM';
$this->params['breadcrumbs'][] = ['label' => 'Ticket CRM', 'url' => ['crmticket/index']];
$this->params['breadcrumbs'][] = $this->title;

$editTipo = $edit instanceof MgCrmTipo ? $edit : null;
$editStato = $edit instanceof MgCrmStato ? $edit : null;
$editPriorita = $edit instanceof MgCrmPriorita ? $edit : null;
?>
<div class="crmconfig-index">
    <p class="text-muted">
        <i class="fas fa-info-circle"></i>
        Le voci non vengono eliminate (sono usate dai ticket): si possono disattivare e riattivare.
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-list"></i> Tipi richiesta</div>
                <div class="card-body">
                    <table class="table table-sm table-striped">
                        <thead><tr><th>Ord.</th><th>Codice</th><th>Descrizione</th><th>Attivo</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($tipi as $t): ?>
                            <tr>
                                <td><?= (int) $t->ordine ?></td>
                                <td><?= Html::encode($t->codice) ?></td>
                                <td><i class="<?= Html::encode($t->icona) ?>"></i> <?= Html::encode($t->descrizione) ?></td>
                                <td><?= $t->attivo ? 'Sì' : 'No' ?></td>
                                <td class="text-nowrap">
                                    <?= Html::a('<i class="fas fa-pen"></i>', ['index', 'entita' => 'tipo', 'edit' => $t->id], ['class' => 'btn btn-xs btn-warning']) ?>
                                    <?= Html::a($t->attivo ? '<i class="fas fa-toggle-on"></i>' : '<i class="fas fa-toggle-off"></i>', ['toggle', 'entita' => 'tipo', 'id' => $t->id], ['class' => 'btn btn-xs btn-outline-secondary', 'data' => ['method' => 'post']]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?= Html::beginForm(['salva'], 'post') ?>
                        <?= Html::hiddenInput('entita', 'tipo') ?>
                        <?= $editTipo ? Html::hiddenInput('id', $editTipo->id) : '' ?>
                        <div class="form-row">
                            <div class="col-3"><?= Html::textInput('codice', $editTipo->codice ?? '', ['class' => 'form-control', 'placeholder' => 'codice']) ?></div>
                            <div class="col-4"><?= Html::textInput('descrizione', $editTipo->descrizione ?? '', ['class' => 'form-control', 'placeholder' => 'descrizione']) ?></div>
                            <div class="col-3"><?= Html::textInput('icona', $editTipo->icona ?? '', ['class' => 'form-control', 'placeholder' => 'es. fas fa-tools']) ?></div>
                            <div class="col-2"><?= Html::textInput('ordine', $editTipo->ordine ?? 0, ['class' => 'form-control', 'type' => 'number']) ?></div>
                        </div>
                        <div class="mt-2">
                            <?= Html::submitButton($editTipo ? 'Aggiorna tipo' : 'Aggiungi tipo', ['class' => 'btn btn-sm btn-success']) ?>
                            <?php if ($editTipo): ?><?= Html::a('Annulla', ['index'], ['class' => 'btn btn-sm btn-secondary']) ?><?php endif; ?>
                        </div>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-flag"></i> Priorità</div>
                <div class="card-body">
                    <table class="table table-sm table-striped">
                        <thead><tr><th>Ord.</th><th>Codice</th><th>Descrizione</th><th>Colore</th><th>Attivo</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($priorita as $p): ?>
                            <tr>
                                <td><?= (int) $p->ordine ?></td>
                                <td><?= Html::encode($p->codice) ?></td>
                                <td><span class="badge badge-<?= Html::encode($p->colore) ?>"><?= Html::encode($p->descrizione) ?></span></td>
                                <td><?= Html::encode($p->colore) ?></td>
                                <td><?= $p->attivo ? 'Sì' : 'No' ?></td>
                                <td class="text-nowrap">
                                    <?= Html::a('<i class="fas fa-pen"></i>', ['index', 'entita' => 'priorita', 'edit' => $p->id], ['class' => 'btn btn-xs btn-warning']) ?>
                                    <?= Html::a($p->attivo ? '<i class="fas fa-toggle-on"></i>' : '<i class="fas fa-toggle-off"></i>', ['toggle', 'entita' => 'priorita', 'id' => $p->id], ['class' => 'btn btn-xs btn-outline-secondary', 'data' => ['method' => 'post']]) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?= Html::beginForm(['salva'], 'post') ?>
                        <?= Html::hiddenInput('entita', 'priorita') ?>
                        <?= $editPriorita ? Html::hiddenInput('id', $editPriorita->id) : '' ?>
                        <div class="form-row">
                            <div class="col-4"><?= Html::textInput('codice', $editPriorita->codice ?? '', ['class' => 'form-control', 'placeholder' => 'codice']) ?></div>
                            <div class="col-4"><?= Html::textInput('descrizione', $editPriorita->descrizione ?? '', ['class' => 'form-control', 'placeholder' => 'descrizione']) ?></div>
                            <div class="col-2"><?= Html::textInput('colore', $editPriorita->colore ?? '', ['class' => 'form-control', 'placeholder' => 'colore']) ?></div>
                            <div class="col-2"><?= Html::textInput('ordine', $editPriorita->ordine ?? 0, ['class' => 'form-control', 'type' => 'number']) ?></div>
                        </div>
                        <div class="mt-2">
                            <?= Html::submitButton($editPriorita ? 'Aggiorna priorità' : 'Aggiungi priorità', ['class' => 'btn btn-sm btn-success']) ?>
                            <?php if ($editPriorita): ?><?= Html::a('Annulla', ['index'], ['class' => 'btn btn-sm btn-secondary']) ?><?php endif; ?>
                        </div>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fas fa-stream"></i> Stati del workflow</div>
        <div class="card-body">
            <table class="table table-sm table-striped">
                <thead><tr><th>Ord.</th><th>Codice</th><th>Descrizione</th><th>Colore</th><th>Chiude</th><th>Attivo</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($stati as $s): ?>
                    <tr>
                        <td><?= (int) $s->ordine ?></td>
                        <td><?= Html::encode($s->codice) ?></td>
                        <td><span class="badge badge-<?= Html::encode($s->colore) ?>"><?= Html::encode($s->descrizione) ?></span></td>
                        <td><?= Html::encode($s->colore) ?></td>
                        <td><?= $s->is_chiuso ? 'Sì' : 'No' ?></td>
                        <td><?= $s->attivo ? 'Sì' : 'No' ?></td>
                        <td class="text-nowrap">
                            <?= Html::a('<i class="fas fa-pen"></i>', ['index', 'entita' => 'stato', 'edit' => $s->id], ['class' => 'btn btn-xs btn-warning']) ?>
                            <?= Html::a($s->attivo ? '<i class="fas fa-toggle-on"></i>' : '<i class="fas fa-toggle-off"></i>', ['toggle', 'entita' => 'stato', 'id' => $s->id], ['class' => 'btn btn-xs btn-outline-secondary', 'data' => ['method' => 'post']]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?= Html::beginForm(['salva'], 'post') ?>
                <?= Html::hiddenInput('entita', 'stato') ?>
                <?= $editStato ? Html::hiddenInput('id', $editStato->id) : '' ?>
                <div class="form-row align-items-center">
                    <div class="col-2"><?= Html::textInput('codice', $editStato->codice ?? '', ['class' => 'form-control', 'placeholder' => 'codice']) ?></div>
                    <div class="col-3"><?= Html::textInput('descrizione', $editStato->descrizione ?? '', ['class' => 'form-control', 'placeholder' => 'descrizione']) ?></div>
                    <div class="col-2"><?= Html::textInput('colore', $editStato->colore ?? '', ['class' => 'form-control', 'placeholder' => 'colore']) ?></div>
                    <div class="col-1"><?= Html::textInput('ordine', $editStato->ordine ?? 0, ['class' => 'form-control', 'type' => 'number']) ?></div>
                    <div class="col-2">
                        <label class="mb-0">
                            <?= Html::checkbox('is_chiuso', $editStato ? (bool) $editStato->is_chiuso : false) ?> Chiude
                        </label>
                    </div>
                    <div class="col-2">
                        <?= Html::submitButton($editStato ? 'Aggiorna stato' : 'Aggiungi stato', ['class' => 'btn btn-sm btn-success btn-block']) ?>
                    </div>
                </div>
                <?php if ($editStato): ?><div class="mt-2"><?= Html::a('Annulla', ['index'], ['class' => 'btn btn-sm btn-secondary']) ?></div><?php endif; ?>
            <?= Html::endForm() ?>
        </div>
    </div>
</div>
