<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $gruppi array */

$this->title = 'Riepilogo controllo documenti fornitore';
$this->params['breadcrumbs'][] = ['label' => 'Controllo documenti fornitore', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Riscontro', 'url' => ['controlla']];
$this->params['breadcrumbs'][] = 'Riepilogo';

$fmt = function ($v) { return number_format((float) $v, 2, ',', '.'); };
?>
<div class="dcf-riepilogo">

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
        <div>
            <h5 class="mb-0"><i class="fas fa-list-check"></i> Passo 3 di 3 — Riepilogo e segnalazioni</h5>
            <div class="text-muted small">
                Per ogni fornitore puoi inviare la segnalazione delle quantita' mancanti oppure chiudere il controllo.
            </div>
        </div>
        <div>
            <a href="<?= Url::to(['controlla']) ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Torna al riscontro
            </a>
            <form method="post" action="<?= Url::to(['chiudi']) ?>" class="d-inline"
                  onsubmit="return confirm('Contrassegnare i documenti come controllati e chiudere il controllo corrente?');">
                <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                <button type="submit" class="btn btn-outline-primary btn-sm"><i class="fas fa-check"></i> Chiudi e contrassegna come controllati</button>
            </form>
        </div>
    </div>

    <?php if (empty($gruppi)): ?>
        <div class="alert alert-info">Nessun documento da riepilogare.</div>
    <?php endif; ?>

    <?php foreach ($gruppi as $gruppo): ?>
        <?php $fornitore = $gruppo['fornitore']; ?>
        <div class="card shadow-sm mb-3">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <i class="fas fa-industry text-muted"></i>
                    <strong><?= Html::encode($fornitore ? $fornitore->ragione_sociale : 'Fornitore non disponibile') ?></strong>
                    <?php if ($fornitore && !empty($fornitore->email)): ?>
                        <span class="text-muted small ml-2"><?= Html::encode($fornitore->email) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($gruppo['differenze'] > 0): ?>
                        <span class="badge badge-danger"><?= (int) $gruppo['differenze'] ?> righe con differenza</span>
                    <?php elseif ($gruppo['completo']): ?>
                        <span class="badge badge-success">Nessuna differenza</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Incompleto</span>
                    <?php endif; ?>
                    <?php if ($gruppo['email_inviata'] && $gruppo['differenze'] > 0): ?>
                        <span class="badge badge-info">Segnalazione inviata</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (!$gruppo['completo']): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Controllo <strong>parziale</strong>: <?= (int) $gruppo['non_riscontrate'] ?> righe non ancora riscontrate.
                        Le righe non riscontrate non verranno incluse nella segnalazione.
                        <a href="<?= Url::to(['controlla']) ?>">Torna al riscontro</a> per completarle,
                        oppure invia/chiudi da qui.
                    </div>
                <?php endif; ?>

                <table class="table table-sm table-bordered mb-3">
                    <thead>
                    <tr>
                        <th style="width:18%">Documento</th>
                        <th style="width:12%">Codice</th>
                        <th>Descrizione</th>
                        <th class="text-right" style="width:11%">Q.ta doc.</th>
                        <th class="text-right" style="width:12%">Q.ta risc.</th>
                        <th class="text-right" style="width:11%">Differenza</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($gruppo['documenti'] as $blocco): ?>
                        <?php foreach ($blocco['righe_differenza'] as $d): ?>
                            <tr>
                                <td><?= Html::encode($blocco['model']->etichetta) ?></td>
                                <td><?= Html::encode($d['riga']->codice_articolo) ?></td>
                                <td><?= Html::encode($d['riga']->descrizione) ?></td>
                                <td class="text-right"><?= $fmt($d['qta_documento']) ?></td>
                                <td class="text-right"><?= $fmt($d['qta_riscontrata']) ?></td>
                                <td class="text-right font-weight-bold <?= $d['differenza'] < 0 ? 'text-danger' : 'text-success' ?>">
                                    <?= $fmt($d['differenza']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <?php if ($gruppo['differenze'] === 0): ?>
                        <tr><td colspan="6" class="text-muted">Nessuna differenza rilevata.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($gruppo['differenze'] > 0): ?>
                    <form method="post" action="<?= Url::to(['invia-email']) ?>">
                        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
                        <input type="hidden" name="id_anagrafica" value="<?= (int) ($fornitore ? $fornitore->id : 0) ?>">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="small mb-1">Destinatari (separati da virgola)</label>
                                <input type="text" name="destinatari" class="form-control"
                                       value="<?= Html::encode($fornitore && !empty($fornitore->email) ? $fornitore->email : '') ?>"
                                       placeholder="email@fornitore.it">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="small mb-1">Oggetto</label>
                                <input type="text" name="oggetto" class="form-control"
                                       value="Segnalazione quantita' mancanti - <?= Html::encode($gruppo['documenti'][0]['model']->etichetta ?? '') ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="small mb-1">Messaggio (opzionale)</label>
                            <textarea name="messaggio" rows="2" class="form-control"
                                      placeholder="Eventuali annotazioni per il fornitore..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-paper-plane"></i>
                            <?= $gruppo['email_inviata'] ? 'Reinvia segnalazione' : 'Invia segnalazione' ?>
                        </button>
                        <?php if ($gruppo['email_inviata']): ?>
                            <span class="text-muted small ml-2">
                                Ultimo invio:
                                <?php
                                $c0 = $gruppo['documenti'][0]['controllo'];
                                echo $c0->email_inviata_at ? date('d/m/Y H:i', strtotime($c0->email_inviata_at)) : '';
                                ?>
                            </span>
                        <?php endif; ?>
                    </form>
                <?php else: ?>
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle"></i>
                        Nessuna differenza rilevata: puoi usare <strong>Chiudi e contrassegna come controllati</strong>.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
