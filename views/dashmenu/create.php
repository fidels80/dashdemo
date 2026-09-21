<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DashMenu */
/* @var $genitori array */

$this->title = 'Nuova voce di menu';
$this->params['breadcrumbs'][] = ['label' => 'Gestione Menu', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashmenu-create">
    <?= $this->render('_form', ['model' => $model, 'genitori' => $genitori]) ?>
</div>
