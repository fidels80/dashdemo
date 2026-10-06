<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgCommessa */
/* @var $anagrafiche array */

$this->title = 'Modifica commessa: ' . $model->codice;
$this->params['breadcrumbs'][] = ['label' => 'Commesse', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgcommessa-update">
    <?= $this->render('_form', ['model' => $model, 'anagrafiche' => $anagrafiche]) ?>
</div>
