<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php $form = ActiveForm::begin(['id' => 'contact-form']);?>

<div class="card-header">
<h3 class="card-title">Contattaci</h3>
</div>

<div class="card-body">
<form>
<div class="row">
<div class="col-sm-6">

<div class="form-group">
<label>Oggetto</label>
<input type="text" id='oggetto'class="form-control" placeholder="Oggetto ...">
</div>
</div>
<div class="col-sm-6">

</div>
</div>
<div class="row">
<div class="col-sm-6">

<div class="form-group">
<label>Messaggio</label>
<textarea id='messaggio' class="form-control" rows="3" placeholder="Messaggio ..."></textarea>
</div>
</div>

</div>
</div>
<?php  

echo Html::a(
    'Invia',
    ['smail'],
    ['class' => 'btn btn-success']
);echo '</th>';
?>

   <?php ActiveForm::end();?>
