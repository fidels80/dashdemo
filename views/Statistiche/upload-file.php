<style>
    /* CSS per Select2 nelle modali */
    .modal {
        overflow: visible !important;
    }

    .select2-container--open {
        z-index: 1060 !important;
    }
    
    .select2-dropdown {
        z-index: 1060 !important;
    }
    
    .DepDrop-dropdown {
        z-index: 1060 !important;
    }
    
    .select2-container {
        width: 100% !important;
    }
</style>

<script>
// SOLUZIONE: Permetti al focus di entrare nei dropdown Select2
$.fn.modal.Constructor.prototype._enforceFocus = function() {};

// Oppure, soluzione più moderna e specifica:
$(document).on('shown.bs.modal', '.modal', function() {
    // Rimuovi l'enforce focus solo per questa modale
    $(this).removeAttr('tabindex');
});

// Assicurati che i dropdown Select2 si aprano correttamente
$(document).ready(function() {
    $('.select2-container').each(function() {
        $(this).css('z-index', 1060);
    });
});
</script>

<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Agente;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
/** @var yii\web\View $this */
/** @var app\models\Agentifiles $model */
$agenti = ArrayHelper::map(
    Agente::find()->select(['Cd_Agente', 'Descrizione'])->orderBy('Cd_Agente')->all(),
    'Cd_Agente',
    function($model) {
        return $model->Cd_Agente . ' - ' . $model->Descrizione;
    }
);


$cartelle = [
    ['cartella' => 'Retribuzioni', 'cartella_padre' => null],
    ['cartella' => 'C1', 'cartella_padre' => 'Retribuzioni'],
    ['cartella' => 'Buste paga', 'cartella_padre' => 'Retribuzioni'],
    ['cartella' => 'Cud', 'cartella_padre' => 'Retribuzioni'],
    
    ['cartella' => 'Documenti Personali', 'cartella_padre' => null],
    ['cartella' => 'Carta Identità', 'cartella_padre' => 'Documenti Personali'],
    ['cartella' => 'Passaporto', 'cartella_padre' => 'Carta Identità'],
    ['cartella' => 'Tessera Sanitaria', 'cartella_padre' => 'Carta Identità'],
    
    ['cartella' => 'Documentazione', 'cartella_padre' => null],
    ['cartella' => 'Contratti', 'cartella_padre' => 'Documentazione'],
    ['cartella' => 'Attestati', 'cartella_padre' => 'Documentazione'],
    ['cartella' => 'Dpi', 'cartella_padre' => 'Documentazione'],
    ['cartella' => 'Visite Mediche', 'cartella_padre' => 'Documentazione']
];
/*
     'Retribuzioni' => [
                'C1' => ['2025','2026','2027','2028','2029','2030','2031','2032','2033','2034','2035'],    
                'Buste paga' => ['2025','2026','2027','2028','2029','2030','2031','2032','2033','2034','2035'],
                'Cud' => ['2025','2026','2027','2028','2029','2030','2031','2032','2033','2034','2035']
            ],
            'Documenti Personali' => [
                'Carta Identità' => [],
                'Passaporto' => [],
                'Tessera Sanitaria' => []
            ],
            'Documentazione' => [
                'Contratti' => [],
                'Attestati' => [],
                'Dpi' => [],
                'Visite Mediche' => []
            ],
*/
 /*
        'id' => 'xroomlist-form',
        'action' => ['xroomlist/createaj', 'th_id' => $model->th_id],
        'enableAjaxValidation' => false,
 */
// mapping per Select2
$listaCartelle = ArrayHelper::map($cartelle, 'cartella', 'cartella');
$listaPadri = ArrayHelper::map($cartelle, 'cartella_padre', 'cartella_padre');

//$this->title = 'Carica File Agente';
?>

<div class="agentifiles-upload container mt-4">
    <h3><?= Html::encode($this->title) ?></h3>

    <?php $form = ActiveForm::begin([
         'id' => 'uploadf-form',
        'options' => ['enctype' => 'multipart/form-data'],
        'enableAjaxValidation' => false,
 
    ]); ?>

    <?= $form->field($model, 'cd_agente')->widget(Select2::classname(), [
    'data' => $agenti,
        'options' => [
            'placeholder' => 'Seleziona agente...',
            'id' => 'age-select',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>


    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nota')->textarea(['rows' => 3]) ?>


<?= $form->field($model, 'cartella_padre')->widget(Select2::classname(), [
    'data' => [
   'Retribuzioni'=>  'Retribuzioni',
    'C1'=>'C1',
    'Buste paga'=>'Buste paga',
    'Cud'=>'Cud',
     'Documenti Personali'=>'Documenti Personali',
     'Carta Identità'=>'Carta Identità',
     'Passaporto'=>'Passaporto',
     'Tessera Sanitaria'=>'Tessera Sanitaria',
     'Documentazione'=>'Documentazione',
     'Contratti'=>'Contratti',
    'Attestati' =>'Attestati',
    'Dpi'=>'Dpi',
    'Visite Mediche'=>'Visite Mediche'
    ],
    'options' => ['id' => 'cartella-padre', 'placeholder' => 'Seleziona cartella padre...'],
    'pluginOptions' => ['allowClear' => true],
]) ?>

<?= $form->field($model, 'cartella')->widget(DepDrop::classname(), [
    'options' => ['id' => 'cartella'],
    'pluginOptions' => [
        'depends' => ['cartella-padre'],
        'placeholder' => 'Seleziona cartella...',
        'url' => Url::to(['/statistiche/cartelle'])
    ]
]) ?>


    <?= $form->field($model, 'data_scadenza')->input('date') ?>

    <?= $form->field($model, 'uplfile')->fileInput() ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('Carica', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['statistiche/index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
