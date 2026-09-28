<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgAnagrafica */
/* @var $metodi array */
/* @var $aliquote array */

$this->title = 'Nuova anagrafica';
$this->params['breadcrumbs'][] = ['label' => 'Anagrafica', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mganagrafica-create">
    <?= $this->render('_form', ['model' => $model, 'metodi' => $metodi, 'aliquote' => $aliquote]) ?>
</div>
