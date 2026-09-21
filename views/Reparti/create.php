<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Reparti */

$this->title = 'Create Reparti';
$this->params['breadcrumbs'][] = ['label' => 'Repartis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reparti-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
