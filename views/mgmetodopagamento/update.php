<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgMetodoPagamento */

$this->title = 'Modifica metodo: ' . $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Metodi pagamento', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->descrizione, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgmetodopagamento-update">
    <?= $this->render('_form', [
        'model' => $model,
        'tipi' => $tipi,
        'rate' => $rate,
    ]) ?>
</div>
