<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Personale */

$this->title = 'Create Personale';
$this->params['breadcrumbs'][] = ['label' => 'Personales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="personale-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
