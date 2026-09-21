<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Gacprv */

$this->title = 'Create Gacprv';
$this->params['breadcrumbs'][] = ['label' => 'Gacprvs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gacprv-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
