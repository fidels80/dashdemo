<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xaraltdesc */

$this->title = 'Create Xaraltdesc';
$this->params['breadcrumbs'][] = ['label' => 'Xaraltdescs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="xaraltdesc-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
