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

    <div class="card mb-3">
        <div class="card-header">Dati generali</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'destinazione')->dropDownList(MgTipoDocumento::opzioniDestinazione()) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'attivo')->checkbox() ?>
                </div>
            </div>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i>
                <strong>Destinazione:</strong> indica se il documento è emesso verso un <strong>cliente</strong> o un
                <strong>fornitore</strong>; nella form documento l'intestatario sarà filtrato di conseguenza.
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Numerazione</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'anno')->textInput(['type' => 'number']) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'contatore')->textInput(['type' => 'number']) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'usa_progressivo')->checkbox() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'congruita')->checkbox() ?>
                </div>
            </div>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i>
                <strong>Proposta congruità numeri:</strong> se attiva, non è possibile creare un documento con numero
                più alto per una data precedente (es. il 101 non può essere datato prima del 100).
                <br>
                <strong>Contatore:</strong> ultimo numero usato; viene aggiornato automaticamente quando si numerano
                i documenti. <strong>Anno:</strong> anno di riferimento dell'ultima numerazione.
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Funzionalità nella form documento</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'crea_scadenze')->checkbox() ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'mostra_varianti')->checkbox() ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'mostra_matrice')->checkbox() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'preleva_rapportini')->checkbox() ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'crea_articoli')->checkbox() ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'crea_anagrafiche')->checkbox() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'gestione_seriali')->checkbox() ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'gestione_data_consegna')->checkbox() ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'gestione_lotti')->checkbox() ?>
                </div>
            </div>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i>
                <strong>Crea scadenze:</strong> alla creazione del documento vengono generate le scadenze in base al
                metodo di pagamento (numero rate e percentuali definite a tabella).
                <br>
                <strong>Mostra taglia/colore:</strong> nelle righe del documento vengono mostrate le colonne Taglia e
                Colore (compilate dall'articolo selezionato).
                <br>
                <strong>Matrice taglie:</strong> rende disponibile il pulsante per generare le righe dalla matrice
                taglie del modello.
                <br>
                <strong>Preleva rapportini:</strong> rende disponibile il pulsante per prelevare ed evadere i
                rapportini (solo per documenti in bozza).
                <br>
                <strong>Crea articoli / Crea anagrafiche:</strong> rendono disponibile la creazione rapida di un
                nuovo articolo o di un nuovo cliente/fornitore.
                <br>
                <strong>Gestione seriali / matricole:</strong> abilita, per ogni riga, l'inserimento del numero di
                serie dei singoli pezzi (una riga di dettaglio per pezzo).
                <br>
                <strong>Gestione data consegna:</strong> abilita, per ogni riga, la gestione delle date di consegna
                anche diverse all'interno della stessa riga (es. 5 pezzi domani e 5 la settimana successiva).
                <br>
                <strong>Gestione lotti:</strong> abilita, per ogni riga, la selezione dei lotti dell'articolo e la
                creazione rapida di nuovi lotti (codice lotto, descrizione, data scadenza, nota). I lotti creati
                dalla riga vengono associati automaticamente all'articolo.
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fas fa-warehouse"></i> Movimento di magazzino</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'id_magazzino_partenza')->dropDownList(
                        \app\models\MgMagazzino::mapAttivi(),
                        ['prompt' => '— Nessuno —']
                    ) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'id_magazzino_arrivo')->dropDownList(
                        \app\models\MgMagazzino::mapAttivi(),
                        ['prompt' => '— Nessuno —']
                    ) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'segno_movimento')->dropDownList(MgTipoDocumento::opzioniSegnoMovimento()) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'varia_impegnato')->dropDownList(MgTipoDocumento::opzioniVariazione()) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'varia_ordinato')->dropDownList(MgTipoDocumento::opzioniVariazione()) ?>
                </div>
            </div>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i>
                <strong>Magazzino partenza / arrivo:</strong> se valorizzi solo la <strong>partenza</strong> il
                documento è un <em>uscita/scarico</em> (es. vendita); se valorizzi solo l'<strong>arrivo</strong> è
                un'<em>entrata/carico</em> (es. DDT da fornitore); se valorizzi <strong>entrambi</strong> è un
                <em>trasferimento interno</em>.
                <br>
                <strong>Segno movimento:</strong> <em>Carico (+)</em> aumenta la giacenza del magazzino indicato,
                <em>Scarico (-)</em> la diminuisce, <em>Nessun movimento</em> non tocca le giacenze. Va sempre
                indicato; in caso di trasferimento la partenza subisce − e l'arrivo + in automatico.
                <br>
                <strong>Varia impegnato / ordinato:</strong> se il documento deve aumentare o diminuire le quantità
                impegnate e/o ordinate sugli articoli, indipendentemente dal movimento fisico a magazzino.
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
