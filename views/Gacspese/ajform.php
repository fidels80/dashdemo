<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Ar;
use app\models\Listini;
/* @var $this yii\web\View */
/* @var $model app\models\Gacspese */
/* @var $form yii\widgets\ActiveForm */
$ar = ar::find()
    ->select(['Cd_AR as id', 'Descrizione'])
    ->asArray()
    ->all();
$listar = ArrayHelper::map($ar, 'id', 'Descrizione');

$id = Yii::$app->request->get('id');
$idspesa = Yii::$app->request->get('idspesa');
if(isset($idspesa)){
$model['id_sub_prv'] = $idspesa;


}else{
$model['id_sub_prv'] = $id;
}
$spese =
    [['id'        => 1,
    'Descrizione' => 'Manutenzione'],
    ['id' => 2, 'Descrizione' => 'Bolli'],
    ['id' => 3, 'Descrizione' => 'Certificati'],
    ['id' => 4, 'Descrizione' => 'Extra'],
    ['id' => 5, 'Descrizione' => 'Spedizione/Consegna'],
    ['id' => 3, 'Descrizione' => 'Trasporto'],

];
$listspese = ArrayHelper::map($spese, 'id', 'Descrizione');
//yii::warning($listtipo);

?>

<div class="gacspese-form">

    <?php $form = ActiveForm::begin(['id' => 'attForm' . $id, // Assegna un ID unico alla form
    'action' => ['gacspese/create2', 'id' => $id, 'idspesa' => $idspesa],
    'enableAjaxValidation' => true,
    'enableClientValidation' => false]); ?>

    <?= $form->field($model, 'id_sub_prv')->textInput(['readonly'=>true,
    //'hidden'=>true
    ])->label('ajax') ?>
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'spesa')->widget(Select2::classname(),
    ['data'         => $listspese,
        'options'       => ['placeholder' => 'Seleziona spesa ...',
            'id'                              => 'spesa'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])?>
</div>
<div class="col-sm">
        
<?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>
</div>
<div class="col-sm">
   
    <?= $form->field($model, 'um')->textInput(['maxlength' => true]) ?>
</div>
<div class="col-sm">

    <?= $form->field($model, 'qta')->textInput() ?>
</div>
</div>
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'costounitario')->textInput() ?>
</div>
    <div class="col-sm">
    <?= $form->field($model, 'sconto')->textInput() ?>
</div>
<div class="col-sm">
    <?= $form->field($model, 'costonetto')->textInput() ?>
</div>
<div class="col-sm">
    <?= $form->field($model, 'ricarico')->textInput() ?>
</div>
<div class="col-sm">
    <?= $form->field($model, 'costoricaricato')->textInput() ?>
</div>
<div class="col-sm">
    <?= $form->field($model, 'scontovendita')->textInput() ?>
</div>
</div>
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'valorenettounitario')->textInput() ?>
</div>
    <div class="col-sm">

    <?= $form->field($model, 'valorenetto')->textInput() ?>
</div>
    <div class="col-sm">

<?= $form->field($model, 'margine')->textInput() ?>
</div>
    <div class="col-sm">
    <?= $form->field($model, 'margineperc')->textInput() ?>
</div>
</div>   
<div class="row">
    <div class="col-sm">
    <?= $form->field($model, 'cd_ar')->widget(Select2::classname(),
    ['data'         => $listar,
        'options'       => ['placeholder' => 'Seleziona Art ...',
            'id'                              => 'cd_ar'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>
</div>
    <div class="col-sm">

    <?= $form->field($model, 'descrizionear')->textInput(['maxlength' => true])->label('Descrizione') ?>
</div>
    <div class="col-sm">

    <?= $form->field($model, 'prezzoar')->textInput() ?>
</div>
</div>

    <?= $form->field($model, 'note')->textarea(['rows' => 6]) ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
// JavaScript per gestire il submit della form
$js = <<< JS
$('#attForm$id').on('beforeSubmit', function(event) {
    event.preventDefault();
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: 'post',
        data: form.serialize(),
        dataType: 'json',
        success: function(data) {
            console.log(data);
            if (data.success) {
                console.log('success');
                // Chiudi la form modale solo se il salvataggio è andato a buon fine
                $('#att$id').modal('hide');
                // Puoi aggiornare eventualmente la pagina principale o fare altre azioni dopo il salvataggio
            } else {
                console.log('Errore durante il salvataggio.');
                // Se ci sono errori nel modello, puoi anche mostrarli nella console
                if (data.errors) {
                    console.log('Errori nel modello:', data.errors);
                }
            }
        },
        error: function(xhr, status, error) {
            console.log('Errore AJAX:', error);
        }
    });
    return false;
});
JS;
//$this->registerJs($js);
?>

<script>
$(document).ready(function() {
    $('#attForm<?php echo $id; ?>').on('beforeSubmit', function(event) {
        event.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if (data.success) {
                    console.log('success');
                    // Chiudi la form modale solo se il salvataggio è andato a buon fine
                    $('#attForm<?php echo $id; ?>').modal('hide');
                    // Puoi aggiornare eventualmente la pagina principale o fare altre azioni dopo il salvataggio
                } else {
                    console.log('Errore durante il salvataggio.');

                    // Se ci sono errori nel modello, puoi anche mostrarli nella console
                    if (data.errors) {
                        console.log('Errori nel modello:', data.errors);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.log('Errore AJAX:', error);
            }
        });
        return false;
    });
});
</script>