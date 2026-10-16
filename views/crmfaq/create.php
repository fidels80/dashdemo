<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgCrmFaq */

$this->title = 'Nuova FAQ';
$this->params['breadcrumbs'][] = ['label' => 'FAQ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crmfaq-create card p-3 shadow-sm">
    <?= $this->render('_form', ['model' => $model, 'articoli' => $articoli]) ?>
</div>
