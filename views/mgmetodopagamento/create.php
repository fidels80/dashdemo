<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgMetodoPagamento */

$this->title = 'Nuovo metodo di pagamento';
$this->params['breadcrumbs'][] = ['label' => 'Metodi pagamento', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgmetodopagamento-create">
    <?= $this->render('_form', [
        'model' => $model,
        'tipi' => $tipi,
        'rate' => $rate,
    ]) ?>
</div>
