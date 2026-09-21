<?php use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'id'                     => 'create-form',
    'enableClientValidation' => true,
]);

echo $form->field($model, 'testo')->textInput(['maxlength' => true]);
echo $form->field($model, 'testo2')->textInput(['maxlength' => true]);

echo Html::submitButton('Salva', ['class' => 'btn btn-success']);

ActiveForm::end();
?>