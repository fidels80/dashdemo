<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cgmovr".
 *
 * @property int $Id_CGMovR ID univoco che identifica le r
 * @property int $Id_CGMovT Riferimento alla testa del mov
 * @property int|null $Id_Sc Riferimento alla scadenza (nel
 * @property string|null $TipoMovimento Tipologia del movimento ad uso
 * @property int|null $SegnoMovimento 1 = Fatture, -1 Note di Accred
 * @property int $TipoContab Determina da quale tipo di con
 * @property int|null $ContoMerceSpesa
 * @property int $IvaSospesa (Vedi descrizione dello stesso
 * @property int $Riga Numero progressivo della riga 
 * @property int $TipoRiga Tipo della riga: 0 = riga clie
 * @property int $TipoQuad Indica se è una riga normale o
 * @property string|null $Cd_CGConto
 * @property string|null $Cd_CF Codice Cliente/Fornitore
 * @property string|null $ControPartita Codice contropartita
 * @property int|null $ControPartitaIsCF Indica se il campo controparti
 * @property string|null $DareAvere D dare ; A avere
 * @property string|null $Cd_CGRegistro Codice registro iva (solo per 
 * @property int|null $PartAnno Anno di competenza della parti
 * @property string|null $PartNum Numero della partita permesso 
 * @property string|null $Cd_CGLiq
 * @property string|null $Cd_Aliquota Codice IVA
 * @property int $DaVentilare
 * @property float $ImportoPartitaV
 * @property float $ImponibileV Imponibile della riga in Valut
 * @property float $ImponibileE Imponibile della riga in Euro 
 * @property float $ImpostaV
 * @property float $ImpostaE
 * @property float $ImportoV Importo riga in valuta
 * @property float $ImportoE Importo riga in Euro
 * @property float $ImponibileINDV
 * @property float $ImponibileINDE
 * @property float $ImportoIndV Parte indetraibile dell'impost
 * @property float $ImportoIndE Parte indetraibile dell'impost
 * @property int|null $AnnoPlafond Anno per il plafond soltanto p
 * @property int|null $MesePlafond Mese per il plafond soltanto p
 * @property float|null $PercVen % di ventilazione dell'IVA
 * @property float|null $PercIva % Iva riportata dalla tabella 
 * @property float|null $PercInd % Iva indetraibile riportata d
 * @property string|null $Cd_CGCausale Codice causale contabile (repl
 * @property string $TipoCausale Tipo della causale contabile. 
 * @property int $Cespite
 * @property int $Leasing
 * @property string|null $DtReg Data di registrazione
 * @property string|null $DtSaldo Data relativa al saldo contabi
 * @property string|null $DataCompIva
 * @property string|null $Cd_CGEsercizio_R Codice esercizio relativo alla
 * @property string|null $Cd_CGEsercizio_S (Vedi descrizione dello stesso
 * @property string|null $Cd_CSCausale Codice Causale x Cespiti
 * @property string|null $Cd_CS Codice dell'eventuale cespite 
 * @property string|null $Cd_VL
 * @property float $Cambio Valore del cambio riportato da
 * @property int $Decimali Numero decimali da applicare p
 * @property int $Provvisorio
 * @property string|null $Note_CGMovR
 * @property string|null $Reserved_1 Utilizzato internamente dal TR
 * @property int $Ritenuta
 * @property string|null $Cd_CAFormula
 * @property int $CAPartenza
 * @property int $CADurata
 * @property string|null $Cd_CAVda
 * @property string|null $Cd_SottoCommessa
 * @property string|null $Cd_CACda
 * @property float $CS_Ven_Val
 * @property int $CS_Ven_Def
 * @property int $SegnoDare
 * @property int $SegnoAvere
 * @property int $SegnoDareAvere
 * @property int $SegnoCF
 * @property string|null $RR_DataInizio
 * @property string|null $RR_DataFine
 * @property string|null $RR_Cd_CGConto
 * @property string|null $ExtraInfo
 * @property string|null $OS_Causale
 * @property string $TipoBeneServizio
 * @property int $UserFlag1
 * @property int $UserFlag2
 * @property int $OroArgento
 * @property int $DestinatoRivendita
 * @property string|null $CD_CGOver3000C
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string|null $Ts
 * @property int|null $SegnoTipoMovimento
 * @property string|null $Cd_ReverseCharge
 * @property string|null $CD_BU
 * @property float $CD_BU_Quantita
 * @property float $CD_BU_Margine
 * @property int|null $Riconciliato
 * @property int|null $Id_RBTes
 * @property int|null $Id_CGMovR_CP
 * @property int $Banca
 * @property int|null $xID_OC_Ribaltamento Id OC Creato
 * @property int $xRigaRitenutaAcconto Riga Ritenuta Acconto
 * @property int $xRigaRitenutaEnasarco Riga Ritenuta Enasarco
 * @property string|null $xCd_CGConto_Origine Conto originario sostituito da
 * @property int $xGirocontoTransitorio Riga di Giroconto Transitorio
 * @property int|null $xId_CGMovR_P_RitenuteCA Riga Parent da cui deriva la r
 * @property int|null $xId_SC_P_RitenuteCA Scadenza Parent da cui deriva 
 * @property int $xRilevazioneRitenute Indica se ? una riga di rileva
 * @property int|null $Id_CSCredImpUtilizzo
 */
class Cgmovr extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cgmovr';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id_CGMovT', 'TipoRiga', 'SegnoDare', 'SegnoAvere', 'SegnoDareAvere', 'SegnoCF'], 'required'],
            [['Id_CGMovT', 'Id_Sc', 'SegnoMovimento', 'TipoContab', 'ContoMerceSpesa', 'IvaSospesa', 'Riga', 'TipoRiga', 'TipoQuad', 'ControPartitaIsCF', 'PartAnno', 'DaVentilare', 'AnnoPlafond', 'MesePlafond', 'Cespite', 'Leasing', 'Decimali', 'Provvisorio', 'Ritenuta', 'CAPartenza', 'CADurata', 'CS_Ven_Def', 'SegnoDare', 'SegnoAvere', 'SegnoDareAvere', 'SegnoCF', 'UserFlag1', 'UserFlag2', 'OroArgento', 'DestinatoRivendita', 'SegnoTipoMovimento', 'Riconciliato', 'Id_RBTes', 'Id_CGMovR_CP', 'Banca', 'xID_OC_Ribaltamento', 'xRigaRitenutaAcconto', 'xRigaRitenutaEnasarco', 'xGirocontoTransitorio', 'xId_CGMovR_P_RitenuteCA', 'xId_SC_P_RitenuteCA', 'xRilevazioneRitenute', 'Id_CSCredImpUtilizzo'], 'integer'],
            [['ImportoPartitaV', 'ImponibileV', 'ImponibileE', 'ImpostaV', 'ImpostaE', 'ImportoV', 'ImportoE', 'ImponibileINDV', 'ImponibileINDE', 'ImportoIndV', 'ImportoIndE', 'PercVen', 'PercIva', 'PercInd', 'Cambio', 'CS_Ven_Val', 'CD_BU_Quantita', 'CD_BU_Margine'], 'number'],
            [['DtReg', 'DtSaldo', 'DataCompIva', 'RR_DataInizio', 'RR_DataFine', 'TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['Note_CGMovR', 'ExtraInfo'], 'string'],
            [['TipoMovimento', 'DareAvere', 'TipoCausale', 'TipoBeneServizio'], 'string', 'max' => 1],
            [['Cd_CGConto', 'ControPartita', 'Cd_CAVda', 'RR_Cd_CGConto', 'xCd_CGConto_Origine'], 'string', 'max' => 12],
            [['Cd_CF'], 'string', 'max' => 7],
            [['Cd_CGRegistro'], 'string', 'max' => 2],
            [['PartNum', 'Reserved_1'], 'string', 'max' => 10],
            [['Cd_CGLiq', 'Cd_Aliquota', 'Cd_CGCausale', 'Cd_CSCausale', 'Cd_VL', 'Cd_ReverseCharge'], 'string', 'max' => 3],
            [['Cd_CGEsercizio_R', 'Cd_CGEsercizio_S'], 'string', 'max' => 4],
            [['Cd_CS', 'Cd_CAFormula', 'Cd_SottoCommessa', 'Cd_CACda', 'CD_CGOver3000C', 'CD_BU'], 'string', 'max' => 20],
            [['OS_Causale'], 'string', 'max' => 5],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['Cd_CACda'], 'exist', 'skipOnError' => true, 'targetClass' => CACda::className(), 'targetAttribute' => ['Cd_CACda' => 'Cd_CACda']],
            [['Cd_CF'], 'exist', 'skipOnError' => true, 'targetClass' => CF::className(), 'targetAttribute' => ['Cd_CF' => 'Cd_CF']],
            [['xCd_CGConto_Origine'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['xCd_CGConto_Origine' => 'Cd_CGConto']],
            [['xId_CGMovR_P_RitenuteCA'], 'exist', 'skipOnError' => true, 'targetClass' => CGMovR::className(), 'targetAttribute' => ['xId_CGMovR_P_RitenuteCA' => 'Id_CGMovR']],
            [['xId_SC_P_RitenuteCA'], 'exist', 'skipOnError' => true, 'targetClass' => SC::className(), 'targetAttribute' => ['xId_SC_P_RitenuteCA' => 'Id_SC']],
            [['xID_OC_Ribaltamento'], 'exist', 'skipOnError' => true, 'targetClass' => DOTes::className(), 'targetAttribute' => ['xID_OC_Ribaltamento' => 'Id_DoTes']],
            [['Id_CSCredImpUtilizzo'], 'exist', 'skipOnError' => true, 'targetClass' => CSCredImpUtilizzo::className(), 'targetAttribute' => ['Id_CSCredImpUtilizzo' => 'Id_CSCredImpUtilizzo']],
            [['Cd_ReverseCharge'], 'exist', 'skipOnError' => true, 'targetClass' => ReverseCharge::className(), 'targetAttribute' => ['Cd_ReverseCharge' => 'Cd_ReverseCharge']],
            [['Cd_VL'], 'exist', 'skipOnError' => true, 'targetClass' => VL::className(), 'targetAttribute' => ['Cd_VL' => 'Cd_VL']],
            [['Id_CGMovT'], 'exist', 'skipOnError' => true, 'targetClass' => CGMovT::className(), 'targetAttribute' => ['Id_CGMovT' => 'Id_CGMovT']],
            [['Id_CGMovR_CP'], 'exist', 'skipOnError' => true, 'targetClass' => CGMovR::className(), 'targetAttribute' => ['Id_CGMovR_CP' => 'Id_CGMovR']],
            [['Cd_CGEsercizio_R'], 'exist', 'skipOnError' => true, 'targetClass' => CGEsercizio::className(), 'targetAttribute' => ['Cd_CGEsercizio_R' => 'Cd_CGEsercizio']],
            [['Cd_CGEsercizio_S'], 'exist', 'skipOnError' => true, 'targetClass' => CGEsercizio::className(), 'targetAttribute' => ['Cd_CGEsercizio_S' => 'Cd_CGEsercizio']],
            [['Cd_CSCausale'], 'exist', 'skipOnError' => true, 'targetClass' => CSCausale::className(), 'targetAttribute' => ['Cd_CSCausale' => 'Cd_CSCausale']],
            [['CD_CGOver3000C'], 'exist', 'skipOnError' => true, 'targetClass' => CGOver3000C::className(), 'targetAttribute' => ['CD_CGOver3000C' => 'Cd_CGOver3000C']],
            [['Cd_CGRegistro'], 'exist', 'skipOnError' => true, 'targetClass' => CGRegistro::className(), 'targetAttribute' => ['Cd_CGRegistro' => 'Cd_CGRegistro']],
            [['CD_BU'], 'exist', 'skipOnError' => true, 'targetClass' => BU::className(), 'targetAttribute' => ['CD_BU' => 'Cd_BU']],
            [['Cd_Aliquota'], 'exist', 'skipOnError' => true, 'targetClass' => Aliquota::className(), 'targetAttribute' => ['Cd_Aliquota' => 'Cd_Aliquota']],
            [['Cd_CGLiq'], 'exist', 'skipOnError' => true, 'targetClass' => CGLiq::className(), 'targetAttribute' => ['Cd_CGLiq' => 'Cd_CGliq']],
            [['Cd_CGCausale'], 'exist', 'skipOnError' => true, 'targetClass' => CGCausale::className(), 'targetAttribute' => ['Cd_CGCausale' => 'Cd_CGCausale']],
            [['Cd_CGConto'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto' => 'Cd_CGConto']],
            [['RR_Cd_CGConto'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['RR_Cd_CGConto' => 'Cd_CGConto']],
            [['Cd_SottoCommessa'], 'exist', 'skipOnError' => true, 'targetClass' => DOSottoCommessa::className(), 'targetAttribute' => ['Cd_SottoCommessa' => 'Cd_DOSottoCommessa']],
            [['Cd_CAVda'], 'exist', 'skipOnError' => true, 'targetClass' => CAVda::className(), 'targetAttribute' => ['Cd_CAVda' => 'Cd_CAVda']],
            [['Cd_CAFormula'], 'exist', 'skipOnError' => true, 'targetClass' => CAFormula::className(), 'targetAttribute' => ['Cd_CAFormula' => 'Cd_CAFormula']],
            [['Cd_CS'], 'exist', 'skipOnError' => true, 'targetClass' => CS::className(), 'targetAttribute' => ['Cd_CS' => 'Cd_CS']],
            [['Id_Sc'], 'exist', 'skipOnError' => true, 'targetClass' => SC::className(), 'targetAttribute' => ['Id_Sc' => 'Id_SC']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_CGMovR' => 'Id Cg Mov R',
            'Id_CGMovT' => 'Id Cg Mov T',
            'Id_Sc' => 'Id Sc',
            'TipoMovimento' => 'Tipo Movimento',
            'SegnoMovimento' => 'Segno Movimento',
            'TipoContab' => 'Tipo Contab',
            'ContoMerceSpesa' => 'Conto Merce Spesa',
            'IvaSospesa' => 'Iva Sospesa',
            'Riga' => 'Riga',
            'TipoRiga' => 'Tipo Riga',
            'TipoQuad' => 'Tipo Quad',
            'Cd_CGConto' => 'Cd Cg Conto',
            'Cd_CF' => 'Cd Cf',
            'ControPartita' => 'Contro Partita',
            'ControPartitaIsCF' => 'Contro Partita Is Cf',
            'DareAvere' => 'Dare Avere',
            'Cd_CGRegistro' => 'Cd Cg Registro',
            'PartAnno' => 'Part Anno',
            'PartNum' => 'Part Num',
            'Cd_CGLiq' => 'Cd Cg Liq',
            'Cd_Aliquota' => 'Cd Aliquota',
            'DaVentilare' => 'Da Ventilare',
            'ImportoPartitaV' => 'Importo Partita V',
            'ImponibileV' => 'Imponibile V',
            'ImponibileE' => 'Imponibile E',
            'ImpostaV' => 'Imposta V',
            'ImpostaE' => 'Imposta E',
            'ImportoV' => 'Importo V',
            'ImportoE' => 'Importo E',
            'ImponibileINDV' => 'Imponibile Indv',
            'ImponibileINDE' => 'Imponibile Inde',
            'ImportoIndV' => 'Importo Ind V',
            'ImportoIndE' => 'Importo Ind E',
            'AnnoPlafond' => 'Anno Plafond',
            'MesePlafond' => 'Mese Plafond',
            'PercVen' => 'Perc Ven',
            'PercIva' => 'Perc Iva',
            'PercInd' => 'Perc Ind',
            'Cd_CGCausale' => 'Cd Cg Causale',
            'TipoCausale' => 'Tipo Causale',
            'Cespite' => 'Cespite',
            'Leasing' => 'Leasing',
            'DtReg' => 'Dt Reg',
            'DtSaldo' => 'Dt Saldo',
            'DataCompIva' => 'Data Comp Iva',
            'Cd_CGEsercizio_R' => 'Cd Cg Esercizio R',
            'Cd_CGEsercizio_S' => 'Cd Cg Esercizio S',
            'Cd_CSCausale' => 'Cd Cs Causale',
            'Cd_CS' => 'Cd Cs',
            'Cd_VL' => 'Cd Vl',
            'Cambio' => 'Cambio',
            'Decimali' => 'Decimali',
            'Provvisorio' => 'Provvisorio',
            'Note_CGMovR' => 'Note Cg Mov R',
            'Reserved_1' => 'Reserved 1',
            'Ritenuta' => 'Ritenuta',
            'Cd_CAFormula' => 'Cd Ca Formula',
            'CAPartenza' => 'Ca Partenza',
            'CADurata' => 'Ca Durata',
            'Cd_CAVda' => 'Cd Ca Vda',
            'Cd_SottoCommessa' => 'Cd Sotto Commessa',
            'Cd_CACda' => 'Cd Ca Cda',
            'CS_Ven_Val' => 'Cs Ven Val',
            'CS_Ven_Def' => 'Cs Ven Def',
            'SegnoDare' => 'Segno Dare',
            'SegnoAvere' => 'Segno Avere',
            'SegnoDareAvere' => 'Segno Dare Avere',
            'SegnoCF' => 'Segno Cf',
            'RR_DataInizio' => 'Rr Data Inizio',
            'RR_DataFine' => 'Rr Data Fine',
            'RR_Cd_CGConto' => 'Rr Cd Cg Conto',
            'ExtraInfo' => 'Extra Info',
            'OS_Causale' => 'Os Causale',
            'TipoBeneServizio' => 'Tipo Bene Servizio',
            'UserFlag1' => 'User Flag1',
            'UserFlag2' => 'User Flag2',
            'OroArgento' => 'Oro Argento',
            'DestinatoRivendita' => 'Destinato Rivendita',
            'CD_CGOver3000C' => 'Cd Cg Over3000c',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'SegnoTipoMovimento' => 'Segno Tipo Movimento',
            'Cd_ReverseCharge' => 'Cd Reverse Charge',
            'CD_BU' => 'Cd Bu',
            'CD_BU_Quantita' => 'Cd Bu Quantita',
            'CD_BU_Margine' => 'Cd Bu Margine',
            'Riconciliato' => 'Riconciliato',
            'Id_RBTes' => 'Id Rb Tes',
            'Id_CGMovR_CP' => 'Id Cg Mov R Cp',
            'Banca' => 'Banca',
            'xID_OC_Ribaltamento' => 'X Id Oc Ribaltamento',
            'xRigaRitenutaAcconto' => 'X Riga Ritenuta Acconto',
            'xRigaRitenutaEnasarco' => 'X Riga Ritenuta Enasarco',
            'xCd_CGConto_Origine' => 'X Cd Cg Conto Origine',
            'xGirocontoTransitorio' => 'X Giroconto Transitorio',
            'xId_CGMovR_P_RitenuteCA' => 'X Id Cg Mov R P Ritenute Ca',
            'xId_SC_P_RitenuteCA' => 'X Id Sc P Ritenute Ca',
            'xRilevazioneRitenute' => 'X Rilevazione Ritenute',
            'Id_CSCredImpUtilizzo' => 'Id Cs Cred Imp Utilizzo',
        ];
    }
}
