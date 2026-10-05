<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgSottocommessa */
/* @var $commesse array */
/* @var $anagrafiche array */

$this->title = 'Modifica sottocommessa: ' . $model->codice;
$this->params['breadcrumbs'][] = ['label' => 'Sottocommesse', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgsottocommessa-update">
    <?= $this->render('_form', ['model' => $model, 'commesse' => $commesse, 'anagrafiche' => $anagrafiche]) ?>
</div>
