<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\Xaraltdesc */

use yii\helpers\ArrayHelper;

$db = Yii::$app->db5;

// 1. Estrai le città uniche
$command = $db->createCommand("select cd_ar as id ,(cd_ar+' '+ descrizione) as 
 Name from ar");
$alst_art = $command->queryAll();



$lst_art = ArrayHelper::map($alst_art, 'id', 'Name');


?>
<?php
$this->registerCss("

    .content {
        width: 90% !important;
        margin: 0;
        padding: 0;
    }
        

");
?>
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success">
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger">
        <?= Yii::$app->session->getFlash('error') ?>
    </div>
<?php endif; ?>

<div class="xaraltdesc-form">

    <?php $form = ActiveForm::begin(); ?>



    <?= $form->field($model, 'cd_ar')->widget(Select2::classname(), [
        'data' => $lst_art,

        'size' => 'lg',
        'options' => [
            'placeholder' => 'seleziona articolo ...',
            'multiple' => false,
            'tags' => true,
            'value' => $model->cd_ar,
            'maximumInputLength' => 10


        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>

    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'button-base button-lift']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<table width="100%">sdaa</table>