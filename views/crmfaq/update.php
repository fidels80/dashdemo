<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgCrmFaq */

$this->title = 'Modifica FAQ';
$this->params['breadcrumbs'][] = ['label' => 'FAQ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->domanda, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="crmfaq-update card p-3 shadow-sm">
    <?= $this->render('_form', ['model' => $model, 'articoli' => $articoli]) ?>
</div>
