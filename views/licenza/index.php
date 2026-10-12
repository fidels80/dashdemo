<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var array $summary */
/* @var string $serverUrl */
/* @var string $appVersion */

$this->title = 'Licenza e aggiornamenti';
$this->params['breadcrumbs'][] = $this->title;

$badge = function ($text, $type = 'secondary') {
    return Html::tag('span', Html::encode($text), ['class' => 'badge badge-' . $type]);
};

$statusType = [
    'active' => 'success',
    'error' => 'danger',
    'expired' => 'warning',
    'revoked' => 'danger',
];
?>
<div class="licenza-index">

    <div class="row">
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-key mr-1"></i> Stato licenza</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-2">
                        <tr><th style="width:40%">Server</th><td class="text-break"><?= Html::encode($serverUrl) ?></td></tr>
                        <tr><th>Versione app</th><td><?= Html::encode($appVersion) ?></td></tr>
                        <tr><th>Chiave</th><td><code><?= Html::encode($summary['license_key'] ?: '— non configurata —') ?></code></td></tr>
                        <tr><th>Machine ID</th><td><code><?= Html::encode($summary['machine_id'] ?: '—') ?></code></td></tr>
                        <tr><th>Prodotto</th><td><?= Html::encode($summary['product'] ?: '—') ?></td></tr>
                        <tr><th>Tier</th><td><?= Html::encode($summary['tier'] ?: '—') ?></td></tr>
                        <tr><th>Stato</th><td><?= $badge($summary['status'] ?: 'non attiva', $statusType[$summary['status']] ?? 'secondary') ?></td></tr>
                        <tr>
                            <th>Scadenza</th>
                            <td>
                                <?= Html::encode($summary['expires_at'] ?: 'permanente') ?>
                                <?php if ($summary['days_left'] !== null): ?>
                                    <?= $badge($summary['days_left'] >= 0 ? $summary['days_left'] . ' giorni' : 'scaduta', $summary['days_left'] >= 0 ? 'info' : 'danger') ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr><th>Ultimo controllo OK</th><td><?= Html::encode($summary['last_check_ok'] ?: 'mai') ?></td></tr>
                        <tr><th>Blocco moduli</th><td><?= $badge($summary['enforced'] ? 'attivo' : 'disattivo', $summary['enforced'] ? 'warning' : 'secondary') ?></td></tr>
                    </table>

                    <?php if (!empty($summary['last_error'])): ?>
                        <div class="alert alert-warning py-2 mb-0"><i class="fas fa-exclamation-triangle mr-1"></i>
                            <?= Html::encode($summary['last_error']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-puzzle-piece mr-1"></i> Moduli abilitati</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($summary['features'])): ?>
                        <p class="text-muted mb-0">Nessun modulo specificato dalla licenza (periodo di tolleranza: tutti i moduli accessibili).</p>
                    <?php else: ?>
                        <?php foreach ($summary['features'] as $f): ?>
                            <span class="badge badge-success mr-1 mb-1"><?= Html::encode($f) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-sync-alt mr-1"></i> Aggiornamenti</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($summary['update_version'])): ?>
                        <div class="alert alert-success py-2">
                            <i class="fas fa-download mr-1"></i> Aggiornamento disponibile: <strong>v<?= Html::encode($summary['update_version']) ?></strong>
                            <?= Html::beginForm(['download'], 'post', ['class' => 'd-inline']) ?>
                                <?= Html::hiddenInput('version', $summary['update_version']) ?>
                                <?= Html::submitButton('<i class="fas fa-download"></i> Scarica qui', ['class' => 'btn btn-sm btn-success ml-2']) ?>
                            <?= Html::endForm() ?>
                        </div>
                        <p class="text-muted small mb-2">Il pacchetto viene salvato in <code>runtime/updates/</code> e applicato manualmente.</p>
                    <?php else: ?>
                        <p class="text-muted">Nessun aggiornamento rilevato.</p>
                    <?php endif; ?>

                    <?= Html::beginForm(['check-update'], 'post') ?>
                        <?= Html::submitButton('<i class="fas fa-search"></i> Controlla aggiornamenti', ['class' => 'btn btn-sm btn-outline-primary']) ?>
                    <?= Html::endForm() ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit mr-1"></i> Attiva / aggiorna licenza</h3>
        </div>
        <div class="card-body">
            <?= Html::beginForm(['activate'], 'post', ['class' => 'form-inline']) ?>
                <div class="form-group mr-2 mb-2">
                    <?= Html::textInput('license_key', $summary['license_key'], [
                        'class' => 'form-control',
                        'placeholder' => 'XXXX-XXXX-XXXX-XXXX',
                        'style' => 'min-width:280px;font-family:monospace;',
                    ]) ?>
                </div>
                <?= Html::submitButton('<i class="fas fa-key"></i> Attiva', ['class' => 'btn btn-primary mr-2 mb-2']) ?>
            <?= Html::endForm() ?>

            <?= Html::beginForm(['heartbeat'], 'post', ['class' => 'd-inline']) ?>
                <?= Html::submitButton('<i class="fas fa-heartbeat"></i> Aggiorna ora (heartbeat)', ['class' => 'btn btn-outline-secondary mb-2']) ?>
            <?= Html::endForm() ?>
        </div>
    </div>

</div>
