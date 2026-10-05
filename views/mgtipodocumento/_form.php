<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\models\MgTipoDocumento;

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoDocumento */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="mgtipodocumento-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'destinazione')->dropDownList(MgTipoDocumento::opzioniDestinazione()) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'anno')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'contatore')->textInput(['type' => 'number']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'usa_progressivo')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'congruita')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'crea_scadenze')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'attivo')->checkbox() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'mostra_varianti')->checkbox() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'preleva_rapportini')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'crea_articoli')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'crea_anagrafiche')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'mostra_matrice')->checkbox() ?>
        </div>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <strong>Destinazione:</strong> indica se il documento è emesso verso un <strong>cliente</strong> o un
        <strong>fornitore</strong>; nella form documento l'intestatario sarà filtrato di conseguenza.
        <br>
        <strong>Crea scadenze:</strong> se attivo, alla creazione del documento di questo tipo vengono generate le
        scadenze in base al metodo di pagamento (numero rate e percentuali definite a tabella).
        <br>
        <strong>Mostra taglia/colore:</strong> se attivo, nelle righe dei documenti di questo tipo vengono mostrate le
        colonne Taglia e Colore (compilate dall'articolo selezionato).
        <br>
        <strong>Preleva rapportini:</strong> se attivo, nella form documento è disponibile il pulsante per prelevare
        ed evadere i rapportini (solo per documenti in bozza).
        <br>
        <strong>Crea articoli:</strong> se attivo, nella form documento è disponibile la creazione rapida di un nuovo
        articolo.
        <br>
        <strong>Crea anagrafiche:</strong> se attivo, nella form documento è disponibile la creazione rapida di un
        nuovo cliente/fornitore.
        <br>
        <strong>Matrice taglie:</strong> se attivo, nella form documento è disponibile il pulsante per generare le
        righe dalla matrice taglie del modello.
        <br>
        <strong>Proposta congruità numeri:</strong> se attiva, non è possibile creare un documento con numero più alto
        per una data precedente (es. il 101 non può essere datato prima del 100). Il contatore indica l'ultimo numero usato.
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
