<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xtravelrow */

$this->title = 'Create Xtravelrow';
$this->params['breadcrumbs'][] = ['label' => 'Xtravelrows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="xtravelrow-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
