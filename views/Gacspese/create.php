<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Gacspese */

$this->title = 'Create Gacspese';
$this->params['breadcrumbs'][] = ['label' => 'Gacspeses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gacspese-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
