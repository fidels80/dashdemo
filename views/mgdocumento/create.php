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
/* @var $tipiMostraVarianti array */
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
        'tipiMostraVarianti' => $tipiMostraVarianti,
        'modelliMatrice' => $modelliMatrice,
        'righe' => $righe,
    ]) ?>
</div>
