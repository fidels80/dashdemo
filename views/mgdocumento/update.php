<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgDocumento */
/* @var $tipi array */
/* @var $anagrafiche array */
/* @var $righe app\models\MgDocumentoRiga[] */

$this->title = 'Modifica documento: ' . $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Documenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->etichetta, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgdocumento-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'tipi' => $tipi,
        'anagrafiche' => $anagrafiche,
        'righe' => $righe,
    ]) ?>
</div>
