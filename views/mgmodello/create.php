<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgAttributoArticolo */
/* @var $tessuti array */
/* @var $tessutiSelezionati array */

$this->title = 'Nuovo modello';
$this->params['breadcrumbs'][] = ['label' => 'Modelli', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgmodello-create card p-3 shadow-sm">
    <?= $this->render('_form', [
        'model' => $model,
        'tessuti' => $tessuti,
        'tessutiSelezionati' => $tessutiSelezionati,
    ]) ?>
</div>
