<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
if(1=2){


};
/* @var $this yii\web\View */
/* @var $model app\models\Gacsottoprv */

$this->title = $model->id_sub_prv;
$this->params['breadcrumbs'][] = ['label' => 'Gacsottoprvs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="gacsottoprv-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
   <?php
yii::warning($model->locked_by);

if (!is_null($model->locked_by)) {
    echo "Attenzione elemento in uso da " . $model->locked_by;

}?>
    <?php if (is_null ($model->locked_by)) {
    echo Html::a('Update', ['update', 'id_sub_prv' => $model->id_sub_prv], ['class' => 'btn btn-primary']);
}
;?>
        <?php if (is_null($model->locked_by)) {
    echo Html::a('Delete', ['delete', 'id_sub_prv' => $model->id_sub_prv], [
        'class' => 'btn btn-danger',
        'data'  => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method'  => 'post',
        ],
    ]);
}
;?>
    </p>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_sub_prv',
            'descrizione',
            'id_prv',
            'note:ntext',
            'tipologia',
            'sottocommessa',
            'datacreazione',
            'inizioval',
            'fineval',
            'probacq',
            'provvigione',
            'apertura',
            'chiusura',
            'apertura_pianificata',
            'chiusura_pianificata',
            'stato',
            'datastato',
        ],
    ]) ?>

</div>
