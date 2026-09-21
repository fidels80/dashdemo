<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Docommessa */

$this->title = 'Update Docommessa: ' . $model->Cd_DOCommessa;
$this->params['breadcrumbs'][] = ['label' => 'Docommessas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Cd_DOCommessa, 'url' => ['view', 'id' => $model->Cd_DOCommessa]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="docommessa-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
