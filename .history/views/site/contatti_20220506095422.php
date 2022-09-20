<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="card-header">
<h3 class="card-title">Contattaci</h3>
</div>

<div class="card-body">
<form>
<div class="row">
<div class="col-sm-6">

<div class="form-group">
<label>Oggetto</label>
<input type="text" class="form-control" placeholder="Enter ...">
</div>
</div>
<div class="col-sm-6">

</div>
</div>
<div class="row">
<div class="col-sm-6">

<div class="form-group">
<label>Messaggio</label>
<textarea class="form-control" rows="3" placeholder="Enter ..."></textarea>
</div>
</div>

</div>
</div>
<?php echo Html::button('Invia  mail',['value'=>$url,
   'class' => 'btn btn-success','id'=>'modalcli_'.$tmpid]);
   echo '</th>';?>