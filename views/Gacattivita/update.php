<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Gacattivita */

$this->title = 'Update Gacattivita: ' . $model->id_attivita;
$this->params['breadcrumbs'][] = ['label' => 'Gacattivitas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_attivita, 'url' => ['view', 'id' => $model->id_attivita]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gacattivita-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
