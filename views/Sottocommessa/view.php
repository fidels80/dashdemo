<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Sottocommessa */

$this->title = $model->Cd_DOSottoCommessa;
$this->params['breadcrumbs'][] = ['label' => 'Sottocommessas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sottocommessa-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->Cd_DOSottoCommessa], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->Cd_DOSottoCommessa], [
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
            'Id_DOSottoCommessa',
            'Cd_DOCommessa',
            'Cd_DOSottoCommessa',
            'Descrizione',
            'DescrizioneBreve',
            'Cd_CF',
            'Cd_DOCommessaStato',
            'DataInizio',
            'DataFinePresunta',
            'DataFineReale',
            'NoteDoSottoCommessa',
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
