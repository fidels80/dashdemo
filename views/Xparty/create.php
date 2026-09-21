<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xruoli */

$this->title = 'Create Xruoli';
$this->params['breadcrumbs'][] = ['label' => 'Xruolis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="xruoli-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
