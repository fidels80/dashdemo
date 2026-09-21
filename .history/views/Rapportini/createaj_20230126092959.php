<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */

$this->title = 'Inserisci Rapportino';
$this->params['breadcrumbs'][] = ['label' => 'Rapportinis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rapportini-create">

    <h1><?= Html::encode($this->title) ?></h1>

    
<div class="rapportini-form">
 <?php echo '<table id="product-files" class="table table-condensed table-bordered">';
echo '<thead>';
echo '<tr>';
//echo '<th>id_agenda</th>';
echo '<th width="70%">file</th>';
echo '<th width="30%">';
     $form = ActiveForm::begin(); 
     echo '</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

     
     ?>

    <?= $form->field($model, 'cd_cli')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'commessa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qta')->textInput() ?>

    <?= $form->field($model, 'data')->textInput() ?>

    <?= $form->field($model, 'ora_in')->textInput() ?>

    <?= $form->field($model, 'ora_out')->textInput() ?>

    <?= $form->field($model, 'userid')->textInput() ?>

    <?= $form->field($model, 'note')->textInput() ?>

    <?= $form->field($model, 'cd_art')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'des_art')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>
