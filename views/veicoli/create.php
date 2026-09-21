<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Veicoli */

$this->title = 'Create Veicoli';
$this->params['breadcrumbs'][] = ['label' => 'Veicolis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="veicoli-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
