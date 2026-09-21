<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */

$this->title = 'Inserisci Rapportino';
$this->params['breadcrumbs'][] = ['label' => 'Rapportinis', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Inserisci Rapportino';
?>
<div class="rapportini-create">

    <h1><?php  
    //Html::encode('Inserisci Rapportino') ?></h1>

    
<div class="rapportini-form">
 <?php   $form = ActiveForm::begin([ 'enableClientValidation' => true,'options' => [
        'validateOnSubmit' => true,
        'class' => 'form'
    ]]); 
 echo '<table>';
echo '<thead>';
echo '<tr>';
//echo '<th>id_agenda</th>';
echo '<th width="70%">file</th>';
echo '<th width="30%">';

     echo '</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

     
     ?>
<tr>
    <?= $form->field($model, 'cd_cli')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'commessa')->textInput(['maxlength' => true]) ?>
</tr>
    <?= $form->field($model, 'qta')->textInput() ?>

    <?= $form->field($model, 'data')->textInput() ?>

    <?= $form->field($model, 'ora_in')->textInput() ?>

    <?= $form->field($model, 'ora_out')->textInput() ?>

     <?= $form->field($model, 'note')->textInput() ?>

    <?= $form->field($model, 'cd_art')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'des_art')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?php  Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
</tbody></table>
    <?php ActiveForm::end(); ?>

</div>

</div>
