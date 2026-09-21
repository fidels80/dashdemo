<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xtravelhead */

$this->title = 'Preventivo';
$this->params['breadcrumbs'][] = ['label' => 'Xtravelheads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="xtravelhead-create">

    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
