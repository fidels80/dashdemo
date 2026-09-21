<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

use app\models\User;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\models\Xtravelhead;

$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['email'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$this->title = '';
$ajax = 0;
$request = Yii::$app->request;

$get = $request->get();


$grid = []; //array('id'=>'default','name'=>'default');
$grid[] = array('id' => 'Richiesta Preventivo', 'Name' => 'Richiesta Preventivo');
$grid[] = array('id' => 'Domanda Amministrativa', 'Name' => 'Domanda Amministrativa');
$grid[] = array('id' => 'Richiesta di ricontatto', 'Name' => 'Richiesta di ricontatto');
$grid[] = array('id' => 'Problematica Portale', 'Name' => 'Problematica Portale');
$grid[] = array('id' => 'Problematica Pubblicazione', 'Name' => 'Problematica Pubblicazione');
$grid[] = array('id' => 'Richiedi Associazione', 'Name' => 'Richiedi Associazione');
$grid[] = array('id' => 'Altro', 'Name' => 'Altro');
$grid[] = array('id' => 'supporto', 'Name' => 'Richiedi Supporto ');
if (isset($get['id'])) {
    yii::error($get['id']);
    //echo $get['id'];
    $ajax = 1;
   // $grid[] = array('id' => 'Supporto Documento', 'Name' => 'Supporto Documento');
    $doc = xtravelhead::find()->where(['th_id' => $get['id']])->AsArray()->one();
    $grid[] = array(
        'id' => 'Supporto Documento',
        'Name' =>'Richiedi Supporto Documento ' .$doc['descrizione'].
         ' Numero ' . $doc['numero'].' del ' .date('d/m/Y', strtotime($doc['datath'])) );
} else {
    yii::error('no');
}

if (isset($get['periodo'])) {
    yii::error($get['periodo']);
    $mese=$get['periodo'];
    //echo $get['id'];
    $ajax = 1;
    $fmt = new \IntlDateFormatter(
    'it_IT',
    \IntlDateFormatter::NONE,
    \IntlDateFormatter::NONE,
    'Europe/Rome',
    null,
    'MMMM yyyy'
);
   // $grid[] = array('id' => 'Supporto Documento', 'Name' => 'Supporto Documento');
   // $doc = xtravelhead::find()->where(['th_id' => $get['id']])->AsArray()->one();
   $data = $fmt->format(strtotime($mese . '-01'));

// Prima lettera maiuscola (gestisce anche parole accentate)
$testo1= mb_convert_case($data, MB_CASE_TITLE, "UTF-8"); 
   
   $grid[] = array(
        'id' => 'Supporto Periodo',
        'Name' =>'Richiedi Supporto per il periodo ' .$testo1 );
} else {
    yii::error('no');
}


$grid2 = ArrayHelper::map($grid, 'id', 'Name');

if ($ajax == 1) {
    //  $this->title = 'Richiedi Supporto Documento del '.$doc['datath'].' Numero '.$doc['numero']. '  ID:'.$doc['th_id'] ;

} else {
    //$this->title = 'Richiedi Preventivo';
}
    $grid2 = \yii\helpers\ArrayHelper::map($grid, 'id', 'Name');

    // Sovrascrivi il subject col valore descrittivo
    if (isset($grid2[$model->subject])) {
        $model->subject = $grid2[$model->subject];
    }
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">

    <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

        <div class="alert alert-success">
            Grazie per averci contattato , ti risponderemo nel più breve tempo possibile.
            Una copia della tua richiesta ti è stata inviata per email.
        </div>

        <p>

            <?php if (Yii::$app->mailer->useFileTransport): ?>
                Because the application is in development mode, the email is not sent but saved as
                a file under <code><?= Yii::getAlias(Yii::$app->mailer->fileTransportPath) ?></code>.
                Please configure the <code>useFileTransport</code> property of the <code>mail</code>
                application component to be false to enable email sending.
            <?php endif; ?>
        </p>

    <?php else: ?>
        <?php





        ?>

        <?php if ($ajax == 1): ?>

            <p>
                Richiesta di supporto per il documento
            </p>
        <?php else: ?>
            <p>
                Se Volete richiederci un preventivo o per altre domande,
                compilate il seguente modulo per contattarci.
                Grazie.
            </p>
        <?php endif; ?>



        <div class="row">
            <div class="col-lg-15">

                <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

                <?= $form->field($model, 'name')->textInput(['autofocus' => true])->label('Il tuo Nome', ['class' => 'label-class']) ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'value' => $ris['email']]); ?>

                <?php
                $ra = $request->get('RA');

                if (isset($ra)) {
                    echo $form->field($model, 'subject')->widget(Select2::classname(), [
                            'data' => $grid2,
                            'options' => ['placeholder' => 'Seleziona Oggetto', 
                            'value' => 'Richiedi Associazione'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ])->label('Oggetto');
                } elseif ((isset($get['id']))) {

                    echo $form->field($model, 'subject')
                    ->textInput(['autofocus' => true,'readonly'=>true,
                     'value' => 'Richiedi Supporto Documento  '.$doc['descrizione'] . ' Numero ' . $doc['numero'] .
                            ' del ' .
                            date('d/m/Y', strtotime($doc['datath'])) .     '  ID:' . $doc['th_id']])->label('Oggetto');
                } 
                                 elseif ((isset($get['periodo']))) {

                    echo $form->field($model, 'subject')
                    ->textInput(['autofocus' => true,'readonly'=>true,
                     'value' => 'Richiedi Supporto per il periodo ' .$testo1 ])->label('Oggetto');
               
   
  
                
                                 }
                else {

                    echo $form->field($model, 'subject')->widget(Select2::classname(), [
                            'data' => $grid2,
                            'options' => ['placeholder' => 'Seleziona Oggetto', 'value' => 'Richiesta Preventivo'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ])->label('Oggetto');
                } 
                
                
                
                ?>



                <?= $form->field($model, 'body')->textarea(['rows' => 6])->label('Richiesta', ['class' => 'label-class']) ?>

                <?= $form->field($model, 'files[]')->fileInput(['multiple' => true]) ?>

                <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-5">{image}</div><div class="col-lg-8">{input}</div></div>',
                ])->label('Non sei un robot', ['class' => 'label-class']) ?>

                <div class="form-group">
                    <?= Html::submitButton('Manda Email', ['Contatti', 'class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>

    <?php endif; ?>
</div>