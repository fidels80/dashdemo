<?php

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \app\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use demogorgorn\ajax\AjaxSubmitButton;
use yii\helpers\Url;

$this->title = 'Recupera Password';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-request-password-reset d-flex justify-content-center align-items-center" style="min-height: 50vh;">

    <div class="card shadow-lg" style="width: 100%; max-width: 450px; border-radius: 16px; border-top: 4px solid var(--primary-color, #007bff);">

        <div class="card-header text-center border-0 pt-4 pb-0">
            <h3 class="font-weight-bold" style="color: var(--primary-color, #007bff);">
                <i class="fas fa-envelope mr-2"></i><?= Html::encode($this->title) ?>
            </h3>
        </div>

        <div class="card-body p-4">
            <p class="login-box-msg text-muted text-center mb-4">
                Inserisci l'indirizzo email collegato al tuo account. Ti invieremo un link per reimpostare la password.
            </p>

            <div id="output" class="text-center mb-3"></div>

            <?php $form = ActiveForm::begin([
                'action' => ['site/RequestPasswordReset'],
                'id' => 'request-password-reset-form'
            ]); ?>

            <?= $form->field($model, 'email', [
                'template' => '
                        <div class="input-group mb-3">
                            {input}
                            <div class="input-group-append">
                                <div class="input-group-text" style="border-radius: 0 20px 20px 0;">
                                    <span class="fas fa-at text-muted"></span>
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
                <?php
                // Il tuo AjaxSubmitButton, ma vestito a festa!
                AjaxSubmitButton::begin([
                    'label' => '<i class="fas fa-paper-plane mr-2"></i> Invia Richiesta',
                    'encodeLabel' => false, // Fondamentale per far vedere l'icona HTML
                    'ajaxOptions' => [
                        'type' => 'POST',
                        'url' => Url::to(['site/RequestPasswordReset']),
                        'success' => new \yii\web\JsExpression('function(html){
                                // Stampa la risposta nel div output
                                $("#output").html(html);
                                
                                // (Opzionale) Mostra il loader globale che abbiamo creato prima!
                                if(typeof showLoader === "function") showLoader();
                                setTimeout(function(){ 
                                    if(typeof hideLoader === "function") hideLoader(); 
                                }, 1000);
                            }'),
                    ],
                    'options' => [
                        'class' => 'btn btn-primary btn-block pill py-2 shadow-sm',
                        'style' => 'background-color: var(--primary-color, #007bff); border: none; font-size: 1.1rem;',
                        'type' => 'submit'
                    ],
                ]);
                AjaxSubmitButton::end();
                ?>
            </div>

            <?php ActiveForm::end(); ?>

            <div class="text-center mt-3">
                <?= Html::a('<i class="fas fa-arrow-left mr-1"></i> Torna al Login', ['site/login'], ['class' => 'text-muted']) ?>
            </div>

        </div>
    </div>
</div>