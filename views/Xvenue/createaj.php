<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xvenue */

$this->title = null;
//$this->params['breadcrumbs'][] = ['label' => 'Xvenues', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="xvenue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formaj', [
        'model' => $model,
    ]) ?>

</div>
