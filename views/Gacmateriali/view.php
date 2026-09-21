<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Gacmateriali */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Gacmaterialis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="gacmateriali-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_sub_prv',
            'listino',
            'cd_ar',
            'descrizione',
            'qta',
            'um',
            'costounitario',
            'scontoacq',
            'costounitscontato',
            'ricarico',
            'costounitarioric',
            'sconto_vendita',
            'valvendita',
            'margine',
            'margineperc',
            'prezzounitarionetto',
            'note:ntext',
        ],
    ]) ?>

</div>
