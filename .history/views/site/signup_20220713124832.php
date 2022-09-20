<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>






     <script type="text/javascript" src="http://code.jquery.com/jquery-2.2.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('#password2').focusout(function(){
        var pass = $('#password').val();
        var pass2 = $('#password2').val();
        if(pass != pass2){
            alert('le password non sono uguali!');
        }

    });
});
       </script> 
    



<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to signup:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup', 'action' =>['site/signup']]); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email') ?>
                 <?= $form->field($model, 'piva') ?>

                <?= $form->field($model, 'password')->passwordInput() ?>
                <?=$form->field($model, 'password')->passwordInput(['id' => 'password2','autofocus' => true])?>

           
              <?= widget(Captcha::className(), [
    'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
])?>


                <div class="form-group">
                    <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
