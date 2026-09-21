<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgAnagrafica */

$this->title = 'Modifica anagrafica: ' . $model->ragione_sociale;
$this->params['breadcrumbs'][] = ['label' => 'Anagrafica', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mganagrafica-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
