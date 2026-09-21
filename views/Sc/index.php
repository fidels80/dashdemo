<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ScSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Scs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sc-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Sc', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'Id_SC',
            'Id_CGMovT',
            'Id_DOTes',
            'Id_SCDistinta',
            'Id_SC_P_Split',
            //'Id_SC_P_Ins',
            //'Id_SC_P_Gruppo',
            //'Id_SCGruppo',
            //'TipoGruppo',
            //'Tipolink',
            //'Cd_CF',
            //'Cd_CGConto_Banca',
            //'Cd_CGConto_Portafoglio',
            //'Cd_CGConto_InPortafoglio',
            //'Cd_CGConto_InSbf',
            //'Cd_VL',
            //'Cambio',
            //'Decimali',
            //'Cd_PG',
            //'Descrizione',
            //'DataScadenza',
            //'DataPagamento',
            //'DataFattura',
            //'NumFattura',
            //'Protocollo',
            //'PartAnno',
            //'PartNum',
            //'TipoRata',
            //'Bloccata',
            //'Emessa',
            //'Contabilizzata',
            //'Pagata',
            //'Insoluta',
            //'Compensata',
            //'RiemessaSuInsoluto',
            //'NumEffetto',
            //'TotEffetti',
            //'ImportoE',
            //'ImportoV',
            //'EmessoV',
            //'EmessoE',
            //'IncassoV',
            //'PercImponibile',
            //'PercImposta',
            //'PercProvvigione',
            //'NoteSC',
            //'Piazza',
            //'Traente',
            //'Girate',
            //'Sollecito',
            //'Cd_SL',
            //'DataUltimoSollecito',
            //'DataValuta',
            //'Cd_Simulazione',
            //'ProvvisorioDaDocumento',
            //'Iban',
            //'BicCode',
            //'Cd_Abicab',
            //'ContoCorrente',
            //'Cin_It',
            //'UserIns',
            //'UserUpd',
            //'TimeIns',
            //'TimeUpd',
            //'Ts',
            //'CambioStorico',
            //'DataRivalutazione',
            //'NoteXML',
            //'CIG',
            //'CUP',
            //'SDD_IdMandato',
            //'SDD_DtMandato',
            //'SDD_SqMandato',
            //'ExtraInfo',
            //'Riconciliato',
            //'Id_RBTes',
            //'FTE_TipoPagamento',
            //'xQuotaV_RA',
            //'xQuotaV_RE',
            //'xQuotaE_RA',
            //'xQuotaE_RE',
            //'xImportoVNettoRitenute',
            //'xImportoENettoRitenute',
            //'Provvisorio',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
