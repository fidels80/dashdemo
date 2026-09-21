<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xtappe */

$this->title = 'Aggiorna Tappa: ' . $model->getDecodevenue();
 
?>
<div class="xtappe-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
