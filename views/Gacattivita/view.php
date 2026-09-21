<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Gacattivita */

$this->title = $model->id_attivita;
$this->params['breadcrumbs'][] = ['label' => 'Gacattivitas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="gacattivita-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id_attivita], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id_attivita], [
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
            'id_attivita',
            'id_sub_prv',
            'sequenza',
            'attivita',
            'descrizione',
            'um',
            'tempo',
            'ore',
            'risorsa',
            'costo',
            'sconto',
            'costo_scontato',
            'ricarico',
            'costo_ricarico',
            'sconto_vendita',
            'valore_costounitario',
            'valore_costotot',
            'margine',
            'margine_perc',
            'note:ntext',
            'data_apertura',
            'data_chiusura',
        ],
    ]) ?>

</div>
