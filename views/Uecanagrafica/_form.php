<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use   app\models\Uecprovincia;
use yii\helpers\ArrayHelper;

$xprov = Uecprovincia::find()
    ->select(['Cd_Provincia as id', 'Descrizione as Name'])
    ->asArray()
    ->all();
$provincia = ArrayHelper::map($xprov, 'id', 'Name');
/* @var $this yii\web\View */
/* @var $model app\models\Uecanagrafica */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="uecanagrafica-form">

    <?php $form = ActiveForm::begin(); ?>
    <table class="table">
        <tr>
            <td>
                <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                <?= $form->field($model, 'cognome')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                <?= $form->field($model, 'indirizzo')->textInput(['maxlength' => true]) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'citta')->textInput(['maxlength' => true]) ?>
            </td>
            <td>
                <?= $form->field($model, 'provincia')->widget(Select2::classname(), [
                    'data' => $provincia,
                    'size' => 'lg',
                    'options' => ['placeholder' => 'Seleziona Provincia ...'],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>
            </td>
            <td>
                <?= $form->field($model, 'nazione')->textInput(['maxlength' => true]) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->field($model, 'codicefiscale')->textInput(['maxlength' => true]) ?>
            </td>
        </tr>
    </table>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>