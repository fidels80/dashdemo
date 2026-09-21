<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Setup Autenticazione a Due Fattori';
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
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-shield-alt mr-2"></i>Attiva Autenticazione a Due Fattori
                    </h4>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-6 text-center mb-4">
                            <h5 class="mb-3"><i class="fas fa-qrcode mr-2"></i>Scansiona questo codice</h5>

                            <?php
                            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data='
                                . urlencode($qrCodeUrl);
                            ?>
                            <div class="p-3 bg-white rounded shadow-sm d-inline-block">
                                <img src="<?= $qrUrl ?>"
                                     alt="QR Code 2FA"
                                     style="width: 250px; height: 250px;">
                            </div>

                            <p class="text-muted mt-3" style="font-size: 0.85rem;">
                                Usa <strong>Google Authenticator</strong>, <strong>Microsoft Authenticator</strong>
                                o <strong>Authy</strong>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-3"><i class="fas fa-list-ol mr-2"></i>Come fare</h5>
                            <ol class="list-group list-group-flush mb-4">
                                <li class="list-group-item">
                                    <i class="fas fa-mobile-alt text-primary mr-2"></i>
                                    Apri l'app di autenticazione sul tuo telefono
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-camera text-primary mr-2"></i>
                                    Scansiona il QR code a sinistra
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-keyboard text-primary mr-2"></i>
                                    Inserisci il codice a 6 cifre qui sotto per confermare
                                </li>
                            </ol>

                            <div class="alert alert-warning" style="border-radius: 10px;">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Importante:</strong> Salva questo codice in un posto sicuro.
                                <br>
                                <code class="mt-2 d-block p-2 bg-light" style="word-break: break-all; font-size: 0.8rem; border-radius: 6px;">
                                    <?= Html::encode($secret) ?>
                                </code>
                                <small class="d-block mt-1">Se perdi l'accesso all'app, potrai usare questo codice per tornare attivo.</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <form method="post" action="<?= Url::to(['two-factor/enable']) ?>" class="form-inline justify-content-center">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" name="code" class="form-control form-control-lg text-center"
                                   placeholder="000000" maxlength="6" required
                                   style="border-radius: 20px 0 0 20px; font-size: 1.3rem; letter-spacing: 5px; font-weight: bold;"
                                   inputmode="numeric" pattern="[0-9]*">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-success px-4"
                                        style="border-radius: 0 20px 20px 0;">
                                    <i class="fas fa-check mr-1"></i> Attiva
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
