<?php

use yii\helpers\Html;
use app\components\FatturaElettronica;

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoDocumento */

$this->title = $model->codice . ' - ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Tipi documento', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$siNo = function ($v) {
    return $v ? 'Sì' : 'No';
};
$campo = function ($label, $valore, $col = 'col-md-3') {
    echo '<div class="' . $col . ' mb-3">'
        . '<label class="text-muted small mb-0 d-block">' . Html::encode($label) . '</label>'
        . '<div class="font-weight-bold">' . ($valore === '' || $valore === null ? '<span class="text-muted">—</span>' : Html::encode($valore)) . '</div>'
        . '</div>';
};
?>
<div class="mgtipodocumento-view">

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
        <div>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Eliminare questo tipo documento?', 'method' => 'post'],
            ]) ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Dati tipo documento</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('ID', $model->id, 'col-md-2');
                $campo('Codice', $model->codice, 'col-md-3');
                $campo('Destinazione', $model->destinazioneLabel, 'col-md-3');
                $campo('Attivo', $siNo($model->attivo), 'col-md-2');
                ?>
            </div>
            <div class="row">
                <?php $campo('Descrizione', $model->descrizione, 'col-md-12'); ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Numerazione</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('Anno', $model->anno, 'col-md-3');
                $campo('Contatore', $model->contatore, 'col-md-3');
                $campo('Numerazione automatica', $siNo($model->usa_progressivo), 'col-md-3');
                $campo('Proposta congruità numeri', $siNo($model->congruita), 'col-md-3');
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Opzioni</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('Crea scadenze', $siNo($model->crea_scadenze), 'col-md-3');
                $campo('Mostra taglia/colore', $siNo($model->mostra_varianti), 'col-md-3');
                $campo('Preleva rapportini', $siNo($model->preleva_rapportini), 'col-md-3');
                $campo('Crea articoli', $siNo($model->crea_articoli), 'col-md-3');
                $campo('Crea anagrafiche', $siNo($model->crea_anagrafiche), 'col-md-3');
                $campo('Matrice taglie', $siNo($model->mostra_matrice), 'col-md-3');
                $campo('Gestione seriali / matricole', $siNo($model->gestione_seriali), 'col-md-3');
                $campo('Gestione data consegna', $siNo($model->gestione_data_consegna), 'col-md-3');
                $campo('Gestione lotti', $siNo($model->gestione_lotti), 'col-md-3');
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><i class="fas fa-file-invoice"></i> Documento elettronico</div>
        <div class="card-body">
            <div class="row">
                <?php
                $tipi = FatturaElettronica::opzioniTipoDocumento();
                $regimi = FatturaElettronica::opzioniRegimeFiscale();
                $esig = FatturaElettronica::opzioniEsigibilitaIva();
                $campo('Documento elettronico', $siNo($model->elettronico), 'col-md-3');
                $campo('Tipo documento SDI', $tipi[$model->fe_tipo_documento] ?? $model->fe_tipo_documento, 'col-md-5');
                $campo('Divisa', $model->fe_divisa, 'col-md-2');
                $campo('Codice destinatario default', $model->fe_codice_destinatario, 'col-md-2');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Regime fiscale', $regimi[$model->fe_regime_fiscale] ?? $model->fe_regime_fiscale, 'col-md-4');
                $campo('Causale', $model->fe_causale, 'col-md-8');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Esigibilità IVA', $esig[$model->fe_esigibilita_iva] ?? $model->fe_esigibilita_iva, 'col-md-3');
                $campo('Riferimento normativo', $model->fe_riferimento_normativo, 'col-md-9');
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Movimento di magazzino</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('Magazzino partenza', $model->magazzinoPartenzaLabel, 'col-md-4');
                $campo('Magazzino arrivo', $model->magazzinoArrivoLabel, 'col-md-4');
                $campo('Segno movimento', $model->segnoMovimentoLabel, 'col-md-4');
                ?>
            </div>
            <div class="row">
                <?php
                $campo('Varia impegnato', $model->variaImpegnatoLabel, 'col-md-4');
                $campo('Varia ordinato', $model->variaOrdinatoLabel, 'col-md-4');
                ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Info</div>
        <div class="card-body">
            <div class="row">
                <?php $campo('Creato il', $model->created_at ? date('d/m/Y H:i', strtotime($model->created_at)) : '', 'col-md-4'); ?>
            </div>
        </div>
    </div>
</div>
