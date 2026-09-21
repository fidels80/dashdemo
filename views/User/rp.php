<?php

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \app\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = 'Recupera Password';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-request-password-reset" style="min-height: 70vh; display: flex; flex-direction: column; justify-content: center; align-items: center;">

    <div style="margin-bottom: 30px;">
        <img src="/uploads/login_logo.png" alt="Logo Planorys" style="max-width: 350px; height: auto; opacity: 0.9;">
    </div>

    <div class="card shadow-lg" style="width: 100%; max-width: 450px; border-radius: 16px; border-top: 4px solid var(--primary-color, #002c48); text-align: left;">

        <div class="card-header text-center border-0 pt-4 pb-0">
            <h3 class="font-weight-bold" style="color: var(--primary-color, #002c48);">
                <i class="fas fa-unlock-alt mr-2"></i><?= Html::encode($this->title) ?>
            </h3>
        </div>

        <div class="card-body p-4">
            <p class="login-box-msg text-muted text-center mb-4">
                Inserisci l'indirizzo email dell'utente.<br>
                Ti invieremo le istruzioni per il reset della password.
            </p>

            <?php $form = ActiveForm::begin([
                'action' => ['user/rp'], // L'azione corretta
                'id' => 'request-password-reset-form'
            ]); ?>

            <?= $form->field($model, 'email', [
                'template' => '
                        <div class="input-group mb-3">
                            {input}
                            <div class="input-group-append">
                                <div class="input-group-text" style="border-radius: 0 20px 20px 0;">
                                    <span class="fas fa-envelope text-muted"></span>
                                </div>
                            </div>
                        </div>
                        {error}',
            ])->textInput([
                'placeholder' => 'es. mario.rossi@email.it',
                'autofocus' => true,
                'style' => 'border-radius: 20px 0 0 20px;'
            ])->label(false) ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-paper-plane mr-2"></i> Invia Richiesta', [
                    'class' => 'btn btn-primary btn-block pill py-2 shadow-sm',
                    'style' => 'background-color: var(--primary-color, #002c48); border: none; font-size: 1.1rem;'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <div class="text-center mt-3">
                <?= Html::a('<i class="fas fa-arrow-left mr-1"></i> Torna al Login', ['site/login'], ['class' => 'text-muted']) ?>
            </div>

        </div>
    </div>
</div>