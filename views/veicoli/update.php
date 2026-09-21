<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Veicoli */

$this->title = '';
//$this->params['breadcrumbs'][] = ['label' => 'Veicolis', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs']  = '';
?>
<div class="veicoli-update">

    <h1><?= Html::encode('Aggiorna Veicolo: ' . $model->targa) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
