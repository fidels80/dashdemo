<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap4\Alert;
use yii\widgets\ActiveForm; // Importazione della classe ActiveForm
use app\models\Todocommenti;


/* @var $this yii\web\View */
/* @var $model app\models\Todomain */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Todomains'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="todomain-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('success')) : ?>
        <?= Alert::widget([
            'options' => ['class' => 'alert-success'],
            'body' => Yii::$app->session->getFlash('success'),
        ]) ?>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')) : ?>
        <?= Alert::widget([
            'options' => ['class' => 'alert-danger'],
            'body' => Yii::$app->session->getFlash('error'),
        ]) ?>
    <?php endif; ?>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a(Yii::t('app', 'Manda Reminder sul calendario'), ['send-event-email2', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to send this event email?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>


    <div class="card">
        <div class="card-header">
            <?= Html::encode($this->title) ?>
        </div>
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'user',
                        'value' => function ($model) {
                            return $model->userdett->username ?? 'N/A';
                        },
                    ],
                    [
                        'attribute' => 'group',
                        'value' => function ($model) {
                            return $model->groupdett->gruppo ?? 'N/A';
                        },
                    ],
                    [
                        'attribute' => 'cd_cli',
                        'value' => function ($model) {
                            return $model->clidett->Desk ?? 'N/A';
                        },
                    ],
                    [
                        'attribute' => 'priorita',
                        'value' => function ($model) {
                            return $model->priodett->priorita ?? 'N/A';
                        },
                    ],
                    'progresso',
                    'id_padre',
                    'descrizione',
                    'data_inizio',
                    'data_fine',
                    'data_scadenza',
                    [
                        'attribute' => 'stato',
                        'value' => function ($model) {
                            return $model->statodett->stato ?? 'N/A';
                        },
                    ],
                ],
            ]) ?>
        </div>
    </div>

    <div id="comments-section">
        <div class="card mt-4">
            <div class="card-header">
                <?= Yii::t('app', 'Comments') ?>
            </div>
            <div class="card-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['add-comment', 'id' => $model->id],
                    'options' => ['id' => 'comment-form'],
                ]); ?>

                <?= $form->field($commentModel, 'commento')->textarea(['rows' => 3])->label(false) ?>
                <?= Html::submitButton(Yii::t('app', 'Add Comment'), ['class' => 'btn btn-primary']) ?>

                <?php ActiveForm::end(); ?>

                <h5 class="mt-4"><?= Yii::t('app', 'Existing Comments') ?></h5>
                <div id="comments-list">
                    <?php
                    ob_start();
                    $comments = Todocommenti::find()->where(['id_todo' => $model->id])->all() ?: [];
                    $newCommentModel = new Todocommenti();
                    include('_comments.php');
                    ob_end_flush();
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
    $script = <<< JS
    $('#comment-form').on('submit', function (e) {
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
                $('#comments-list').html(response);
                // Ri-inizializza gli handler
                initReplyHandlers();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Errore AJAX:', textStatus, errorThrown); // Log degli errori
                alert('Errore durante il salvataggio del commento. Dettagli nell\'console.');
                submitButton.prop('disabled', false);
            }
        });
    });

    // Funzione per ri-inizializzare gli handler di risposta
    function initReplyHandlers() {
        $('.reply-btn').off('click').on('click', function() {
            var commentId = $(this).data('comment-id');
            $('#reply-form-' + commentId).toggle();
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

    // Inizializza gli handler di risposta alla prima carica
    initReplyHandlers();
    JS;
    $this->registerJs($script);
    ?>
</div>