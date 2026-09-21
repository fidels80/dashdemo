<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xruoli */

$this->title = null;
 
?>
<div class="xruoli-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formaj', [
        'model' => $model,
    ]) ?>

</div>
