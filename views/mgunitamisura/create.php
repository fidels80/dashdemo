<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgUnitaMisura */

$this->title = 'Nuova unità di misura';
$this->params['breadcrumbs'][] = ['label' => 'Unità di misura', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgunitamisura-create">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
