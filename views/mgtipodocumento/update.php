<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoDocumento */

$this->title = 'Modifica tipo documento: ' . $model->codice;
$this->params['breadcrumbs'][] = ['label' => 'Tipi documento', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgtipodocumento-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
