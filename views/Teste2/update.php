<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Teste2 */

$this->title = 'Update Teste2: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Teste2s', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="teste2-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
