<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Xtravelrow */

$this->title = $model->tr_id;
$this->params['breadcrumbs'][] = ['label' => 'Xtravelrows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="xtravelrow-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->tr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->tr_id], [
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
            'tr_id',
            'th_id',
            'sottocommessa',
            'cd_Ar',
            'descrizione',
            'qta',
            'prezzo',
            'stato',
            'guest',
            'ruolo',
            'cd_cf_ft',
            'descli',
            'citta',
            'fornitore',
            'desfor',
            'struttura',
            'check_in',
            'check_out',
            'citta_da',
            'citta_a',
            'orario',
            'pnr',
            'nr_biglietto',
            'data_pg',
            'cd_pg',
            'contabile',
            'totale',
            'tax',
            'fee',
            'fee_perc',
            'imponibile',
            'iva',
            'Totalegenerale',
            'evadi_A',
            'evadi_p',
            'tax_unit',
            'note',
            'descontab',
            'totfattura',
            'codiva',
            'pagato',
            'xid',
            'timeins',
            'numero',
            'datah',
            'x_scdesc',
            'x_pagato',
        ],
    ]) ?>

</div>
