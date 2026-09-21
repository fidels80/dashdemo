<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\Pjax;
?>

<?php Pjax::begin(['id' => 'upload-pjax']); ?>

<?php $form = ActiveForm::begin([
    'id' => 'upload-form',
    'action' => ['xtravelhead/ajximage'],
    'method' => 'post',
    'options' => ['enctype' => 'multipart/form-data'],
 //   'enableAjaxValidation' => false,
 //   'enableClientValidation' => true,
]); ?>

<?= $form->field($model, 'th_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'imageFile')->fileInput() ?>

<div class="form-group">
    <?= Html::submitButton('Carica', ['class' => 'button-base button-lift']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php Pjax::end(); ?>

<?php
$this->registerJs(<<<JS

$(document).ready(function() {
    console.log("Script caricato correttamente");
});

$(document).on('submit', '#upload-form', function(event) {
    event.preventDefault();
    var form = $(this);
    console.log("Submitting to: ", form.attr('action')); // Debug

    var formData = new FormData(form[0]);

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $.pjax.reload({container: '#upload-pjax'});
            $('#upl-modal').modal('hide');
        },
        error: function() {
            alert('Errore durante il caricamento');
        }
    });
});
JS);
?>