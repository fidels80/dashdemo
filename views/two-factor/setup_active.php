<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Autenticazione a Due Fattori';
$this->params['breadcrumbs'] = '';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-2"></i> <?= Yii::$app->session->getFlash('success') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <?= Yii::$app->session->getFlash('error') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-shield-alt mr-2"></i>2FA Attiva
                    </h4>
                </div>
                <div class="card-body text-center">

                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>

                    <h5>L'autenticazione a due fattori e' attiva</h5>
                    <p class="text-muted">
                        Ad ogni accesso dovrai inserire il codice generato dall'app di autenticazione
                        sul tuo telefono, dopo aver inserito email e password.
                    </p>

                    <?php if ($model->two_factor_verified_at): ?>
                        <p class="text-muted" style="font-size: 0.85rem;">
                            <i class="fas fa-calendar mr-1"></i>
                            Attivata il <?= date('d/m/Y H:i', $model->two_factor_verified_at) ?>
                        </p>
                    <?php endif; ?>

                    <hr>

                    <?php if (isset($trustedCount) && $trustedCount > 0): ?>
                        <h6 class="mb-3">
                            <i class="fas fa-laptop mr-1"></i> Dispositivi fidati
                        </h6>
                        <p class="text-muted" style="font-size: 0.85rem;">
                            Hai <strong><?= $trustedCount ?></strong> dispositivo<?= $trustedCount > 1 ? 'i' : '' ?> fidato<?= $trustedCount > 1 ? 'i' : '' ?>
                            che non richiede il codice 2FA. Revocandoli, il codice sara'
                            richiesto nuovamente su ogni dispositivo.
                        </p>
                        <form method="post" action="<?= Url::to(['two-factor/revoke-trusted-devices']) ?>" class="mb-4">
                            <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                            <button type="submit" class="btn btn-warning px-4"
                                    style="border-radius: 20px;"
                                    onclick="return confirm('Vuoi revocare tutti i dispositivi fidati? Da ora dovrai inserire il codice 2FA su ogni dispositivo.')">
                                <i class="fas fa-user-shield mr-1"></i> Revoca tutti i dispositivi fidati
                            </button>
                        </form>
                        <hr>
                    <?php endif; ?>

                    <h6 class="text-danger mb-3">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Disattiva 2FA
                    </h6>
                    <p class="text-muted" style="font-size: 0.85rem;">
                        Per disattivare l'autenticazione a due fattori, inserisci la tua password corrente.
                    </p>

                    <form method="post" action="<?= Url::to(['two-factor/disable']) ?>" class="form-inline justify-content-center">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <div class="input-group" style="max-width: 350px;">
                            <input type="password" name="password" class="form-control"
                                   placeholder="Password corrente" required
                                   style="border-radius: 20px 0 0 20px;">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-danger px-4"
                                        style="border-radius: 0 20px 20px 0;"
                                        onclick="return confirm('Sei sicuro di voler disattivare la 2FA?')">
                                    <i class="fas fa-times mr-1"></i> Disattiva
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
