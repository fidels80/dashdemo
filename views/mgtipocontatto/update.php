<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoContatto */

$this->title = 'Modifica tipo contatto: ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Tipi contatto', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->descrizione, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgtipocontatto-update card p-3 shadow-sm">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
