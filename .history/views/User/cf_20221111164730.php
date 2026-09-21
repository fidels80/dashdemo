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

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\usrgrp;
use app\models\xmenu;
use app\models\xsubmenu;
use app\models\Anacli;
use dosamigos\fileupload\FileUploadui;
use app\models\user;
$request = Yii::$app->request;

$id = $request->get('id');

$model=User::findOne($id);



$cli= anacli::find()
        ->Select(['cd_cli'])
        ->where (['PartitaIva'=>($model->piva ?? '01')])
        ->AsArray()
        ->One();
        yii::warning($model->cd_cli ?? 'errore');

        if (empty($cli)==false || empty( $model->cd_cli)==true){
        $model->cd_cli= $cli['cd_cli'];
        }


 
 
$lst_menu=ArrayHelper::map($xmenus,'id','Name');

$cli= Anacli::find()
->select(['cd_cli as id','Desk as Name'])
->asArray()
->all();
$lstcli=ArrayHelper::map($cli,'id','Name');





?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id'=>'usrcf','options' => ['enctype' => 'multipart/form-data']]); ?>

 a





 <?php  
echo <<<EOD
<div class="card card-danger">
<div class="card-header">
<h3 class="card-title">Cambio Azienda</h3>
</div>
<div class="card-body">
EOD;


//echo '<br><h3>status a 10 per attivazione utenza</h3><br>';

echo $form->field($model, 'cd_cli', ['enableClientValidation' => false])->
    widget(Select2::classname(), [
    'data' => $lstcli,

    'size' => 'lg',
    'options' => ['placeholder' => 'seleziona Cliente ...', 
        

    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);


echo <<<EOD
</div>
<div class="card-footer">
</div>
</div>
EOD;



 
?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
