<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Dosottocommessa */

//$this->title = 'Create Dosottocommessa';
//$this->params['breadcrumbs'][] = ['label' => 'Dosottocommessas', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dosottocommessa-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
