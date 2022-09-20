<?php

use yii\widgets\ActiveForm;

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
    ['id' => $model->id],
    ['class' => 'btn btn-danger ',
        //      'data' => [
        //  'confirm' =>  'Sei sicuro di voler RIFIUTARE il documento?',
        //  'method' => 'post',
        //],
        'id' => 'modalcli_' . $tmpid,
        'value' => $url]
);?>

    <?php ActiveForm::end();?>

</div>
