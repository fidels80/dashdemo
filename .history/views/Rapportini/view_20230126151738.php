<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Rapportini */

$this->title = $model->numero.' del ' . $model->data;
$this->params['breadcrumbs'][] = ['label' => 'Rapportinis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="rapportini-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php /*Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'cf',
            'commessa',
            'qta',
            'data',
            'ora_in',
            'ora_out',
            'numero',
            'userid',
            'note',
            'cd_art',
            'des_art',
        ],
    ]) ?>

</div>
