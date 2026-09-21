<?php

use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInput;
use yii\helpers\Html;
use kartik\select2\Select2;
use   app\models\Uecprovincia;
use yii\helpers\ArrayHelper;
use app\models\Uecarticoli;
use app\models\Uecanagrafica;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
/* @var $this yii\web\View */
/* @var $modelTesta app\models\Uectesta */
/* @var $modelsRighe app\models\Uecrighe[] */

$zdata = Uecarticoli::find()
    ->select(['codice', "descrizione"])
    ->orderBy(['descrizione' => SORT_ASC])
    ->asArray()
    ->all();

$zmap = array_column($zdata, 'descrizione', 'codice');

$zcli = Uecanagrafica::find()
    ->select(['id', "CONCAT(nome,' ' ,cognome)  as descrizione "])
    ->asArray()
    ->all();
$zclimap = array_column($zcli, 'descrizione', 'id');
$form = ActiveForm::begin(['id' => 'uectesta']);

$tipopag = ['Carta' => 'Carta', 'Contanti' => 'Contanti', 'Assegno' => 'Assegno', 'Bancomat' => 'Bancomat'];

$this->title = "Crea";
?>

<?= $form->field($model, 'data')->textInput(['value' => date('Y-m-d')]) ?>
<?= $form->field($model, 'numero')->textInput(['readonly' => false, 'value' => $tnum]) ?>
<?= $form->field($model, 'cliente')->widget(Select2::classname(), [
    'data' => $zclimap,
    'size' => 'lg',
    'options' => ['placeholder' => 'Seleziona Cliente ...'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]); ?>
<?= $form->field($model, 'tipopag')->widget(Select2::classname(), [
    'data' => $tipopag,
    'size' => 'lg',
    'options' => ['placeholder' => 'Seleziona Metodo ...'],
    'pluginOptions' => [
        'allowClear' => true
    ],
]); ?>

<?= $form->field($model, 'esportato')->checkbox() ?>

<?= $form->field($model, 'righe')->widget(MultipleInput::class, [
    'data' => $items,  // Usa 'data' invece di 'models'
          'addButtonPosition'   => [

                MultipleInput::POS_ROW, MultipleInput::POS_FOOTER
          ],
    'addButtonOptions'    => [
        'class' => 'btn btn-success',
        'label' => 'Aggiungi', // also you can use html code
    ],
    'removeButtonOptions' => [
        'label' => 'Rimuovi',
        'class' => 'btn btn-danger',
    ],
    'columns' => [
        [
            'name' => 'articolo',
            'type' => Select2::class,
            'title' => 'Articolo',
            'options' => [
                'data' => $zmap,
                'options' => ['placeholder' => 'Seleziona un articolo...'],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ],
        ],




        [
            'name' => 'nota',
            'type' => 'textInput',
            'title' => 'Nota',
        ],
        [
            'name' => 'qta',
            'type' => 'textInput',
            'title' => 'Qta',
        ],
        [
            'name' => 'prezzo',
            'type' => 'textInput',
            'title' => 'Prezzo',
        ],
    ]
]) ?>

<div class="form-group">
    <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>


<?php
$this->registerJs("
    $(document).on('change', '.articolo-select', function() {
        var articoloCodice = $(this).val();
        var descrizioni = " . json_encode($zmap) . ";
        
        // Trova il campo 'nota' nella stessa riga
        var riga = $(this).closest('tr');
        var notaInput = riga.find('.nota-input');

        // Aggiorna il valore della 'nota' con la descrizione dell'articolo
        if (descrizioni[articoloCodice]) {
            notaInput.val(descrizioni[articoloCodice]);
        } else {
            notaInput.val('');
        }
    });
");
?>