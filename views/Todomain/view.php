<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap4\Alert;
use yii\widgets\ActiveForm;
use yii\bootstrap4\Modal;
use dosamigos\ckeditor\CKEditor;
use app\models\Todocommenti;

/* @var $this yii\web\View */
/* @var $model app\models\Todomain */

$this->title = $model->descrizione;
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
                'confirm' => Yii::t('app', 'sei sicuro di voler mandare la mail di notifica?'),
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
                    'user' => [
                        'attribute' => 'user',
                        'value' => fn ($model) => $model->userdett->username ?? 'N/A',
                    ],
                    'group' => [
                        'attribute' => 'group',
                        'value' => fn ($model) => $model->groupdett->gruppo ?? 'N/A',
                    ],
                    'cd_cli' => [
                        'attribute' => 'cd_cli',
                        'value' => fn ($model) => $model->clidett->ragione_sociale ?? 'N/A',
                    ],
                    'priorita' => [
                        'attribute' => 'priorita',
                        'value' => fn ($model) => $model->priodett->priorita ?? 'N/A',
                    ],
                    'tipo' => [
                        'attribute' => 'tipo',
                        'value' => fn ($model) => $model->tipodett->tipo ?? 'N/A',
                    ],
                    'story_points',
                    'sprint_id' => [
                        'attribute' => 'sprint_id',
                        'value' => fn ($model) => $model->sprintdett->nome ?? 'Backlog',
                    ],
                    'reporter',
                    'progresso',
                    'id_padre',
                    'descrizione',
                    'data_inizio',
                    'data_fine',
                    'data_scadenza',
                    'created_at',
                    'updated_at',
                    'stato' => [
                        'attribute' => 'stato',
                        'value' => fn ($model) => $model->statodett->stato ?? 'N/A',
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

                <?= $form->field($commentModel, 'commento')->widget(CKEditor::class, [
                    'options' => ['rows' => 3],
                    'preset' => 'basic',
                    'clientOptions' => [
                        'allowedContent' => true,
                        'extraAllowedContent' => 'p strong em u; a[!href,target]',
                    ],
                ])->label(false) ?>
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
        e.preventDefault();
        var form = $(this);
        var submitButton = form.find('button[type="submit"]');
        submitButton.prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            method: form.attr('method'),
            data: form.serialize(),
            success: function(response) {
                $('#comments-list').html(response);
                initReplyHandlers();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('Errore AJAX:', textStatus, errorThrown);
                alert('Errore durante il salvataggio del commento. Dettagli nell\'console.');
                submitButton.prop('disabled', false);
            }
        });
    });

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

    initReplyHandlers();

    $('a[data-confirm]').on('click', function(e) {
        e.preventDefault();
        $('#confirm-modal').modal('show')
            .find('.modal-body')
            .load($(this).attr('href'));
    });

    JS;
    $this->registerJs($script);
    ?>
</div>

<style>
    .todomain-view {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    .card-header {
        background-color: #007bff;
        color: white;
        font-size: 1.2em;
    }

    .card-body {
        background-color: #f8f9fa;
    }

    .alert {
        margin-top: 20px;
    }

    .btn {
        margin: 5px;
    }

    #comments-list {
        border-top: 1px solid #e9ecef;
        padding-top: 15px;
    }

    #comment-form {
        margin-bottom: 20px;
    }
</style>