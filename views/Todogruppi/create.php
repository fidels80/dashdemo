<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Todogruppi */

$this->title = Yii::t('app', 'Create Todogruppi');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Todogruppis'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="todogruppi-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
