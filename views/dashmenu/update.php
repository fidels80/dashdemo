<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DashMenu */
/* @var $genitori array */

$this->title = 'Modifica voce: ' . $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Gestione Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashmenu-update">
    <?= $this->render('_form', ['model' => $model, 'genitori' => $genitori]) ?>
</div>
