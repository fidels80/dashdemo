<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;

$this->title = 'Login';
?>

<div class="container mt-3" style="max-width: 450px;">
    <?php if (Yii::$app->session->hasFlash('inviato')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm text-center" style="border-radius: 10px;" role="alert">
            <i class="fas fa-paper-plane mr-2"></i> A breve riceverai una mail con il link del reset.<br>Ricordati di controllare lo spam!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('errore')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm text-center" style="border-radius: 10px;" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> Non sono riuscito a inviare la mail!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('Login fallita')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm text-center" style="border-radius: 10px;" role="alert">
            <i class="fas fa-times-circle mr-2"></i> Email o Password inserite non corrispondono!
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
</div>

<div class="site-login d-flex flex-column justify-content-center align-items-center" style="min-height: 70vh; padding: 20px 0;">

    <div style="margin-bottom: 30px;">
        <img src="/uploads/login_logo.jpg" alt="Logo Ufficio 2000" style="max-width: 350px; height: auto; opacity: 0.9; object-fit: contain;">
    </div>

    <div class="card shadow-lg" style="width: 100%; max-width: 450px; border-radius: 16px; border-top: 4px solid var(--primary-color, #002c48); text-align: left;">

        <div class="card-header text-center border-0 pt-4 pb-0">
            <h3 class="font-weight-bold" style="color: var(--primary-color, #002c48);">
                <i class="fas fa-sign-in-alt mr-2"></i><?= Html::encode($this->title) ?>
            </h3>
        </div>

        <div class="card-body p-4">
            <p class="login-box-msg text-muted text-center mb-4">
                Inserisci le tue credenziali per accedere
            </p>

            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'action' => ['site/login']
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
                'placeholder' => 'Email',
                'autofocus' => true,
                'style' => 'border-radius: 20px 0 0 20px;'
            ])->label(false) ?>

            <?= $form->field($model, 'password', [
                'template' => '
                    <div class="input-group mb-4">
                        {input}
                        <div class="input-group-append">
                            <div class="input-group-text" style="border-radius: 0 20px 20px 0;">
                                <span class="fas fa-lock text-muted"></span>
                            </div>
                        </div>
                    </div>
                    {error}',
            ])->passwordInput([
                'placeholder' => 'Password',
                'style' => 'border-radius: 20px 0 0 20px;'
            ])->label(false) ?>

            <div class="form-group mt-2 mb-4 text-center">
                <?= Html::submitButton('<i class="fas fa-sign-in-alt mr-2"></i> Collegati', [
                    'class' => 'btn btn-primary btn-block pill py-2 shadow-sm',
                    'style' => 'background-color: var(--primary-color, #002c48); border: none; font-size: 1.1rem;'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <div class="d-flex flex-column text-center mt-3">
                <div class="mb-2">
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Hai dimenticato la password?</p>
                    <?= Html::a('Reimposta Password', ['user/rp', 'reset' => 1], ['class' => 'font-weight-bold', 'style' => 'color: var(--primary-color, #002c48);']) ?>
                </div>

                <hr style="width: 50%;">

                <div>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Non hai ancora un account?</p>
                    <?= Html::a('Registrati ora', ['site/login', 'isnew' => true], ['class' => 'font-weight-bold', 'style' => 'color: var(--primary-color, #002c48);']) ?>
                </div>
            </div>

        </div>
    </div>
</div>