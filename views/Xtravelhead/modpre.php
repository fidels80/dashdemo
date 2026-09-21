<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use app\models\Cf;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\Xtravelhead $model */

$this->title = 'Modifica Prenotazione: ' . $model->numero;

// Lista utenti per Multi-Select
$usersList = ArrayHelper::map(User::find()->where(['istourmanager' => 1])->orderBy('username')->all(), 'id', 'username');

// Dati Clienti per JS
$anagrafica = Cf::find()->all();
$listaClienti = ArrayHelper::map($anagrafica, 'Cd_CF', 'Descrizione');
$clientiJson = json_encode($listaClienti);
?>
<style>
    .nowrap {
        white-space: nowrap;
    }

    .content {
        width: 95%;
    }

    /* ... restanti stili ... */
</style>
<?php
// Inserisci questo all'inizio del div class="xtravelhead-modpre"
?>

<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= Yii::$app->session->getFlash('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= Yii::$app->session->getFlash('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?= Yii::$app->session->getFlash('warning') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<div class="xtravelhead-modpre">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="xtravelhead-form">
        <?php $form = ActiveForm::begin(['id' => 'form-modifica-prenotazione']); ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'datath')->widget(DatePicker::classname(), [
                    'pluginOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd']
                ]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'numero')->textInput(['readonly' => true, 'id' => 'input-numero']) ?>
            </div>
            <div class="col-md-4">
                <label>Anteprima Decodificatore</label>
                <?= Html::textInput('anteprima', '', ['id' => 'input-decodificatore', 'class' => 'form-control', 'readonly' => true, 'style' => 'background-color: #eee;']) ?>
            </div>
        </div>

        <?= $form->field($model, 'descrizione')->textarea(['id' => 'input-descrizione', 'rows' => 3]) ?>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'x_cd_cf')->widget(Select2::classname(), [
                    'data' => $listaClienti,
                    'options' => ['id' => 'select-cliente', 'placeholder' => 'Seleziona Cliente...'],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Cliente') ?>
            </div>
            <div class="col-md-8">
                <?= $form->field($model, 'x_cfdesk')->textInput(['id' => 'descrizione-cliente', 'readonly' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'x_acconto')->textInput(['type' => 'number', 'step' => '0.01']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'x_tiposhow')->dropDownList([
                    1 => 'Città',
                    2 => 'Commessa',
                    3 => 'Festival'
                ], ['prompt' => 'Seleziona tipo...']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'manager_ids')->widget(Select2::classname(), [
                    'data' => $usersList,
                    'options' => [
                        'placeholder' => 'Seleziona Tour Managers...',
                        'multiple' => true,
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ])->label('Tour Managers Assegnati') ?>
            </div>
        </div>

        <div class="form-group" style="margin-top: 20px;">
            <?= Html::submitButton('Salva Modifiche', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Annulla', ['masterhotel', 'id' => $model->th_id], ['class' => 'btn btn-secondary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
// Re-inseriamo la logica JS per il decodificatore e la descrizione cliente
$this->registerJs("
    var datiClienti = $clientiJson;
    
    function aggiornaDeco() {
        $('#input-decodificatore').val('Num: ' + $('#input-numero').val() + ' Des: ' + $('#input-descrizione').val());
    }

    $('#select-cliente').on('change', function() {
        var val = $(this).val();
        $('#descrizione-cliente').val(val && datiClienti[val] ? datiClienti[val] : '');
    });

    $('#input-numero, #input-descrizione').on('input change', aggiornaDeco);
    aggiornaDeco();
");
?>