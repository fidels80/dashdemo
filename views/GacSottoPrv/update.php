<?php

use yii\helpers\Html;
$this->title                   = '';
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $model app\models\Gacsottoprv */

//$this->title = 'Update Gacsottoprv: ' . $model->id_sub_prv;
//$this->params['breadcrumbs'][] = ['label' => 'Gacsottoprvs', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_sub_prv, 'url' => ['view', 'id' => $model->id_sub_prv]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="gacsottoprv-update">

    <h1><?= Html::encode('Sotto Preventivi') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
