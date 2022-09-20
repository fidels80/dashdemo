<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\anacli;
use yii\helpers\ArrayHelper;

$cli = Anacli::find()
    ->select(['cd_cli as id', 'Desk as Name'])
    ->asArray()
    ->all();
$lstcli = ArrayHelper::map($cli, 'id', 'Name');


/* @var $this yii\web\View */
/* @var $model app\models\Relcli */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="relcli-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cd_cli')-> widget(Select2::classname(), [
    'data' => $grid2,
    'options' => ['placeholder' => 'Seleziona Oggetto',  'value' =>'Richiesta Preventivo' ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])->label('Cliente Padre') ?>

    <?= $form->field($model, 'altcli')-> widget(Select2::classname(), [
    'data' => $grid2,
    'options' => ['placeholder' => 'Seleziona Oggetto',  'value' =>'Richiesta Preventivo' ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])->label('Cliente Figlio')?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
