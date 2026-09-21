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
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model, 'genitori' => $genitori]) ?>
</div>
