<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\XtravelrowSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Xtravelrows';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="xtravelrow-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Xtravelrow', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'tr_id',
            'th_id',
            'sottocommessa',
            'cd_Ar',
            'descrizione',
            //'qta',
            //'prezzo',
            //'stato',
            //'guest',
            //'ruolo',
            //'cd_cf_ft',
            //'descli',
            //'citta',
            //'fornitore',
            //'desfor',
            //'struttura',
            //'check_in',
            //'check_out',
            //'citta_da',
            //'citta_a',
            //'orario',
            //'pnr',
            //'nr_biglietto',
            //'data_pg',
            //'cd_pg',
            //'contabile',
            //'totale',
            //'tax',
            //'fee',
            //'fee_perc',
            //'imponibile',
            //'iva',
            //'Totalegenerale',
            //'evadi_A',
            //'evadi_p',
            //'tax_unit',
            //'note',
            //'descontab',
            //'totfattura',
            //'codiva',
            //'pagato',
            //'xid',
            //'timeins',
            //'numero',
            //'datah',
            //'x_scdesc',
            //'x_pagato',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
