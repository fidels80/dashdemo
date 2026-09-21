<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Uecanagrafica;
 
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\Uectesta */
/* @var $form yii\widgets\ActiveForm */
$zcli = Uecanagrafica::find()
    ->select(['id', "CONCAT(nome,' ' ,cognome)  as descrizione "])
    ->asArray()
    ->all();
$zclimap = array_column($zcli, 'descrizione', 'id');
$tipopag = ['Carta' => 'Carta', 'Contanti' => 'Contanti', 'Assegno' => 'Assegno', 'Bancomat' => 'Bancomat'];


?>

<div class="uectesta-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'data')->textInput() ?>

    <?= $form->field($model, 'numero')->textInput() ?>

    <?= $form->field($model, 'cliente')->widget(Select2::classname(), [
    'data' => $zclimap,
    'size' => 'lg',
    'options' => ['placeholder' => 'Seleziona Cliente ...'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]); ?>
<?= $form->field($model, 'tipopag')->widget(Select2::classname(), [
    'data' => $tipopag,
    'size' => 'lg',
    'options' => ['placeholder' => 'Seleziona Metodo ...'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]); ?>
    <?= $form->field($model, 'esportato')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
