<?php

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */

$data = $model->data ? date('d/m/Y', strtotime((string) $model->data)) : '';

$this->title = 'Modifica rapportino n. ' . (int) $model->numero . ($data ? ' del ' . $data : '');
$this->params['breadcrumbs'][] = ['label' => 'Rapportini', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Rapportino n. ' . (int) $model->numero, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="rapportini-update card p-3 shadow-sm">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
