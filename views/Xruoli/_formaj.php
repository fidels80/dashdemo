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
<div class="xruoli-form">

    <?php $form = ActiveForm::begin([
        'id' => 'xruoli-form',
        'options' => ['data-pjax' => false],
    ]); ?>

    <?= $form->field($model, 'cd_ruolo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>
     
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annulla</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// JavaScript per gestire il submit via AJAX
$this->registerJs("
$('#xruoli-form').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                $('#ruoloModal').modal('hide');
                // Opzionale: ricarica la pagina o aggiorna una lista
                 alert('Ruolo inserito  con successo');
               // location.reload();
            } else {
                // Gestisci errori di validazione
                $('#modalContentRl').html(response);
            }
        }
    });
});
");
?>