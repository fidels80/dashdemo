<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoPagamento */

$this->title = 'Modifica tipo pagamento: ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Tipi pagamento', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->descrizione, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgtipopagamento-update">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
