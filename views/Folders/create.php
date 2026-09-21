<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Folders */

$this->title = 'Create Folders';
$this->params['breadcrumbs'][] = ['label' => 'Folders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="folders-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
