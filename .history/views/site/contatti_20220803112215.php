<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\Html;
use app\models\User;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['email'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
 $grid = []; //array('id'=>'default','name'=>'default');
$grid[] = array('id' => 'default', 'Name' => 'Richiesta Preventivo');
$grid[] = array('id' => 'primary', 'Name' => 'Domanda Amministrativa');
$grid[] = array('id' => 'secondary', 'Name' => 'Richiesta di ricontatto');
$grid[] = array('id' => 'info', 'Name' => 'Problematica Portale');
$grid[] = array('id' => 'danger', 'Name' => 'Problematica Pubblicazione');
$grid[] = array('id' => 'warning', 'Name' => 'Altro');

$grid2 = ArrayHelper::map($grid, 'id', 'Name');
 


$this->title = 'Richiedi Preventivo';
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
 Se Volete richiederci un preventivo  o per  altre domande, 
 compilate il seguente modulo per contattarci.
            Grazie.
        </p>

        <div class="row">
            <div class="col-lg-5">

                <?php $form = ActiveForm::begin(['id' => 'contact-form']);?>

                    <?=$form->field($model, 'name')->textInput(['autofocus' => true])->label('Il tuo Nome',['class'=>'label-class'])?>

                    <?=$form->field($model, 'email')->textInput(['autofocus' => true,'value' => $ris['email']]);?>

                    <?=$form->field($model, 'subject')->
                    widget(Select2::classname(), [
    'data' => $grid2,
    'options' => ['placeholder' => 'Seleziona Oggetto'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])->label('Oggetto')?>

                    
                 

                    <?=$form->field($model, 'body')->textarea(['rows' => 6])->label('Richiesta',['class'=>'label-class'])?>
<?= $form->field($model, 'file')->fileInput() ?>

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
