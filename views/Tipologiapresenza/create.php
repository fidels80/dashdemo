<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tipologiapresenza */

$this->title = 'Create Tipologiapresenza';
$this->params['breadcrumbs'][] = ['label' => 'Tipologiapresenzas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipologiapresenza-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
