<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Sc */

$this->title = $model->Id_SC;
$this->params['breadcrumbs'][] = ['label' => 'Scs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sc-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->Id_SC], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->Id_SC], [
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
            'Id_SC',
            'Id_CGMovT',
            'Id_DOTes',
            'Id_SCDistinta',
            'Id_SC_P_Split',
            'Id_SC_P_Ins',
            'Id_SC_P_Gruppo',
            'Id_SCGruppo',
            'TipoGruppo',
            'Tipolink',
            'Cd_CF',
            'Cd_CGConto_Banca',
            'Cd_CGConto_Portafoglio',
            'Cd_CGConto_InPortafoglio',
            'Cd_CGConto_InSbf',
            'Cd_VL',
            'Cambio',
            'Decimali',
            'Cd_PG',
            'Descrizione',
            'DataScadenza',
            'DataPagamento',
            'DataFattura',
            'NumFattura',
            'Protocollo',
            'PartAnno',
            'PartNum',
            'TipoRata',
            'Bloccata',
            'Emessa',
            'Contabilizzata',
            'Pagata',
            'Insoluta',
            'Compensata',
            'RiemessaSuInsoluto',
            'NumEffetto',
            'TotEffetti',
            'ImportoE',
            'ImportoV',
            'EmessoV',
            'EmessoE',
            'IncassoV',
            'PercImponibile',
            'PercImposta',
            'PercProvvigione',
            'NoteSC',
            'Piazza',
            'Traente',
            'Girate',
            'Sollecito',
            'Cd_SL',
            'DataUltimoSollecito',
            'DataValuta',
            'Cd_Simulazione',
            'ProvvisorioDaDocumento',
            'Iban',
            'BicCode',
            'Cd_Abicab',
            'ContoCorrente',
            'Cin_It',
            'UserIns',
            'UserUpd',
            'TimeIns',
            'TimeUpd',
            'Ts',
            'CambioStorico',
            'DataRivalutazione',
            'NoteXML',
            'CIG',
            'CUP',
            'SDD_IdMandato',
            'SDD_DtMandato',
            'SDD_SqMandato',
            'ExtraInfo',
            'Riconciliato',
            'Id_RBTes',
            'FTE_TipoPagamento',
            'xQuotaV_RA',
            'xQuotaV_RE',
            'xQuotaE_RA',
            'xQuotaE_RE',
            'xImportoVNettoRitenute',
            'xImportoENettoRitenute',
            'Provvisorio',
        ],
    ]) ?>

</div>
