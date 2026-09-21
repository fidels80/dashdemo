<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Docommessa */

$this->title = $model->Cd_DOCommessa;
$this->params['breadcrumbs'][] = ['label' => 'Docommessas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="docommessa-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->Cd_DOCommessa], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->Cd_DOCommessa], [
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
            'Id_DOCommessa',
            'Cd_DOCommessa',
            'Descrizione',
            'DescrizioneBreve',
            'Cd_CF',
            'Cd_DOCommessaStato',
            'DataInizio',
            'DataFinePresunta',
            'DataFineReale',
            'NoteDoCommessa',
            'UserIns',
            'UserUpd',
            'TimeIns',
            'TimeUpd',
            'Ts',
            'NoteXML',
            'Attributi',
            'Sconto',
            'Provvigione',
        ],
    ]) ?>

</div>
