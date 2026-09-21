<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Todostato */

$this->title = Yii::t('app', 'Create Todostato');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Todostatos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="todostato-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
