<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Stackposttags */

$this->title = 'Create Stackposttags';
$this->params['breadcrumbs'][] = ['label' => 'Stackposttags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stackposttags-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
