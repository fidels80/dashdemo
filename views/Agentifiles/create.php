<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Agentifiles */

$this->title = 'Create Agentifiles';
$this->params['breadcrumbs'][] = ['label' => 'Agentifiles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agentifiles-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
