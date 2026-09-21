<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Testaj */

$this->title = 'Create Testaj';
$this->params['breadcrumbs'][] = ['label' => 'Testajs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="testaj-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
