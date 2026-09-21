<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model \app\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Registrati';
$this->params['breadcrumbs'][] = $this->title;

// --- Gestione Flash Messages ---
$flashMessages = '';
if (Yii::$app->session->hasFlash('errore')) {
    $flashMessages .= '
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" style="border-radius: 10px;" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i> ' . Yii::$app->session->getFlash('errore') . '
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>';
}
if (Yii::$app->session->hasFlash('success')) {
    $flashMessages .= '
    <div class="alert alert-success alert-dismissible fade show shadow-sm" style="border-radius: 10px;" role="alert">
        <i class="fas fa-check-circle mr-2"></i> ' . Yii::$app->session->getFlash('success') . '
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>';
}

// --- Script per validazione Password (inserito nel modo corretto per Yii2) ---
$js = <<<JS
    $('#password2').focusout(function() {
        var pass = $('#password').val();
        var pass2 = $('#password2').val();
        if (pass != pass2) {
            alert('Le password non sono uguali!');
            $('#password2').val('');
        }
    });

    $('#password').focusout(function() {
        var pass = $('#password').val();
        var strength = 0;

        if (pass.match(/[a-z]+/)) strength += 0;
        if (pass.match(/[A-Z]+/)) strength += 1;
        if (pass.match(/[0-9]+/)) strength += 1;
        if (pass.match(/[$@#&!.:,;]+/)) strength += 1;

        if (strength <= 2 && pass.length > 0) {
            alert('Attenzione la password non è sicura: deve contenere almeno un carattere MAIUSCOLO e un numero e un carattere speciale tipo il . o !');
            $('#password').val('');
        }
    });
JS;
$this->registerJs($js);

?>

<div class="site-signup" style="min-height: 80vh; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 20px 0;">

    <div style="margin-bottom: 30px;">
        <img src="/uploads/login_logo.png" alt="Logo Planorys" style="max-width: 350px; height: auto; opacity: 0.9; object-fit: contain;">
    </div>

    <div style="width: 100%; max-width: 450px;">
        <?= $flashMessages ?>
    </div>

    <div class="card shadow-lg" style="width: 100%; max-width: 450px; border-radius: 16px; border-top: 4px solid var(--primary-color, #002c48); text-align: left;">

        <div class="card-header text-center border-0 pt-4 pb-0">
            <h3 class="font-weight-bold" style="color: var(--primary-color, #002c48);">
                <i class="fas fa-user-plus mr-2"></i><?= Html::encode($this->title) ?>
            </h3>
        </div>

        <div class="card-body p-4">
            <p class="login-box-msg text-muted text-center mb-4">
                Crea un nuovo account compilando i campi sottostanti.
            </p>

            <?php $form = ActiveForm::begin(['id' => 'form-signup', 'action' => ['site/signup']]); ?>

            <?= $form->field($model, 'username', [
                'template' => '
                    <div class="input-group mb-3">
                        {input}
                        <div class="input-group-append">
                            <div class="input-group-text" style="border-radius: 0 20px 20px 0;">
                                <span class="fas fa-user text-muted"></span>
                            </div>
                        </div>
                    </div>
                    {error}',
            ])->textInput([
                'placeholder' => 'Username',
                'autofocus' => true,
                'maxlength' => 29,
                'style' => 'border-radius: 20px 0 0 20px;'
            ])->label(false) ?>

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
                'placeholder' => 'Indirizzo Email',
                'style' => 'border-radius: 20px 0 0 20px;'
            ])->label(false) ?>

            <?= $form->field($model, 'piva', [
                'template' => '
                    <div class="input-group mb-3">
                        {input}
                        <div class="input-group-append">
                            <div class="input-group-text" style="border-radius: 0 20px 20px 0;">
                                <span class="fas fa-id-card text-muted"></span>
                            </div>
                        </div>
                    </div>
                    {error}',
            ])->textInput([
                'placeholder' => 'Partita IVA',
                'style' => 'border-radius: 20px 0 0 20px;'
            ])->label(false) ?>

            <?= $form->field($model, 'password', [
                'template' => '
                    <div class="input-group mb-3">
                        {input}
                        <div class="input-group-append">
                            <div class="input-group-text" style="border-radius: 0 20px 20px 0;">
                                <span class="fas fa-lock text-muted"></span>
                            </div>
                        </div>
                    </div>
                    {error}',
            ])->passwordInput([
                'id' => 'password',
                'placeholder' => 'Password (es. Abc123!.)',
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
                'id' => 'password2',
                'placeholder' => 'Ripeti Password',
                'style' => 'border-radius: 20px 0 0 20px;'
            ])->label(false) ?>

            <div class="form-group mt-2">
                <?= Html::submitButton('<i class="fas fa-user-check mr-2"></i> Registrati', [
                    'class' => 'btn btn-primary btn-block pill py-2 shadow-sm',
                    'name' => 'signup-button',
                    'style' => 'background-color: var(--primary-color, #002c48); border: none; font-size: 1.1rem;'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <div class="text-center mt-3">
                <p class="text-muted mb-0">Hai già un account?</p>
                <?= Html::a('Torna al Login', ['site/login'], ['class' => 'font-weight-bold', 'style' => 'color: var(--primary-color, #002c48);']) ?>
            </div>

        </div>
    </div>
</div>