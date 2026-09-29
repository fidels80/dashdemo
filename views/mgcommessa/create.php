<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgCommessa */
/* @var $anagrafiche array */

$this->title = 'Nuova commessa';
$this->params['breadcrumbs'][] = ['label' => 'Commesse', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgcommessa-create card p-3 shadow-sm">
    <?= $this->render('_form', ['model' => $model, 'anagrafiche' => $anagrafiche]) ?>
</div>
