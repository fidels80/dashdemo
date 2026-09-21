<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

use yii\bootstrap4\Modal;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\db\Expression;
/* @var $this yii\web\View */
/* @var $model app\models\Xroomlist */
/* @var $form yii\widgets\ActiveForm */

$partyData = (new \yii\db\Query())
    ->select(['party as cd_party', 'party as dparty'])
    ->from('xtravelrow')
    ->where(['not', ['party' => null]])
    ->distinct()
    ->createCommand(Yii::$app->db5)
    ->queryAll();

$ruoloData = (new \yii\db\Query())
    ->select(['cd_ruolo as cd_ruolo', 'descrizione  as druolo'])
    ->from('xruoli')
    ->where(['not', ['cd_ruolo' => null]])
    ->distinct()
    ->createCommand(Yii::$app->db5)
    ->queryAll();

$commessaData = (new \yii\db\Query())
    ->select(['Cd_DOSottoCommessa as cd_commessa', 'Descrizione as dcommessa'])
    ->from('DOSottoCommessa')
    ->where(['not', ['Cd_DOSottoCommessa' => null]])
    ->distinct()
    ->createCommand(Yii::$app->db5)
    ->queryAll();


$listaart = (new \yii\db\Query())
    ->select([
        'Cd_Ar as id',
        new Expression("Cd_Ar + '   ' + Descrizione AS desk")
    ])
    ->from('Ar')
    ->where(['obsoleto' => 0])
    ->createCommand(Yii::$app->db5)
    ->queryAll();


$partyOptions = ArrayHelper::map($partyData, 'cd_party', 'dparty');
$ruoloOptions = ArrayHelper::map($ruoloData, 'cd_ruolo', 'druolo');
$commessaOptions = ArrayHelper::map($commessaData, 'cd_commessa', 'dcommessa');
$listaart2 = ArrayHelper::map($listaart, 'id', 'desk');
$guid = Yii::$app->db->createCommand("SELECT NEWID()")->queryScalar();

$model->id_guest = $guid;
?>

<style>
    /* CSS minimo e corretto per Select2 nelle modali */
    .modal {
        overflow: visible !important;
    }

    .select2-container--open {
        z-index: 1060 !important;
    }
</style>

<div class="xroomlist-form">

    <?php $form = ActiveForm::begin([
        'id' => 'xroomlist-form',
        'action' => ['xroomlist/createaj', 'th_id' => $model->th_id],
        'enableAjaxValidation' => false,
    ]); ?>

    <?= $form->field($model, 'id_guest')->textInput(['readonly' => true]) ?>

    <?= $form->field($model, 'nominativo')->textInput(['maxlength' => true]) ?>

    <!-- Select2 per cd_ar -->
    <?= $form->field($model, 'cd_ar')->widget(\kartik\select2\Select2::class, [
        'data' => $listaart2,
        'options' => [
            'placeholder' => 'Seleziona cd_Ar...',
            'id' => 'cd_ar-select',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>

    <?= $form->field($model, 'th_id')->textInput([
        'readonly' => true,
    ]) ?>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>

    <!-- Select2 per ruolo -->
    <?= $form->field($model, 'ruolo')->widget(\kartik\select2\Select2::class, [
        'data' => $ruoloOptions,
        'options' => [
            'placeholder' => 'Seleziona ruolo...',
            'id' => 'ruolo-select',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>

    <!-- Select2 per commessa -->
    <?= $form->field($model, 'commessa')->widget(\kartik\select2\Select2::class, [
        'data' => $commessaOptions,
        'options' => [
            'placeholder' => 'Seleziona commessa...',
            'id' => 'commessa-select',  // CORRETTO: rimosso lo spazio
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>

    <!-- Select2 per party -->
    <?= $form->field($model, 'party')->widget(\kartik\select2\Select2::class, [
        'data' => $partyOptions,
        'options' => [
            'placeholder' => 'Seleziona party...',
            'id' => 'party-select',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

