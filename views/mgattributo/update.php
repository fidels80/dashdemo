<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgAttributoArticolo */
/* @var $tipi array */

$this->title = 'Modifica attributo: ' . $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Attributi articolo', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->etichetta, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgattributo-update">
    <?= $this->render('_form', ['model' => $model, 'tipi' => $tipi]) ?>
</div>
