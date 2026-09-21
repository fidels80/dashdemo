<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Todocommenti;
use dosamigos\ckeditor\CKEditor;

$newCommentModel = new Todocommenti();

if (!isset($comments)) {
    $comments = [];
}

// Ordina i commenti per data una sola volta prima di passare i dati alla funzione renderComments
usort($comments, function ($a, $b) {
    return strtotime($b->data) - strtotime($a->data);
});
/* @var $comments app\models\Todocommenti[] */
// Funzione ricorsiva per visualizzare i commenti e le risposte
function renderComments($comments, $newCommentModel,$parentId = null)
{
    echo '<ul class="list-group">';
    foreach ($comments as $comment) {
        if ($comment->id_commento == $parentId) {
            echo '<li class="list-group-item">';
            echo '<strong>' . Html::encode($comment->user) . '</strong>: ' . $comment->commento; // Use $comment->commento directly
            echo '<br><small>' . Html::encode($comment->data) . '</small>';
            echo '<br><button class="btn btn-sm btn-primary reply-btn" data-comment-id="' . $comment->id . '">Rispondi</button>';
            echo '<div class="reply-form" id="reply-form-' . $comment->id . '" style="display: none;">';
            $form = ActiveForm::begin([
                'id' => 'reply-form-' . $comment->id,
                'action' => ['todomain/addreply', 'id_todo' => $comment->id_todo, 'id_commento' => $comment->id],
                'method' => 'post'
            ]);
            echo $form->field($newCommentModel, 'user')->hiddenInput(['value' => Yii::$app->user->identity->username ?? null])->label(false);
            echo $form->field($newCommentModel, 'parent_id')->hiddenInput(['value' => $comment->id])->label(false);
            echo $form->field($newCommentModel, 'commento')->widget(CKEditor::class, [
                'options' => ['rows' => 3],
                'preset' => 'basic',
                'clientOptions' => [
                    'allowedContent' => true,
                    'extraAllowedContent' => 'p strong em u; a[!href,target]',
                ],
            ])->label(false);
            echo '<div class="form-group">';
            echo Html::submitButton('Salva', ['class' => 'btn btn-primary save-reply-btn', 'data-comment-id' => $comment->id]);
            echo '</div>';
            ActiveForm::end();
            echo '</div>';
            // Richiamo ricorsivo per visualizzare le risposte
            renderComments($comments, $comment->id, $newCommentModel);
            echo '</li>';
        }
    }
    echo '</ul>';
}

// Render dei commenti principali (parent_id null)
renderComments($comments, null, $newCommentModel);

?>

<script>
    $(document).ready(function() {
        // Handler per il click su "Rispondi"
        $('.reply-btn').click(function() {
            var commentId = $(this).data('comment-id');
            $('#reply-form-' + commentId).toggle();

            // Inizializza o distruggi CKEditor in base alla visibilità del form
            if ($('#reply-form-' + commentId).is(':visible')) {
                CKEDITOR.replace($('#reply-form-' + commentId).find('textarea')[0].id);
            } else {
                CKEDITOR.instances[$('#reply-form-' + commentId).find('textarea')[0].id].destroy();
            }
        });

        // Handler per il submit dinamico dei form di risposta
        $(document).on('submit', 'form[id^="reply-form-"]', function(e) {
            e.preventDefault(); // Previeni il submit normale del form
            var form = $(this);
            var submitButton = form.find('button[type="submit"]');

            // Disabilita il pulsante di submit per evitare invii multipli
            submitButton.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    // Aggiorna la sezione dei commenti
                    $('#comments-section').html(response);

                    // Re-inizializza gli handler dopo l'aggiornamento
                    initReplyHandlers();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Errore AJAX:', textStatus, errorThrown); // Log degli errori
                    alert('Errore durante il salvataggio del commento. Dettagli nell\'console.');
                    submitButton.prop('disabled', false);
                }
            });
        });

        // Funzione per inizializzare o re-inizializzare gli handler di risposta
        function initReplyHandlers() {
            $('.reply-btn').off('click').on('click', function() {
                var commentId = $(this).data('comment-id');
                $('#reply-form-' + commentId).toggle();

                // Inizializza o distruggi CKEditor in base alla visibilità del form
                if ($('#reply-form-' + commentId).is(':visible')) {
                    CKEDITOR.replace($('#reply-form-' + commentId).find('textarea')[0].id);
                } else {
                    CKEDITOR.instances[$('#reply-form-' + commentId).find('textarea')[0].id].destroy();
                }
            });

            $(document).off('submit', 'form[id^="reply-form-"]').on('submit', 'form[id^="reply-form-"]', function(e) {
                e.preventDefault();
                var form = $(this);
                var submitButton = form.find('button[type="submit"]');

                submitButton.prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: form.attr('method'),
                    data: form.serialize(),
                    success: function(response) {
                        $('#comments-section').html(response);
                        initReplyHandlers();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Errore AJAX:', textStatus, errorThrown);
                        alert('Errore durante il salvataggio del commento. Dettagli nell\'console.');
                        submitButton.prop('disabled', false);
                    }
                });
            });
        }

        // Inizializza gli handler di risposta alla prima carica della pagina
        initReplyHandlers();
    });
</script>