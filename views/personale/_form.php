<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Ruoli;
use app\models\Reparti;
use app\models\Mansioni;

$this->registerCss("
    .personale-form label { font-size: 1.1rem; font-weight: 600; margin-bottom: 8px; color: #333; }
    .personale-form .form-control { font-size: 1.1rem; border-radius: 0.5rem; }
    .section-title { color: #0d6efd; border-bottom: 2px solid #e9ecef; padding-bottom: 8px; margin-top: 25px; margin-bottom: 20px; font-weight: bold; }
    .btn-salva { padding: 12px 40px; font-size: 1.2rem; font-weight: bold; }
");
?>

<div class="personale-form card p-4 shadow-sm border-0">

    <?php $form = ActiveForm::begin(['fieldConfig' => ['options' => ['class' => 'form-group mb-3']]]); ?>

    <h4 class="section-title"><i class="fa fa-id-card"></i> Anagrafica e Stato Contrattuale</h4>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'nome')->textInput(['placeholder' => 'Nome']) ?>
            <?= $form->field($model, 'luogo_data_nascita')->textInput(['placeholder' => 'Es: Roma, 01/01/1980']) ?>
            <?= $form->field($model, 'data_assunzione')->textInput(['type' => 'date']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'cognome')->textInput(['placeholder' => 'Cognome']) ?>
            <?= $form->field($model, 'codice_fiscale')
            ->textInput(['style' => 'text-transform: uppercase;'])->label('Documento') ?>
            <?= $form->field($model, 'tipo_contratto')->dropDownList([
                'Indeterminato' => 'Indeterminato',
                'Determinato' => 'Determinato',
                'Apprendistato' => 'Apprendistato',
                'Co.Co.Co' => 'Co.Co.Co',
                'Partita IVA' => 'Partita IVA',
            ], ['prompt' => 'Seleziona...']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'tariffa_oraria')->textInput(['type' => 'number', 'step' => '0.01'])->label('Tariffa Oraria (€/h)') ?>
            <?= $form->field($model, 'data_inserimento')->textInput([
                'readonly' => true,
                'class' => 'form-control bg-light',
                'value' => $model->isNewRecord ? date('d/m/Y H:i') : $model->data_inserimento
            ]) ?>
            <?= $form->field($model, 'stato_attivo')->checkbox(['style' => 'transform: scale(1.3); margin-top:10px'])->label('Dipendente in Forza') ?>
        </div>
    </div>

    <h4 class="section-title"><i class="fa fa-briefcase"></i> Inquadramento Aziendale</h4>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'reparto')->dropDownList(ArrayHelper::map(Reparti::find()->all(), 'codice', 'descrizione'), ['prompt' => 'Seleziona...']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'ruolo')->dropDownList(ArrayHelper::map(Ruoli::find()->all(), 'codice', 'descrizione'), ['prompt' => 'Seleziona...']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'mansione')->dropDownList(ArrayHelper::map(Mansioni::find()->all(), 'codice', 'descrizione'), ['prompt' => 'Seleziona...']) ?>
        </div>
    </div>

    <h4 class="section-title"><i class="fa fa-phone"></i> Contatti e Recapiti</h4>
    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'cellulare')->textInput() ?></div>
        <div class="col-md-3"><?= $form->field($model, 'email')->textInput(['type' => 'email']) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'indirizzo')->textInput() ?></div>
        <div class="col-md-2"><?= $form->field($model, 'citta')->textInput() ?></div>
    </div>

    <div class="form-group text-end mt-4">
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-outline-secondary btn-salva me-2']) ?>
        <?= Html::submitButton('<i class="fa fa-save"></i> Salva Anagrafica', ['class' => 'btn btn-success btn-salva shadow']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>