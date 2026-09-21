<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ScSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sc-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id_SC') ?>

    <?= $form->field($model, 'Id_CGMovT') ?>

    <?= $form->field($model, 'Id_DOTes') ?>

    <?= $form->field($model, 'Id_SCDistinta') ?>

    <?= $form->field($model, 'Id_SC_P_Split') ?>

    <?php // echo $form->field($model, 'Id_SC_P_Ins') ?>

    <?php // echo $form->field($model, 'Id_SC_P_Gruppo') ?>

    <?php // echo $form->field($model, 'Id_SCGruppo') ?>

    <?php // echo $form->field($model, 'TipoGruppo') ?>

    <?php // echo $form->field($model, 'Tipolink') ?>

    <?php // echo $form->field($model, 'Cd_CF') ?>

    <?php // echo $form->field($model, 'Cd_CGConto_Banca') ?>

    <?php // echo $form->field($model, 'Cd_CGConto_Portafoglio') ?>

    <?php // echo $form->field($model, 'Cd_CGConto_InPortafoglio') ?>

    <?php // echo $form->field($model, 'Cd_CGConto_InSbf') ?>

    <?php // echo $form->field($model, 'Cd_VL') ?>

    <?php // echo $form->field($model, 'Cambio') ?>

    <?php // echo $form->field($model, 'Decimali') ?>

    <?php // echo $form->field($model, 'Cd_PG') ?>

    <?php // echo $form->field($model, 'Descrizione') ?>

    <?php // echo $form->field($model, 'DataScadenza') ?>

    <?php // echo $form->field($model, 'DataPagamento') ?>

    <?php // echo $form->field($model, 'DataFattura') ?>

    <?php // echo $form->field($model, 'NumFattura') ?>

    <?php // echo $form->field($model, 'Protocollo') ?>

    <?php // echo $form->field($model, 'PartAnno') ?>

    <?php // echo $form->field($model, 'PartNum') ?>

    <?php // echo $form->field($model, 'TipoRata') ?>

    <?php // echo $form->field($model, 'Bloccata') ?>

    <?php // echo $form->field($model, 'Emessa') ?>

    <?php // echo $form->field($model, 'Contabilizzata') ?>

    <?php // echo $form->field($model, 'Pagata') ?>

    <?php // echo $form->field($model, 'Insoluta') ?>

    <?php // echo $form->field($model, 'Compensata') ?>

    <?php // echo $form->field($model, 'RiemessaSuInsoluto') ?>

    <?php // echo $form->field($model, 'NumEffetto') ?>

    <?php // echo $form->field($model, 'TotEffetti') ?>

    <?php // echo $form->field($model, 'ImportoE') ?>

    <?php // echo $form->field($model, 'ImportoV') ?>

    <?php // echo $form->field($model, 'EmessoV') ?>

    <?php // echo $form->field($model, 'EmessoE') ?>

    <?php // echo $form->field($model, 'IncassoV') ?>

    <?php // echo $form->field($model, 'PercImponibile') ?>

    <?php // echo $form->field($model, 'PercImposta') ?>

    <?php // echo $form->field($model, 'PercProvvigione') ?>

    <?php // echo $form->field($model, 'NoteSC') ?>

    <?php // echo $form->field($model, 'Piazza') ?>

    <?php // echo $form->field($model, 'Traente') ?>

    <?php // echo $form->field($model, 'Girate') ?>

    <?php // echo $form->field($model, 'Sollecito') ?>

    <?php // echo $form->field($model, 'Cd_SL') ?>

    <?php // echo $form->field($model, 'DataUltimoSollecito') ?>

    <?php // echo $form->field($model, 'DataValuta') ?>

    <?php // echo $form->field($model, 'Cd_Simulazione') ?>

    <?php // echo $form->field($model, 'ProvvisorioDaDocumento') ?>

    <?php // echo $form->field($model, 'Iban') ?>

    <?php // echo $form->field($model, 'BicCode') ?>

    <?php // echo $form->field($model, 'Cd_Abicab') ?>

    <?php // echo $form->field($model, 'ContoCorrente') ?>

    <?php // echo $form->field($model, 'Cin_It') ?>

    <?php // echo $form->field($model, 'UserIns') ?>

    <?php // echo $form->field($model, 'UserUpd') ?>

    <?php // echo $form->field($model, 'TimeIns') ?>

    <?php // echo $form->field($model, 'TimeUpd') ?>

    <?php // echo $form->field($model, 'Ts') ?>

    <?php // echo $form->field($model, 'CambioStorico') ?>

    <?php // echo $form->field($model, 'DataRivalutazione') ?>

    <?php // echo $form->field($model, 'NoteXML') ?>

    <?php // echo $form->field($model, 'CIG') ?>

    <?php // echo $form->field($model, 'CUP') ?>

    <?php // echo $form->field($model, 'SDD_IdMandato') ?>

    <?php // echo $form->field($model, 'SDD_DtMandato') ?>

    <?php // echo $form->field($model, 'SDD_SqMandato') ?>

    <?php // echo $form->field($model, 'ExtraInfo') ?>

    <?php // echo $form->field($model, 'Riconciliato') ?>

    <?php // echo $form->field($model, 'Id_RBTes') ?>

    <?php // echo $form->field($model, 'FTE_TipoPagamento') ?>

    <?php // echo $form->field($model, 'xQuotaV_RA') ?>

    <?php // echo $form->field($model, 'xQuotaV_RE') ?>

    <?php // echo $form->field($model, 'xQuotaE_RA') ?>

    <?php // echo $form->field($model, 'xQuotaE_RE') ?>

    <?php // echo $form->field($model, 'xImportoVNettoRitenute') ?>

    <?php // echo $form->field($model, 'xImportoENettoRitenute') ?>

    <?php // echo $form->field($model, 'Provvisorio') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
