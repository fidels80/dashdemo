<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Xvenue */
/* @var $form yii\widgets\ActiveForm */

/*$cittadata = (new \yii\db\Query())
    ->select(['cd_citta', 'descrizione'])
    ->from('x_citta')

    ->distinct()
    ->createCommand(Yii::$app->db5)
    ->queryAll();*/

$fornitoredata = (new \yii\db\Query())
    ->select(['cd_cf as Cd_cf', 'descrizione'])
    ->from('cf')
    // ->where(['like', 'cd_Cf', 'F%'])

    ->createCommand(Yii::$app->db5)
    ->queryAll();

//$cittaOptions = ArrayHelper::map($cittadata, 'cd_citta', 'descrizione');
$cfOptions = ArrayHelper::map($fornitoredata, 'Cd_cf', 'descrizione');
//yii::warning($model->attributes);
//yii::warning($model->Cd_cf); // o $model->cd_cf
//yii::warning(array_keys($cfOptions));

//yii::warning($cfOptions);
//yii::warning($cittaOptions);

$isModal = isset($isModal) && $isModal === true;

/* @var $this yii\web\View */
/* @var $model app\models\Xstruttura */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .select2-container {
        z-index: 9999;
    }

    .select2-dropdown {
        z-index: 9999;
    }
</style>
<div class="xstruttura-form">

    <?php $form = ActiveForm::begin([
        'id' => 'struttura-form',
        'options' => ['data-pjax' => false],
    ]); ?>

    <?= $form->field($model, 'Struttura')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Descrizione')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'Citta')->widget(Select2::class, [
        'options' => ['placeholder' => 'Seleziona una città...'],
        'pluginOptions' => [
            'allowClear' => true,
            'dropdownParent' => new \yii\web\JsExpression('$("#strutturaModal")'),
            'ajax' => [
                'url' => \yii\helpers\Url::to(['xstruttura/xcaricacitta']), // il tuo action
                'dataType' => 'json',
                'delay' => 250,
                'data' => new \yii\web\JsExpression('function(params) { return {q:params.term}; }'),
                'processResults' => new \yii\web\JsExpression('function(data) { return {results:data.items}; }'),
            ],
            'minimumInputLength' => 2, // inizia a cercare dopo 2 caratteri
        ],
    ]); ?>

    <?= $form->field($model, 'Cd_cf')->widget(Select2::class, [
        'data' => $cfOptions,
        'options' => [
            'placeholder' => 'Seleziona Cliente...',
            'id' => 'fornitore-select'
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'dropdownParent' => new \yii\web\JsExpression('$("#strutturaModal")'),
        ],
    ])->label('Codice Cliente'); ?>


    <?= $form->field($model, 'Partitaiva')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'xcheck')->textInput(['readonly' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// JavaScript corretto per gestire il submit via AJAX in Yii2
$this->registerJs("
$('#struttura-form').on('beforeSubmit', function (e) {
    var \$form = $(this);
    
    // Invia i dati solo se la validazione lato client è passata
    $.ajax({
        url: \$form.attr('action'),
        type: 'POST',
        data: \$form.serialize(),
        success: function (response) {
            if (response.success) {
                $('#strutturaModal').modal('hide');
                alert('Struttura inserita con successo');
                // Se vuoi aggiornare la pagina: location.reload();
            } else {
                // Se ci sono errori, ricarichiamo il contenuto della modale
                // Nota: assicurati che il controller restituisca la vista parziale in caso di errore
                $('#modalContent').html(response);
            }
        },
        error: function () {
            alert('Errore durante il salvataggio');
        }
    });
    
    return false; // Impedisce l'invio standard del form (fondamentale!)
});
");
?>