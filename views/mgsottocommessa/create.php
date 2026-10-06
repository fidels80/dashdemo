<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgSottocommessa */
/* @var $commesse array */
/* @var $anagrafiche array */

$this->title = 'Nuova sottocommessa';
$this->params['breadcrumbs'][] = ['label' => 'Sottocommesse', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgsottocommessa-create">
    <?= $this->render('_form', ['model' => $model, 'commesse' => $commesse, 'anagrafiche' => $anagrafiche]) ?>
</div>
