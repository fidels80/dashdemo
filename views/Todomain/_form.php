<style>
    .align-items-center {
        align-items: center;
    }

    .custom-height {
        height: 38px;
    }

    .custom-align {
        margin-bottom: -6px;
        /* Adjust this value as needed */
    }
</style>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap4\Modal;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
use kartik\select2\Select2;
use app\models\todostato;
use app\models\todopriorita;
use app\models\anacli;
use app\models\user;
use app\models\todogruppi;
use app\models\Todorelgrpusr;
use app\models\Todomain;
use app\models\Todotag;

$tags = Todotag::find()
    ->select(['Tag as id', 'Tag as Name'])
    ->asArray()
    ->all();
$tagList = ArrayHelper::map($tags, 'id', 'Name');
yii::warning($tagList);

$tgrp = todogruppi::find()
    ->select(['id', 'gruppo as Name'])
    ->asArray()
    ->all();
$gruppo = ArrayHelper::map($tgrp, 'id', 'Name');

$us = user::find()
    ->select(['id', 'username as Name'])
    ->asArray()
    ->all();
$xuser = ArrayHelper::map($us, 'id', 'Name');

// Clienti presi dall'anagrafica del microgestionale (mg_anagrafica)
$clifor = \app\models\MgAnagrafica::mapClienti();

$xpriorita = todopriorita::find()
    ->select(['id', 'priorita as Name'])
    ->asArray()
    ->all();
$priorita = ArrayHelper::map($xpriorita, 'id', 'Name');

$xstato = todostato::find()
    ->select(['id', 'stato as Name'])
    ->asArray()
    ->all();
$stato = ArrayHelper::map($xstato, 'id', 'Name');

$tipiIssue = \app\models\Todotipo::map();
$sprintList = \app\models\Todosprint::map();

$percentuali = [];
for ($i = 0; $i <= 100; $i++) {
    $percentuali[] = [
        'id' => $i,
        'Name' => "$i%"
    ];
}
$Percentuali2 = ArrayHelper::map($percentuali, 'id', 'Name');



$todomain = Todomain::find()
    ->select(['id', 'descrizione', 'user', 'group', 'stato'])
    ->asArray()
    ->all();


/* @var $this yii\web\View */
/* @var $model app\models\Todomain */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="todomain-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'user')->widget(Select2::classname(), [
                'data' => $xuser,
                'size' => 'lg',
                'options' => ['placeholder' => 'Seleziona utente ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'group')->widget(Select2::classname(), [
                'data' => $gruppo,
                'size' => 'lg',
                'options' => ['placeholder' => 'Seleziona gruppo ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);  ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'cd_cli')->widget(Select2::classname(), [
                'data' => $clifor,
                'size' => 'lg',
                'options' => ['placeholder' => 'Seleziona gruppo ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'priorita')->widget(Select2::classname(), [
                'data' => $priorita,
                'size' => 'lg',
                'options' => ['placeholder' => 'Seleziona priorità ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'tipo')->widget(Select2::classname(), [
                'data' => $tipiIssue,
                'size' => 'lg',
                'options' => ['placeholder' => 'Seleziona tipo issue ...'],
                'pluginOptions' => ['allowClear' => true],
            ]); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'story_points')->textInput(['type' => 'number', 'min' => 0]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'sprint_id')->widget(Select2::classname(), [
                'data' => $sprintList,
                'size' => 'lg',
                'options' => ['placeholder' => 'Sprint (vuoto = backlog) ...'],
                'pluginOptions' => ['allowClear' => true],
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'progresso')->widget(Select2::classname(), [
                'data' => $Percentuali2,
                'size' => 'lg',
                'options' => ['placeholder' => 'Seleziona progresso ...'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'id_padre')->textInput(['maxlength' => true, 'readonly' => true]) ?>
        </div>
        <div class="d-flex align-items-center custom-align">
            <?= Html::button('Crea Nuovo', [
                'class' => 'btn btn-primary btn-block',
                'data-toggle' => 'modal',
                'data-target' => '#todo-modal'
            ]) ?>
        </div>
    </div>

    <?= $form->field($model, 'descrizione')->textarea(['rows' => 6, 'placeholder' => 'Inserisci descrizione ...']) ?>


    <?= $form->field($model, 'tagValues')->widget(Select2::classname(), [
        'options' => ['placeholder' => 'Seleziona tag ...', 'multiple' => true],
        'pluginOptions' => [
            'tags' => true,
            'tokenSeparators' => [','],
            'createTag' => new \yii\web\JsExpression('function(params) {
             if (params.term.length < 3) {
                return null; // Do not perform search if term is too short
            }
                return {
                    id: params.term,
                    text: params.term,
                    newTag: true // add additional parameters
                };
            }'),
            'ajax' => [
                'url' => Url::to(['todomain/search-tag']),
                'dataType' => 'json',
                'data' => new \yii\web\JsExpression('function(params) {
                    return {
                        q: params.term
                    };
                }'),
                'processResults' => new \yii\web\JsExpression('function(data) {
                    return {
                        results: data.results
                    };
                }'),
            ],
        ],
    ]); ?>






    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'data_inizio')->widget(DateTimePicker::classname(), [
                'size' => 'lg',
                'options' => ['placeholder' => 'Seleziona Data di Inizio ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd/mm/yyyy hh:ii'
                ]
            ]); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'data_fine')->widget(DateTimePicker::classname(), [
                'size' => 'lg',
                'options' => ['placeholder' => 'Seleziona Data di Fine ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd/mm/yyyy hh:ii'
                ]
            ]); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'data_scadenza')->widget(DateTimePicker::classname(), [
                'size' => 'lg',
                'options' => ['placeholder' => 'Seleziona Data di Scadenza ...'],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd/mm/yyyy hh:ii'
                ]
            ]); ?>
        </div>
    </div>

    <?= $form->field($model, 'stato')->widget(Select2::classname(), [
        'data' => $stato,
        'size' => 'lg',
        'options' => ['placeholder' => 'Seleziona stato ...'],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
Modal::begin([
    'title' => '<h4>Seleziona Todo</h4>',
    'id' => 'todo-modal',
    'size' => 'modal-lg',
]);
?>

<div class="form-group">
    <input type="text" id="todo-search" class="form-control" placeholder="Cerca...">
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Descrizione</th>
            <th>Utente</th>
            <th>Gruppo</th>
            <th>Stato</th>
        </tr>
    </thead>
    <tbody id="todo-table">
        <?php foreach ($todomain as $todo) : ?>
            <tr class="todo-item" data-id="<?= $todo['id'] ?>">
                <td><?= $todo['id'] ?></td>
                <td><?= $todo['descrizione'] ?></td>
                <td><?= $todo['user'] ?></td>
                <td><?= $todo['group'] ?></td>
                <td><?= $todo['stato'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php Modal::end(); ?>

<script>
    // Funzione per gestire il click su un todo-item
    $(document).on('click', '.todo-item', function() {
        var todoId = $(this).data('id');
        $('#todomain-id_padre').val(todoId);
        $('#todo-modal').modal('hide');
    });

    // Funzione per la ricerca dinamica
    $('#todo-search').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('#todo-table tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
</script>