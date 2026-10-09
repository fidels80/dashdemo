<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgMatricola */

$this->title = 'Nuova matricola';
$this->params['breadcrumbs'][] = ['label' => 'Matricole / Numeri di serie', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgmatricola-create">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
