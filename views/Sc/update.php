<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sc */

$this->title = 'Update Sc: ' . $model->Id_SC;
$this->params['breadcrumbs'][] = ['label' => 'Scs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Id_SC, 'url' => ['view', 'id' => $model->Id_SC]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sc-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
