<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoPagamento */

$this->title = 'Nuovo tipo pagamento';
$this->params['breadcrumbs'][] = ['label' => 'Tipi pagamento', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgtipopagamento-create">
    <?= $this->render('_form', ['model' => $model]) ?>
</div>
