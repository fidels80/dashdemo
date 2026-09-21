<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xstruttura */

$this->title = null;
//$this->params['breadcrumbs'][] = ['label' => 'Xstrutturas', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="xstruttura-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formaj', [
        'model' => $model,
    ]) ?>

</div>
