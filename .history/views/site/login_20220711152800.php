<?php
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use yii\helpers\Url;

?>
<div class="card">
    <div class="card-body login-card-body">
        <p class="login-box-msg">Collegati</p>

        <?php $form = \yii\bootstrap4\ActiveForm::begin(['id' => 'login-form', 
         'action' => ['site/login']]) ?>

        <?= $form->field($model,'email', [
            'options' => ['class' => 'form-group has-feedback'],
            'inputTemplate' => '{input}<div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>',
            'template' => '{beginWrapper}{input}{error}{endWrapper}',
            'wrapperOptions' => ['class' => 'input-group mb-3']
        ])
            ->label(false)
            ->textInput(['placeholder' => 'email']//$model->getAttributeLabel('username')]
            ) ?>

        <?= $form->field($model, 'password', [
            'options' => ['class' => 'form-group has-feedback'],
            'inputTemplate' => '{input}<div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>',
            'template' => '{beginWrapper}{input}{error}{endWrapper}',
            'wrapperOptions' => ['class' => 'input-group mb-3']
        ])
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>

        <div class="row">
            <div class="col-8">
                <?= $form->field($model, 'rememberMe')->checkbox([
                    'template' => '<div class="icheck-primary">{input}{label}</div>',
                    'labelOptions' => [
                        'class' => ''
                    ],
                    'uncheck' => null
                ]) ?>
            </div>
            <div class="col-4">
                <?php echo Html::submitButton('Collegati', ['class' => 'btn btn-primary btn-block']) ;
             echo '<br><br>';
            ?>
            </div>
        </div>

        <?php \yii\bootstrap4\ActiveForm::end(); ?>

<?php 
$tmpid = 0;
Modal::begin([
    //'header'=>'<h4>Clienti</h4>',
    'id' => 'cli' . $tmpid,
    'size' => 'modal-lg', //classe bootstrap
]);
echo "<div id='modalContent'></div>";
Modal::end();
$this->registerJs("
  $('#modalcli_$tmpid').click(function (){
  $('#cli$tmpid').modal('show')
  .find('#modalContent')
  .load($(this).attr('value'));
  });"
);
$url = Url::to(['user/rp']);
//echo Html::button('Carica File', ['value' => $url,
//    'class' => 'btn btn-success', 'id' => 'modalcli_' . $tmpid]);
?>

      <!--   <div class="social-auth-links text-center mb-3">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-primary">
                <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
            </a>
            <a href="#" class="btn btn-block btn-danger">
                <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
            </a>
        </div>
        /.social-auth-links -->

        <p class="mb-1">
         <?php   
        
        echo Html::a('Reimposta Password', ['user/rp','reset'=>1]);

        ?>
        </p>
        <p class="mb-0">
 <?php
            echo Html::a('Registrati', ['login','isnew'=>true]);
?>
        </p>
    </div>
    <!-- /.login-card-body -->
</div>


<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

 