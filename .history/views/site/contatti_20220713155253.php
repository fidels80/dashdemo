<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;

$this->title = 'Contattaci';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
 
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
        </p>

    <?php else: ?>

        <p>
 Se avete richieste commerciali o altre domande, compilate il seguente modulo per contattarci.
            Grazie.
        </p>

        <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin(['id' => 'contact-form']);?>

                    <?=$form->field($model, 'name')->textInput(['autofocus' => true])->label('Il tuo Nome',['class'=>'label-class'])?>

                    <?=$form->field($model, 'email')->textInput(['autofocus' => true]);?>

                    <?=$form->field($model, 'subject')->label('Oggetto',['class'=>'label-class'])?>

                    <?=$form->field($model, 'body')->textarea(['rows' => 6])->label('Richiesta',['class'=>'label-class'])?>

                    <?=$form->field($model, 'verifyCode')->widget(Captcha::className(), [
    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
])->label('Non sei un robot',['class'=>'label-class'])?>

                    <div class="form-group">
                        <?=Html::submitButton('Manda Email', ['Contatti','class' => 'btn btn-primary', 'name' => 'contact-button'])?>
                    </div>

                <?php ActiveForm::end();?>

            </div>
        </div>

    <?php endif;?>
</div>
