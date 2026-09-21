<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */

?>
<div class="rapportini-create">

    <h1> Inserisci Rapportino'</h1>

    
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
  <td>  <?= $form->field($model, 'cd_cli')->textInput(['maxlength' => true]) ?></td>
 <td>
    <?= $form->field($model, 'commessa')->textInput(['maxlength' => true]) ?>
 </td><td></td></tr>
 <tr>
      <td>  <?= $form->field($model, 'data')->textInput() ?></td>

    <td><?= $form->field($model, 'ora_in')->textInput() ?></td>

    <td><?= $form->field($model, 'ora_out')->textInput() ?></td>
 </tr>
 <tr><td>    <?= $form->field($model, 'cd_art')->textInput(['maxlength' => true]) ?></td>

 <td>   <?= $form->field($model, 'des_art')->textInput(['maxlength' => true]) ?></td>
<td>
 <?= $form->field($model, 'qta')->textInput() ?>
 </td></tr>

     <?= $form->field($model, 'note')->textInput() ?>


    <div class="form-group">
        <?php  Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
</tbody></table>
    <?php ActiveForm::end(); ?>

</div>

</div>
