<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoContatto */

$this->title = 'Nuovo tipo contatto';
$this->params['breadcrumbs'][] = ['label' => 'Tipi contatto', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgtipocontatto-create card p-3 shadow-sm">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
