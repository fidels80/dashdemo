<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xroomlist */

$this->title = 'Create Xroomlist';
$this->params['breadcrumbs'][] = ['label' => 'Xroomlists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="xroomlist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
