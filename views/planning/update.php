<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Planning */

$this->title =null;
 
?>
<div class="planning-update">

    <h1> Aggiorna Evento</h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
