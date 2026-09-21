<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoDocumento */

$this->title = 'Nuovo tipo documento';
$this->params['breadcrumbs'][] = ['label' => 'Tipi documento', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgtipodocumento-create">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
