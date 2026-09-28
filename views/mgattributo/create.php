<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgAttributoArticolo */
/* @var $tipi array */

$this->title = 'Nuovo attributo articolo';
$this->params['breadcrumbs'][] = ['label' => 'Attributi articolo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgattributo-create">
    <?= $this->render('_form', ['model' => $model, 'tipi' => $tipi]) ?>
</div>
