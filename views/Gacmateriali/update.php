<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Gacmateriali */

$this->title = 'Update Gacmateriali: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gacmaterialis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gacmateriali-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
