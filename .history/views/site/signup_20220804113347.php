<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
//use kartik\widgets\PasswordInput;


$this->title = 'Registrati';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs("jQuery('#reveal-password').change(function(){
    jQuery('#Password').attr('type',this.checked?'text':'password');})");

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

    $('#password').focusout(function(){
        var pass = $('#password').val();
      
var strength=0;
    if (pass.match(/[a-z]+/)){
        strength+=1;
    }
    if (pass.match(/[A-Z]+/)){
        strength+=1;
    }
    if (pass.match(/[0-9]+/)){
        strength+=1;
    }
    if (pass.match(/[$@#&!]+/)){
        strength+=1;

        }
 
alert(strength);

if (strength==0){

alert('Attenzione la password non è sicura deve contenere almeno un carattere MAIUSCOLO e un numero');

}


    });


});
       </script> 
    



<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Compila i campi per registrarti:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup', 'action' =>['site/signup']]); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email')?>
                <?= $form->field($model, 'piva')?>
                <?= $form->field($model, 'password')-> 
                // widget(PasswordInput::classname(), ['id' => 'password']);
                passwordInput(['id' => 'password']) ;
              
                
                ?>
                 <?=$form->field($model, 'password')->
                //   widget(PasswordInput::classname(), ['id' => 'password2']);
                passwordInput(['id' => 'password2']);
                
                
                ?>

           


                <div class="form-group">
                    <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
