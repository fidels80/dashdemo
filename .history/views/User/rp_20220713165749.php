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
          A Breve riceverai una mail con il link del reset.
          Rincordati di controllare anche lo spam!
        </div>

        <p>
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
