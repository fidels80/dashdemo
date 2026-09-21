<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Gacsottoprv */

$this->title = 'Create Gacsottoprv';
$this->params['breadcrumbs'][] = ['label' => 'Gacsottoprvs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gacsottoprv-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
