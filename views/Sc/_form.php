<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Sc */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sc-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Id_CGMovT')->textInput() ?>

    <?= $form->field($model, 'Id_DOTes')->textInput() ?>

    <?= $form->field($model, 'Id_SCDistinta')->textInput() ?>

    <?= $form->field($model, 'Id_SC_P_Split')->textInput() ?>

    <?= $form->field($model, 'Id_SC_P_Ins')->textInput() ?>

    <?= $form->field($model, 'Id_SC_P_Gruppo')->textInput() ?>

    <?= $form->field($model, 'Id_SCGruppo')->textInput() ?>

    <?= $form->field($model, 'TipoGruppo')->textInput() ?>

    <?= $form->field($model, 'Tipolink')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cd_CF')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cd_CGConto_Banca')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cd_CGConto_Portafoglio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cd_CGConto_InPortafoglio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cd_CGConto_InSbf')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cd_VL')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cambio')->textInput() ?>

    <?= $form->field($model, 'Decimali')->textInput() ?>

    <?= $form->field($model, 'Cd_PG')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Descrizione')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DataScadenza')->textInput() ?>

    <?= $form->field($model, 'DataPagamento')->textInput() ?>

    <?= $form->field($model, 'DataFattura')->textInput() ?>

    <?= $form->field($model, 'NumFattura')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Protocollo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'PartAnno')->textInput() ?>

    <?= $form->field($model, 'PartNum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'TipoRata')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Bloccata')->textInput() ?>

    <?= $form->field($model, 'Emessa')->textInput() ?>

    <?= $form->field($model, 'Contabilizzata')->textInput() ?>

    <?= $form->field($model, 'Pagata')->textInput() ?>

    <?= $form->field($model, 'Insoluta')->textInput() ?>

    <?= $form->field($model, 'Compensata')->textInput() ?>

    <?= $form->field($model, 'RiemessaSuInsoluto')->textInput() ?>

    <?= $form->field($model, 'NumEffetto')->textInput() ?>

    <?= $form->field($model, 'TotEffetti')->textInput() ?>

    <?= $form->field($model, 'ImportoE')->textInput() ?>

    <?= $form->field($model, 'ImportoV')->textInput() ?>

    <?= $form->field($model, 'EmessoV')->textInput() ?>

    <?= $form->field($model, 'EmessoE')->textInput() ?>

    <?= $form->field($model, 'IncassoV')->textInput() ?>

    <?= $form->field($model, 'PercImponibile')->textInput() ?>

    <?= $form->field($model, 'PercImposta')->textInput() ?>

    <?= $form->field($model, 'PercProvvigione')->textInput() ?>

    <?= $form->field($model, 'NoteSC')->textInput() ?>

    <?= $form->field($model, 'Piazza')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Traente')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Girate')->textInput() ?>

    <?= $form->field($model, 'Sollecito')->textInput() ?>

    <?= $form->field($model, 'Cd_SL')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'DataUltimoSollecito')->textInput() ?>

    <?= $form->field($model, 'DataValuta')->textInput() ?>

    <?= $form->field($model, 'Cd_Simulazione')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ProvvisorioDaDocumento')->textInput() ?>

    <?= $form->field($model, 'Iban')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'BicCode')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cd_Abicab')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ContoCorrente')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'Cin_It')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'UserIns')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'UserUpd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'TimeIns')->textInput() ?>

    <?= $form->field($model, 'TimeUpd')->textInput() ?>

    <?= $form->field($model, 'Ts')->textInput() ?>

    <?= $form->field($model, 'CambioStorico')->textInput() ?>

    <?= $form->field($model, 'DataRivalutazione')->textInput() ?>

    <?= $form->field($model, 'NoteXML')->textInput() ?>

    <?= $form->field($model, 'CIG')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CUP')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'SDD_IdMandato')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'SDD_DtMandato')->textInput() ?>

    <?= $form->field($model, 'SDD_SqMandato')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ExtraInfo')->textInput() ?>

    <?= $form->field($model, 'Riconciliato')->textInput() ?>

    <?= $form->field($model, 'Id_RBTes')->textInput() ?>

    <?= $form->field($model, 'FTE_TipoPagamento')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'xQuotaV_RA')->textInput() ?>

    <?= $form->field($model, 'xQuotaV_RE')->textInput() ?>

    <?= $form->field($model, 'xQuotaE_RA')->textInput() ?>

    <?= $form->field($model, 'xQuotaE_RE')->textInput() ?>

    <?= $form->field($model, 'xImportoVNettoRitenute')->textInput() ?>

    <?= $form->field($model, 'xImportoENettoRitenute')->textInput() ?>

    <?= $form->field($model, 'Provvisorio')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
