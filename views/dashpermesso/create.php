<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DashPermesso */

$this->title = 'Nuova risorsa permessi';
$this->params['breadcrumbs'][] = ['label' => 'Gestione Permessi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashpermesso-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
