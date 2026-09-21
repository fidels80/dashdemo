<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sc */

$this->title = 'Create Sc';
$this->params['breadcrumbs'][] = ['label' => 'Scs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sc-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
