<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Planning */

$this->title = '';
$this->params['breadcrumbs'][] = '';
//['label' => 'Plannings', 'url' => ['index']];
$this->params['breadcrumbs']  ='';
// $this->title;
?>
<div class="planning-create">

    <h1><?= Html::encode('Crea Planning') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
