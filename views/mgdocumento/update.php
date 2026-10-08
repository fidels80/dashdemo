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
/* @var $modelliMatrice array */
/* @var $righe app\models\MgDocumentoRiga[] */

$this->title = 'Modifica documento: ' . $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Documenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->etichetta, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgdocumento-update">
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
        'modelliMatrice' => $modelliMatrice,
        'righe' => $righe,
    ]) ?>
</div>
