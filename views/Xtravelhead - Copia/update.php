<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xtravelhead */

$this->title = 'Update Xtravelhead: ' . $model->th_id;
$this->params['breadcrumbs'][] = ['label' => 'Xtravelheads', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->th_id, 'url' => ['view', 'id' => $model->th_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="xtravelhead-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
