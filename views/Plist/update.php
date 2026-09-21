<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Plist */

$this->title = 'Update Plist: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Plists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="plist-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
