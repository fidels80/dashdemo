<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DashPermesso */

$this->title = 'Modifica risorsa: ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Gestione Permessi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashpermesso-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
