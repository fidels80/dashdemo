<?php

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */

$this->title = 'Inserisci rapportino';
$this->params['breadcrumbs'][] = ['label' => 'Rapportini', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rapportini-create card p-3 shadow-sm">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
