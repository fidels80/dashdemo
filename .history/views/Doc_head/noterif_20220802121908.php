<?php

use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Doc_head */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="doc-head-form">

    <?php $form = ActiveForm::begin();?>
    
    
    ciasidasia
    <?php echo $form->field($model, 'id')->textInput(['hidden' => false])->label('')?>

    <?php echo $form->field($model, 'rifiutato_nota')->textarea(['rows' => 6])
    ->label('Motivazioni Rifiuto') ?>

<?php echo Html::a(
    'Rifiuta',
    ['Rifiuta', 'id' => $model->id,'nota'=>$model->rifiutato_nota],
    ['class' => 'btn  btn-danger',
        'data' => [
            'confirm' => 'Sei sicuro di voler ANNULLATE il documento?',
            'method' => 'post',
        ]],
);

?>

    <?php ActiveForm::end();?>

</div>
