<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sottocommessa */

//$this->title = 'Aggiorno' . $model->Cd_DOSottoCommessa;
//$this->params['breadcrumbs'][] = ['label' => 'Sottocommessas', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->Cd_DOSottoCommessa, 'url' => ['view', 'id' => $model->Cd_DOSottoCommessa]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sottocommessa-update">

    <h1><?= Html::encode('Aggiorna Commessa') ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
