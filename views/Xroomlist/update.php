<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Xroomlist */

$this->title = null;
//$this->params['breadcrumbs'][] = ['label' => 'Xroomlists', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id_guest, 'url' => ['view', 'id' => $model->id_guest]];
//$this->params['breadcrumbs'][] = 'Update';
//$this->params['breadcrumbs'][]='';
?>
<div class="xroomlist-update">

    <h1><?= Html::encode('Aggiorna: ' . $model->nominativo) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'tappeDisponibili' => $tappeDisponibili,
    ]) ?>

</div>
