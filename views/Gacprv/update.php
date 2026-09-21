<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Gacprv */

$this->title = 'Update Gacprv: ' . $model->id_prv;
$this->params['breadcrumbs'][] = ['label' => 'Gacprvs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_prv, 'url' => ['view', 'id' => $model->id_prv]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gacprv-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
