<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Todorelgrpusr */

$this->title = Yii::t('app', 'Create Todorelgrpusr');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Todorelgrpusrs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="todorelgrpusr-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
