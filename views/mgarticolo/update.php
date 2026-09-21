<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgArticolo */

$this->title = 'Modifica articolo: ' . $model->codice;
$this->params['breadcrumbs'][] = ['label' => 'Articoli', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgarticolo-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
