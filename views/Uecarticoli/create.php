<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Uecarticoli */

$this->title = 'Create Uecarticoli';
$this->params['breadcrumbs'][] = ['label' => 'Uecarticolis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uecarticoli-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
