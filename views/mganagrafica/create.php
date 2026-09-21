<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgAnagrafica */

$this->title = 'Nuova anagrafica';
$this->params['breadcrumbs'][] = ['label' => 'Anagrafica', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mganagrafica-create">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
