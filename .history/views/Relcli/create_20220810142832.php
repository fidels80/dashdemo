<?php

use yii\helpers\Html;
 

/* @var $this yii\web\View */
/* @var $model app\models\Relcli */

$this->title = 'Crea Relazioni tra clienti';
$this->params['breadcrumbs'][] = ['label' => ' Relazioni tra clienti', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;



?>
<div class="relcli-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
