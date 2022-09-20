<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Relcli */

$this->title = 'Create Relcli';
$this->params['breadcrumbs'][] = ['label' => 'Relclis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="relcli-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
