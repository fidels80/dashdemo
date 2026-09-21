<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xtappe */

$this->title = 'Create Xtappe';
$this->params['breadcrumbs'][] = ['label' => 'Xtappes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="xtappe-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
