<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TwoFactorForm */

$this->title = 'Verifica 2FA';
?>

<div class="site-login d-flex flex-column justify-content-center align-items-center" style="min-height: 70vh; padding: 20px 0;">

    <div style="margin-bottom: 30px;">
        <img src="/uploads/login_logo.jpg" alt="Logo Ufficio 2000" style="max-width: 350px; height: auto; opacity: 0.9; object-fit: contain;">
    </div>

    <div class="card shadow-lg" style="width: 100%; max-width: 450px; border-radius: 16px; border-top: 4px solid var(--primary-color, #002c48);">

        <div class="card-header text-center border-0 pt-4 pb-0">
            <h3 class="font-weight-bold" style="color: var(--primary-color, #002c48);">
                <i class="fas fa-shield-alt mr-2"></i>Verifica in due passi
            </h3>
        </div>

        <div class="card-body p-4">

            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm text-center" style="border-radius: 10px;">
                    <i class="fas fa-exclamation-triangle mr-2"></i> <?= Yii::$app->session->getFlash('error') ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            <?php endif; ?>

            <p class="text-muted text-center mb-4">
                Apri l'app <strong>Google Authenticator</strong> o <strong>Microsoft Authenticator</strong>
                sul tuo telefono e inserisci il codice a 6 cifre.
            </p>

            <?php $form = ActiveForm::begin([
                'id' => 'two-factor-form',
                'action' => ['two-factor/verify'],
            ]); ?>

            <?= $form->field($model, 'code', [
                'template' => '
                    <div class="input-group mb-3">
                        {input}
                        <div class="input-group-append">
                            <div class="input-group-text" style="border-radius: 0 20px 20px 0;">
                                <span class="fas fa-key text-muted"></span>
                            </div>
                        </div>
                    </div>
                    {error}',
            ])->textInput([
                'placeholder' => '000000',
                'maxlength' => 6,
                'inputmode' => 'numeric',
                'pattern' => '[0-9]*',
                'autofocus' => true,
                'style' => 'border-radius: 20px 0 0 20px; text-align: center; font-size: 1.5rem; letter-spacing: 8px; font-weight: bold;',
            ])->label(false) ?>

            <?= $form->field($model, 'rememberDevice')->checkbox([
                'class' => 'custom-control-input',
            ])->label('Ricordami su questo dispositivo', ['class' => 'custom-control-label']) ?>

            <div class="form-group mt-3 mb-3 text-center">
                <?= Html::submitButton('<i class="fas fa-check-circle mr-2"></i> Verifica', [
                    'class' => 'btn btn-primary btn-block pill py-2 shadow-sm',
                    'style' => 'background-color: var(--primary-color, #002c48); border: none; font-size: 1.1rem;'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <div class="text-center mt-3">
                <?= Html::a('Non riesci ad accedere? Torna al login', ['site/login'], [
                    'class' => 'text-muted',
                    'style' => 'font-size: 0.85rem;'
                ]) ?>
            </div>

        </div>
    </div>
</div>
