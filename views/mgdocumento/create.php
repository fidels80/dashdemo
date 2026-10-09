<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgDocumento */
/* @var $tipi array */
/* @var $tipiCreaScadenze array */
/* @var $tipiDestinazione array */
/* @var $anagrafiche array */
/* @var $anagraficheMetodi array */
/* @var $anagraficheIva array */
/* @var $metodi array */
/* @var $aliquote array */
/* @var $unita array */
/* @var $sottocommesse array */
/* @var $magazzini array */
/* @var $tipiMagazzini array */
/* @var $tipiMostraVarianti array */
/* @var $tipiPrelevaRapportini array */
/* @var $tipiCreaArticoli array */
/* @var $tipiCreaAnagrafiche array */
/* @var $tipiMostraMatrice array */
/* @var $tipiGestioneSeriali array */
/* @var $tipiGestioneDataConsegna array */
/* @var $tipiGestioneLotti array */
/* @var $modelliMatrice array */
/* @var $righe array */

$this->title = 'Nuovo documento';
$this->params['breadcrumbs'][] = ['label' => 'Documenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgdocumento-create">
    <?= $this->render('_form', [
        'model' => $model,
        'tipi' => $tipi,
        'tipiCreaScadenze' => $tipiCreaScadenze,
        'tipiDestinazione' => $tipiDestinazione,
        'anagrafiche' => $anagrafiche,
        'anagraficheMetodi' => $anagraficheMetodi,
        'anagraficheIva' => $anagraficheIva,
        'metodi' => $metodi,
        'aliquote' => $aliquote,
        'unita' => $unita,
        'sottocommesse' => $sottocommesse,
        'magazzini' => $magazzini,
        'tipiMagazzini' => $tipiMagazzini,
        'tipiMostraVarianti' => $tipiMostraVarianti,
        'tipiPrelevaRapportini' => $tipiPrelevaRapportini,
        'tipiCreaArticoli' => $tipiCreaArticoli,
        'tipiCreaAnagrafiche' => $tipiCreaAnagrafiche,
        'tipiMostraMatrice' => $tipiMostraMatrice,
        'tipiGestioneSeriali' => $tipiGestioneSeriali,
        'tipiGestioneDataConsegna' => $tipiGestioneDataConsegna,
        'tipiGestioneLotti' => $tipiGestioneLotti,
        'modelliMatrice' => $modelliMatrice,
        'righe' => $righe,
    ]) ?>
</div>
