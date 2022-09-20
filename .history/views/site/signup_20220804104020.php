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

    <p>Compila i campi per registrarti:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup', 'action' =>['site/signup']]); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'email')?>
                <?= $form->field($model, 'piva')?>
                <?= $form->field($model, 'password')-> 
                // widget(PasswordInput::classname(), ['id' => 'password']);
                passwordInput(['id' => 'password',
                'pattern'=>'/^(?=.*[0-9])(?=.*[A-Z])([a-zA-Z0-9]+)$/']) ;
              
                
                ?>
               <?= Html::checkbox('reveal-password', false, ['id' => 'reveal-password']) ?> <?= Html::label('Show password', 'reveal-password')?>
                <?=$form->field($model, 'password')->
                //   widget(PasswordInput::classname(), ['id' => 'password2']);
                passwordInput(['id' => 'password2',
                'pattern'=>'/^(?=.*[0-9])(?=.*[A-Z])([a-zA-Z0-9]+)$/']);
                
                
                ?>

           


                <div class="form-group">
                    <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
