<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgLotto */

$this->title = 'Modifica lotto: ' . $model->codice_lotto;
$this->params['breadcrumbs'][] = ['label' => 'Lotti', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice_lotto, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mglotto-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
