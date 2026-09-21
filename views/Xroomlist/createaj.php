<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xroomlist */

$this->title = null;
//$this->params['breadcrumbs'][] = ['label' => 'Xroomlists', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
//$modalId = $modalId ?? '#nominativoModal';
?>
<div class="xroomlist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formaj', [
        'model' => $model,
        //'modalId' => $modalId,
    ]) ?>

</div>
