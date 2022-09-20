<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\PasswordResetRequestForm */

use demogorgorn\ajax\AjaxSubmitButton;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Request password reset';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    <h1><?=Html::encode($this->title)?></h1>


    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success">
          Grazie per averci contattato , ti risponderemo nel più breve tempo possibile.
          Una copia della tua richiesta ti è stata inviata per email.
        </div>

        <p>
            Note that if you turn on the Yii debugger, you should be able
            to view the mail message on the mail panel of the debugger.
            <?php if (Yii::$app->mailer->useFileTransport): ?>
                Because the application is in development mode, the email is not sent but saved as
                a file under <code><?=Yii::getAlias(Yii::$app->mailer->fileTransportPath)?></code>.
                Please configure the <code>useFileTransport</code> property of the <code>mail</code>
                application component to be false to enable email sending.
            <?php endif;?>


    <p>Inserire la mail dell'utenta per la quale si richiede il reset della password</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin([
    'action' => ['user/rp'],
    'id' => 'request-password-reset-form']);?>

                <?=$form->field($model, 'email')->textInput(['autofocus' => true])?>

                <div class="form-group">
   <?= Html::submitButton('Invia', ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end();?>
        </div>
    </div>
</div>
