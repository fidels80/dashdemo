<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgMagazzino */

$this->title = 'Nuovo magazzino';
$this->params['breadcrumbs'][] = ['label' => 'Magazzini', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgmagazzino-create">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
