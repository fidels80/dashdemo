<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgConfigurazione */

$this->title = 'Nuovo parametro di configurazione';
$this->params['breadcrumbs'][] = ['label' => 'Configurazione', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgconfigurazione-create">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
