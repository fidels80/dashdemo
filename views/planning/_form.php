<?php
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\models\Personale;
use app\models\Veicoli;
use app\models\Sottocommessa;
use app\models\CF;
use app\models\DittaEsterna;

// 1. CAPISCE IL CONTESTO DI CARICAMENTO
$isAjax = Yii::$app->request->isAjax;

// 2. CONFIGURA SELECT2 DI CONSEGUENZA
$select2Options = ['allowClear' => true];
if ($isAjax) {
    // Applica il fix per la modale solo se siamo in AJAX
    $select2Options['dropdownParent'] = '#modal-create'; 
}
$commessedisponibili = Sottocommessa::find()->select(['Cd_DOSottoCommessa', 
'Descrizione'])->orderBy('Descrizione')->asArray()->all();
yii::warning($commessedisponibili);
?>

<div class="planning-form">
    <?php $form = ActiveForm::begin([
        'id' => 'planning-form-dynamic',
        'enableClientValidation' => true,
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fa fa-clock"></i> Tempi e Luogo</h5>
       <?= $form->field($model, 'data_attivita')->textInput([
    'type' => 'date', 
    'onkeydown' => 'return false;', 
    'onpaste' => 'return false;',
    'style' => 'cursor: pointer;' // Indica all'utente che è cliccabile
]) ?>      <div class="row">
                <div class="col-6"><?= $form->field($model, 'ora_inizio')->textInput(['type' => 'time']) ?></div>
                <div class="col-6"><?= $form->field($model, 'ora_fine')->textInput(['type' => 'time']) ?></div>
            </div>
    
<?= $form->field($model, 'giro')->widget(\kartik\select2\Select2::class, [
    // Crea l'elenco da 1 a 24 sia come chiave che come valore
    'data' => array_combine(range(1, 24), range(1, 24)), 
    'options' => [
        'placeholder' => 'Seleziona ordine giro...', 
        'id' => 'f-giro'
    ],
    'pluginOptions' => [
        'allowClear' => true,
        // Usiamo la variabile che abbiamo definito all'inizio della form
        // per far sì che la tendina si apra sopra la modale
        'dropdownParent' => $isAjax ? '#modal-create' : null, 
    ],
]); ?>
 
            <?= $form->field($model, 'cd_cf')->widget(Select2::class, [
                'data' => ArrayHelper::map(CF::find()->orderBy('Descrizione')->all(), 'Cd_CF', 'Descrizione'),
                'options' => ['placeholder' => 'Seleziona Cliente...', 'id' => 'f-cliente'],
                'pluginOptions' => $select2Options, // <-- Usiamo la variabile dinamica
            ])->label('Cliente') ?>
                      <?= $form->field($model, 'cd_dosottocommessa')->widget(Select2::class, [
                'data' => ArrayHelper::map(Sottocommessa::find()->orderBy('Descrizione')
                ->all(), 'Cd_DoSottocommessa', 'Cd_DOSottoCommessa'),
                'options' => ['placeholder' => 'Seleziona Commessa...', 'cd_dosottocommessa' => 'f-commessa'],
                'pluginOptions' => $select2Options, // <-- Usiamo la variabile dinamica
            ])->label('Commessa') ?> 
            
            

            
            <?= $form->field($model, 'indirizzo')->textInput(['placeholder' => 'Luogo attività...']) ?>
        </div>

        <div class="col-md-4 border-start border-end">
            <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fa fa-users-cog"></i> Risorse</h5>
            
            <?= $form->field($model, 'ditta_esterna')->widget(Select2::class, [
                'data' => ArrayHelper::map(DittaEsterna::find()->all(), 'codice', 'descrizione'),
                'options' => ['placeholder' => 'Nessuna Ditta', 'id' => 'f-ditta'],
                'pluginOptions' => $select2Options,
            ]) ?>

            <?= $form->field($model, 'personale_ids')->widget(Select2::class, [
                'data' => ArrayHelper::map(Personale::find()->where(['stato_attivo' => 1])->all(), 'id', function($p){ return $p->cognome.' '.$p->nome; }),
                'options' => ['multiple' => true, 'placeholder' => 'Dipendenti...', 'id' => 'f-personale'],
                'pluginOptions' => $select2Options,
            ]) ?>

            <?= $form->field($model, 'qta_operai')->textInput(['type' => 'number', 'min' => 0]) ?>

<?= $form->field($model, 'veicoli_ids')->widget(Select2::class, [
                'data' => ArrayHelper::map(Veicoli::find()->where("UPPER(stato_veicolo) = 'DISPONIBILE'")->all(), 'id', function($v){ return $v->targa . ' - ' . $v->marca_modello; }),
                'options' => [
                    'multiple' => true, // <-- ABILITA LA SELEZIONE MULTIPLA
                    'placeholder' => 'Seleziona Mezzi...',
                    'id' => 'f-veicoli'
                ],
                'pluginOptions' => $select2Options,
            ])->label('Veicoli') ?>
        </div>

        <div class="col-md-4">
            <h5 class="text-primary border-bottom pb-2 mb-3"><i class="fa fa-tasks"></i> Dettagli</h5>
            <?= $form->field($model, 'stato_completamento')->dropDownList(['Da Iniziare'=>'Da Iniziare','In Corso'=>'In Corso','Completato'=>'Completato']) ?>
            <?= $form->field($model, 'descrizione')->textarea(['rows' => 8]) ?>
        </div>
    </div>

    <div class="text-end border-top pt-3">
        <?php if ($isAjax): ?>
   <div class="btn btn-secondary" data-bs-dismiss="modal">Premi Esc per Uscire</div>
        <?php else: ?>
            <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
        <?php endif; ?>
        
        <?= Html::submitButton('<i class="fa fa-save"></i> Salva Impegno', ['class' => 'btn btn-success px-4']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
// JAVASCRIPT REGISTRATO CORRETTAMENTE TRAMITE YII2
$js = <<<JS
    // Logica mutua esclusione nel form
    $('#f-ditta').on('change', function() {
        if ($(this).val()) {
            $('#f-personale').val(null).trigger('change').prop('disabled', true);
        } else {
            $('#f-personale').prop('disabled', false);
        }
    });

    $('#f-personale').on('change', function() {
        if ($(this).val() && $(this).val().length > 0) {
            $('#f-ditta').val(null).trigger('change').prop('disabled', true);
        } else {
            $('#f-ditta').prop('disabled', false);
        }
    });
JS;

//$this->registerJs($js);
?>