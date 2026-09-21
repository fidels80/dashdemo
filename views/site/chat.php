<?php
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

$this->title = 'Chat';

/* Recupera la chiave API dal parametro */
$api_key = Yii::$app->params['chatgptApiKey'];

/* Registra lo script JavaScript per gestire la chat */
$this->registerJs("
    $(document).ready(function() {
        $('.chat-form').submit(function(e) {
            e.preventDefault();

            var message = $('#chatform-message').val();
            var apiKey = $('#chatform-api_key').val();

            $.ajax({
                url: '/site/chat',
                type: 'POST',
                data: { message: message, api_key: apiKey },
                success: function(response) {
                    var answer = response.answer;
                    $('.chat-messages').append('<div class=\"chat-message\"><div class=\"chat-bubble\">' + answer + '</div></div>');
                    $('#chatform-message').val('');
                },
                error: function() {
                    alert('Si è verificato un errore durante l\'invio del messaggio.');
                }
            });
        });
    });
", View::POS_END);
?>

<div class="chat-container">
    <h1><?=Html::encode($this->title)?></h1>

    <div class="chat-messages">
        <!-- Messaggi di chat qui -->
    </div>

    <?php $form = ActiveForm::begin(['class' => 'chat-form']);?>
        <?=$form->field($model, 'message')->textInput(['autofocus' => true])->label(false)?>
        <?=Html::hiddenInput('api_key', $api_key, ['id' => 'chatform-api_key'])?>
        <div class="form-group">
            <?=Html::submitButton('Invia', ['class' => 'btn btn-primary'])?>
        </div>
    <?php ActiveForm::end();?>
</div>
