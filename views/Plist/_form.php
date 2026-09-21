<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
 

$tipo = [['id' => 'A',
    'Descrizione'  => 'Acquisti'],
    ['id' => 'V', 'Descrizione' => 'Vendite']
];
$listtipo = ArrayHelper::map($tipo, 'id', 'Descrizione');

/* @var $this yii\web\View */
/* @var $model app\models\Plist */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="plist-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'tipo')->widget(Select2::classname(),
    ['data'         => $listtipo,
        'options'       => ['placeholder' => 'Seleziona Tipologia ...',
            'id'                              => 'tipo'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
