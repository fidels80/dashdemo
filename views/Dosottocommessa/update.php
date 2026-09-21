<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Dosottocommessa */

//$this->title = 'Update Dosottocommessa: ' . $model->Cd_DOSottoCommessa;
//$this->params['breadcrumbs'][] = ['label' => 'Dosottocommessas', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->Cd_DOSottoCommessa, 'url' => ['view', 'id' => $model->Cd_DOSottoCommessa]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="dosottocommessa-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
