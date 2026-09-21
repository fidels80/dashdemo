<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Todopriorita */

$this->title = Yii::t('app', 'Create Todopriorita');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Todoprioritas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="todopriorita-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
