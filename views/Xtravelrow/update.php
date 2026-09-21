<?php

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
/* @var $this yii\web\View */
/* @var $model app\models\Xtravelrow */

$this->title = 'Aggiorna Riga per: ' . $model->guest .' Tappa :'.$model->citta;
 

  Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? [] : [],
])  
?>
<div class="xtravelrow-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
