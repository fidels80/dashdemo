<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Todomain */

$this->title = Yii::t('app', 'Create Todomain');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Todomains'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="todomain-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
