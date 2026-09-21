<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap4\Alert; // Importa la classe Alert
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
Html::csrfMetaTags();
/* @var $this yii\web\View */
/* @var $model app\models\Allfiles */
/* @var $form yii\widgets\ActiveForm */
$request = Yii::$app->request;
$get = $request->get();
if (!empty($get)) {
    yii::error($get);
}
/*
if (array_key_exists('idagenda', $get)) {
$fid=$get['idagenda'];
}else{
$fid=null;
*/
//}
yii::warning($get);
?>

<div class="allfiles-form">

    <?php $form = ActiveForm::begin(['id' => 'myFormId']); ?>

    <?= $form->field($model, 'id_padre')->textInput(['value' =>
        $get['xid_testa'] ?? ''
        , 'hidden' => true])->label('') ?>


    <?= $form->field($model, 'tab')->textInput(['value' =>
        $get['tab'] ?? ''
        , 'hidden' => true])->label('') ?>

    <?php //$form->field($model, 'f_content')->textInput() ?>
    <?php $form->field($model, 'origine')->textInput(['hidden' => true])->label('') ?>

    <?php echo $form->field($model, 'entita')->textInput(['maxlength' => true,
        'value' => $get['tab'] ?? '', 'hidden' => true])->label('') ?>
    <?php
    echo \hail812\adminlte\widgets\Alert::widget([
        'type' => 'warning',
        'body' => 'per terminare chiudere la finestra',
    ]);
    ?>
    <?php //
    echo $form->field($model, 'nota')->textarea(['rows' => 5])->label('Nota');

    ?>

    <?PHP //$form->field($model, 'estensione')->textInput(['maxlength' => true]) ?>
    <?php echo $form->field($model, 'files[]')->
        fileInput(['multiple' => true])->label('segli File') ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>

<script>
    $(document).ready(function() {
        // Intercezione dell'evento submit della form

        $('#myFormId').on('submit', function(e) {
            // Evitare il comportamento predefinito di submit della form
            e.preventDefault();
            alert('intercettato');
            console.log('Form submit intercettato');
            // Serializzazione dei dati della form
            var formData = $(this).serialize();
            console.log('Dati della form:', formData);
            // Invio della richiesta AJAX al server
            $.ajax({
                url: '/index.php?r=folders/ajupd', // URL dell'azione nel controller
                type: 'POST', // Metodo HTTP della richiesta
                data: formData, // Dati della form serializzati
                success: function(response) {
                    // Gestione della risposta dal server
                    console.log('Risposta dal serverok :', response); // Puoi fare il logging della risposta o fare altre azioni
                    // Chiudi la modale o esegui altre azioni necessarie
                    // Mostriamo il messaggio flash se presente
                    if (response.success) {
                        var message = response.success;
                        $('#success-message').html(message); // Inseriamo il messaggio flash nella vista
                    }
                },
                error: function(xhr, status, error) {
                    // Gestione degli errori durante la richiesta AJAX
                    console.error('Risposta dal ERRORE :', error); // Puoi fare il logging degli errori o fare altre azioni
                }
            });
        });
    });
</script>

<!-- Mostra il messaggio flash -->
<?php if (Yii::$app->session->hasFlash('success')) : ?>
    <?= Alert::widget([
        'options' => [
            'class' => 'alert-success',
        ],
        'body' => Yii::$app->session->getFlash('success'),
    ]) ?>
<?php endif; ?>
