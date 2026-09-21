<?php

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \app\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Reimposta Password';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-reset-password d-flex justify-content-center align-items-center" style="min-height: 50vh;">

    <div class="card shadow-lg" style="width: 100%; max-width: 450px; border-radius: 16px; border-top: 4px solid var(--primary-color, #007bff);">
        <div class="card-header text-center border-0 pt-4 pb-0">
            <h3 class="font-weight-bold" style="color: var(--primary-color, #007bff);">
                <i class="fas fa-key mr-2"></i><?= Html::encode($this->title) ?>
            </h3>
        </div>

        <div class="card-body p-4">
            <p class="login-box-msg text-muted text-center mb-4">
                Scegli una nuova password sicura per il tuo account.
            </p>

            <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

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
                'placeholder' => 'Inserisci la nuova password',
                'autofocus' => true,
                'style' => 'border-radius: 20px 0 0 20px;'
            ])->label(false) ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save mr-2"></i> Salva Nuova Password', [
                    'class' => 'btn btn-primary btn-block pill py-2 shadow-sm',
                    'style' => 'background-color: var(--primary-color, #007bff); border: none; font-size: 1.1rem;'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>