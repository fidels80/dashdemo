<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sottocommessa */

$this->title = '';

?>
<div class="sottocommessa-create">

    <h1><?= Html::encode('Crea Sotto Commessa') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
