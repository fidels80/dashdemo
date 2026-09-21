<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgDocumento */
/* @var $tipi array */
/* @var $anagrafiche array */
/* @var $righe array */

$this->title = 'Nuovo documento';
$this->params['breadcrumbs'][] = ['label' => 'Documenti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgdocumento-create">
    <?= $this->render('_form', [
        'model' => $model,
        'tipi' => $tipi,
        'anagrafiche' => $anagrafiche,
        'righe' => $righe,
    ]) ?>
</div>
