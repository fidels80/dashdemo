<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Plist */

$this->title = 'Crea listino';
$this->params['breadcrumbs'][] = ['label' => 'Plists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="plist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
