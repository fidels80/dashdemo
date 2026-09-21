<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Personale */

$this->title = null;
//'Aggiorna Personale: ' . $model->nome.  '   '.$model->cognome;
//$this->params['breadcrumbs'][] = ['label' => 'Personales', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 
//'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="personale-update">

    <h1><?= Html::encode('Aggiorna Personale: ' . $model->nome.  '   '.$model->cognome) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
