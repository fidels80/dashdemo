<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgAttributoArticolo */
/* @var $tessuti array */
/* @var $tessutiSelezionati array */

$this->title = 'Modifica modello: ' . $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Modelli', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->etichetta, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgmodello-update card p-3 shadow-sm">
    <?= $this->render('_form', [
        'model' => $model,
        'tessuti' => $tessuti,
        'tessutiSelezionati' => $tessutiSelezionati,
    ]) ?>
</div>
