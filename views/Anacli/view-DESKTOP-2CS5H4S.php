<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Anacli */

$this->title = $model->cd_cli;
$this->params['breadcrumbs'][] = ['label' => 'Anaclis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="anacli-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cd_cli], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'cd_cli',
            'Desk',
            'address',
            'localita',
            'cap',
            'cd_nazione',
            'PartitaIva',
            'CodiceFiscale',
            'ccemail',
            'showprices','show_ins_nrgaz'
        ],
    ]) ?>

</div>
