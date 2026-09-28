<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgAliquotaIva */

$this->title = 'Nuova aliquota IVA';
$this->params['breadcrumbs'][] = ['label' => 'Aliquote IVA', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgaliquotaiva-create">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
