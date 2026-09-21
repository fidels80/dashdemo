<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "CGMovT".
 *
 * @property int $Id_CGMovT Id Univoco.
 * @property int|null $Id_CGMovT_P Id del movimento Parent nel ca
 * @property int|null $Id_DOTes Id Del documento che ha genera
 * @property string|null $TipoLink Tipologio di movimento collega
 * @property string $DtReg Data di registrazione.
 * @property int $TipoFattura
 * @property string $TipoMovimento Campo ad utilizzo interno : A 
 * @property int|null $SegnoMovimento Utilizzo Interno. In genere 1
 * @property int $IvaSospesa Riportato da CGCausale, indica
 * @property int $SezioneIva Riportato da CGCausale, indica
 * @property int $BollaDoganale 1 Nel caso si tratti di una re
 * @property string $Corrispettivo
 * @property int $Ritenuta Indica se si tratta di una fat
 * @property string|null $Cd_CGEsercizio_R Codice Esercizio contabile (re
 * @property string|null $DtSaldo Data di riferimento per il com
 * @property string|null $Cd_CGEsercizio_S Codice di esercizio di riferim
 * @property string|null $DtRif Data del documento di riferime
 * @property string|null $NumRif Numero del documento di riferi
 * @property string|null $NumProt Numero di protocollo assegnato
 * @property string|null $Descrizione Descrizione del movimento.
 * @property string|null $Note_ Note per il movimento.
 * @property string $Cd_CGCausale Codice Causale Contabile deriv
 * @property string $TipoCausale Tipo Causale :0 - Normale; 1 -
 * @property string|null $Cd_CGRegistro Codice registro IVA
 * @property string|null $Cd_CN Codice contatore per il regist
 * @property string|null $Cd_VL Codice della Valuta del movime
 * @property string|null $Cd_PG Codice pagamento
 * @property string|null $Cd_CF Codice Cliente/Forntiore prese
 * @property string|null $Cd_CGConto
 * @property string|null $Cd_CGConto_Banca Conto della banca di sconto da
 * @property float $Cambio Cambio applicato (Fattore divi
 * @property float $TotIvaE Totale IVA in Euro (Calcolato 
 * @property float $TotIvaV Totale IVA in Valuta (Calcolat
 * @property float $TotIndE Totale IVA indetraibile in Eur
 * @property float $TotIndV Totale IVA indetraibile in Val
 * @property float $ImportoE Totale documento in Euro
 * @property float $ImportoV Totale documento in valuta
 * @property float $ImponibileE Totale base imponibile IVA in 
 * @property float $ImponibileV Totale base imponibile IVA in 
 * @property float $TotDareE Totale Dare in Euro del movime
 * @property float $TotDareV Totale Dare in valuta del movi
 * @property float $TotAvereE Totale Avere in Euro del movim
 * @property float $TotAvereV Totale Avere in valuta del mov
 * @property float $AccontoE Importo dell'acconto in Euro.
 * @property float $AccontoV Importo dell'acconto in valuta
 * @property float $AbbuonoE
 * @property float $AbbuonoV
 * @property float $ScontoE
 * @property float $ScontoV
 * @property float $Omaggio_ME
 * @property float $Omaggio_MV
 * @property float $Omaggio_IE
 * @property float $Omaggio_IV
 * @property int $NPagGiornale Numero di pagina giornale di c
 * @property int $NRigaDef Numero di riga giornale di con
 * @property int $NRigaRgIva Numero di riga registro iva se
 * @property string|null $PartNum Numero di partita associato al
 * @property int|null $PartAnno Anno della partita associato a
 * @property string|null $Cd_Agente_1 Codice Agente chi associare le
 * @property string $Provvigione_1
 * @property float $ValProvvigioneE_1 importo Provvigione 1 In Euro.
 * @property float $ValProvvigioneV_1 Importo Provvigione 1 in valut
 * @property string|null $Cd_Agente_2
 * @property string $Provvigione_2
 * @property float $ValProvvigioneE_2 Provvigione in Euro per agente
 * @property float $ValProvvigioneV_2 Provvigione in valuta per agen
 * @property int $Decimali Numero di decimali configurati
 * @property int $ProvvisorioDaDocumento Indica che il movimento contab
 * @property string|null $Cd_Simulazione Codice di Simulazione del movi
 * @property int|null $Id_CGLiqIva
 * @property string|null $Iban
 * @property string|null $BicCode
 * @property string|null $Cd_Abicab
 * @property string|null $DataPag
 * @property string|null $DtCompRA
 * @property int $ConsumatoreFinale
 * @property int $Compensazione
 * @property int $OS_Exported
 * @property int $SP_Exported
 * @property int $CGCorrispondenzaIvaMerce
 * @property string|null $PeriodoRiferimento
 * @property string|null $Cd_CF_BL
 * @property string|null $Cd_CGOver3000C
 * @property int $UE
 * @property int $NoLiquidazione
 * @property int $EsclusoSpesometro
 * @property string|null $Noleggio
 * @property int $IvaNonEsposta
 * @property int $DocumentoRiepilogativo
 * @property int $Autofattura
 * @property string|null $Reserved_1 Utilizzato internamente dal TR
 * @property string $UserIns Utente esecutore dell'inserime
 * @property string $UserUpd Utente esecutore dell'ultimo a
 * @property string $TimeIns Data di inserimento del movime
 * @property string $TimeUpd Data dell'ultimo aggiornamento
 * @property string|null $Ts
 * @property int $IvaSplit
 * @property string|null $CD_ReverseCharge
 * @property float $TotIvaRCV
 * @property float $TotIvaRCE
 * @property int $DF_Escluso
 * @property int $IvaMargine
 * @property string|null $DF_TipoDocumento
 * @property string|null $FTE_Status
 * @property float $TotIvaSPV
 * @property float $TotIvaSPE
 * @property int $xGirocontoRitenuta Registrazione Collegata di gir
 * @property int $Provvisorio
 */
class Cgmovt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'CGMovT';
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
            [['Id_CGMovT_P', 'Id_DOTes', 'TipoFattura', 'SegnoMovimento', 'IvaSospesa', 'SezioneIva', 'BollaDoganale', 'Ritenuta', 'NPagGiornale', 'NRigaDef', 'NRigaRgIva', 'PartAnno', 'Decimali', 'ProvvisorioDaDocumento', 'Id_CGLiqIva', 'ConsumatoreFinale', 'Compensazione', 'OS_Exported', 'SP_Exported', 'CGCorrispondenzaIvaMerce', 'UE', 'NoLiquidazione', 'EsclusoSpesometro', 'IvaNonEsposta', 'DocumentoRiepilogativo', 'Autofattura', 'IvaSplit', 'DF_Escluso', 'IvaMargine', 'xGirocontoRitenuta', 'Provvisorio'], 'integer'],
            [['DtReg', 'DtSaldo', 'DtRif', 'DataPag', 'DtCompRA', 'PeriodoRiferimento', 'TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['Note_'], 'string'],
            [['Cd_CGCausale', 'UE', 'Provvisorio'], 'required'],
            [['Cambio', 'TotIvaE', 'TotIvaV', 'TotIndE', 'TotIndV', 'ImportoE', 'ImportoV', 'ImponibileE', 'ImponibileV', 'TotDareE', 'TotDareV', 'TotAvereE', 'TotAvereV', 'AccontoE', 'AccontoV', 'AbbuonoE', 'AbbuonoV', 'ScontoE', 'ScontoV', 'Omaggio_ME', 'Omaggio_MV', 'Omaggio_IE', 'Omaggio_IV', 'ValProvvigioneE_1', 'ValProvvigioneV_1', 'ValProvvigioneE_2', 'ValProvvigioneV_2', 'TotIvaRCV', 'TotIvaRCE', 'TotIvaSPV', 'TotIvaSPE'], 'number'],
            [['TipoLink', 'TipoMovimento', 'Corrispettivo', 'TipoCausale', 'Noleggio'], 'string', 'max' => 1],
            [['Cd_CGEsercizio_R', 'Cd_CGEsercizio_S', 'Cd_PG', 'DF_TipoDocumento'], 'string', 'max' => 4],
            [['NumRif', 'Cd_CGOver3000C'], 'string', 'max' => 20],
            [['NumProt', 'PartNum', 'Cd_Simulazione', 'Cd_Abicab', 'Reserved_1'], 'string', 'max' => 10],
            [['Descrizione'], 'string', 'max' => 50],
            [['Cd_CGCausale', 'Cd_CN', 'Cd_VL', 'Cd_Agente_1', 'Cd_Agente_2', 'CD_ReverseCharge', 'FTE_Status'], 'string', 'max' => 3],
            [['Cd_CGRegistro'], 'string', 'max' => 2],
            [['Cd_CF', 'Cd_CF_BL'], 'string', 'max' => 7],
            [['Cd_CGConto', 'Cd_CGConto_Banca'], 'string', 'max' => 12],
            [['Provvigione_1', 'Provvigione_2'], 'string', 'max' => 16],
            [['Iban'], 'string', 'max' => 34],
            [['BicCode'], 'string', 'max' => 11],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['Cd_CN'], 'exist', 'skipOnError' => true, 'targetClass' => CN::className(), 'targetAttribute' => ['Cd_CN' => 'Cd_CN']],
            [['Id_CGMovT_P'], 'exist', 'skipOnError' => true, 'targetClass' => Cgmovt::className(), 'targetAttribute' => ['Id_CGMovT_P' => 'Id_CGMovT']],
            [['Cd_CGEsercizio_R'], 'exist', 'skipOnError' => true, 'targetClass' => CGEsercizio::className(), 'targetAttribute' => ['Cd_CGEsercizio_R' => 'Cd_CGEsercizio']],
            [['Cd_CGEsercizio_S'], 'exist', 'skipOnError' => true, 'targetClass' => CGEsercizio::className(), 'targetAttribute' => ['Cd_CGEsercizio_S' => 'Cd_CGEsercizio']],
            [['Cd_PG'], 'exist', 'skipOnError' => true, 'targetClass' => PG::className(), 'targetAttribute' => ['Cd_PG' => 'Cd_PG']],
            [['Cd_CGOver3000C'], 'exist', 'skipOnError' => true, 'targetClass' => CGOver3000C::className(), 'targetAttribute' => ['Cd_CGOver3000C' => 'Cd_CGOver3000C']],
            [['Cd_CGRegistro'], 'exist', 'skipOnError' => true, 'targetClass' => CGRegistro::className(), 'targetAttribute' => ['Cd_CGRegistro' => 'Cd_CGRegistro']],
            [['Cd_CGCausale'], 'exist', 'skipOnError' => true, 'targetClass' => CGCausale::className(), 'targetAttribute' => ['Cd_CGCausale' => 'Cd_CGCausale']],
            [['Id_CGLiqIva'], 'exist', 'skipOnError' => true, 'targetClass' => CGLiqIva::className(), 'targetAttribute' => ['Id_CGLiqIva' => 'Id_CGLiqIva']],
            [['Cd_CGConto'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto' => 'Cd_CGConto']],
            [['Cd_CGConto_Banca'], 'exist', 'skipOnError' => true, 'targetClass' => Banca::className(), 'targetAttribute' => ['Cd_CGConto_Banca' => 'Cd_CGConto']],
            [['Cd_Agente_1'], 'exist', 'skipOnError' => true, 'targetClass' => Agente::className(), 'targetAttribute' => ['Cd_Agente_1' => 'Cd_Agente']],
            [['Cd_Agente_2'], 'exist', 'skipOnError' => true, 'targetClass' => Agente::className(), 'targetAttribute' => ['Cd_Agente_2' => 'Cd_Agente']],
            [['Id_DOTes'], 'exist', 'skipOnError' => true, 'targetClass' => DOTes::className(), 'targetAttribute' => ['Id_DOTes' => 'Id_DoTes']],
            [['Cd_CF_BL'], 'exist', 'skipOnError' => true, 'targetClass' => CF::className(), 'targetAttribute' => ['Cd_CF_BL' => 'Cd_CF']],
            [['Cd_CF'], 'exist', 'skipOnError' => true, 'targetClass' => CF::className(), 'targetAttribute' => ['Cd_CF' => 'Cd_CF']],
            [['Cd_Simulazione'], 'exist', 'skipOnError' => true, 'targetClass' => Simulazione::className(), 'targetAttribute' => ['Cd_Simulazione' => 'Cd_Simulazione']],
            [['Cd_VL'], 'exist', 'skipOnError' => true, 'targetClass' => VL::className(), 'targetAttribute' => ['Cd_VL' => 'Cd_VL']],
            [['CD_ReverseCharge'], 'exist', 'skipOnError' => true, 'targetClass' => ReverseCharge::className(), 'targetAttribute' => ['CD_ReverseCharge' => 'Cd_ReverseCharge']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_CGMovT' => 'Id Cg Mov T',
            'Id_CGMovT_P' => 'Id Cg Mov T P',
            'Id_DOTes' => 'Id Do Tes',
            'TipoLink' => 'Tipo Link',
            'DtReg' => 'Dt Reg',
            'TipoFattura' => 'Tipo Fattura',
            'TipoMovimento' => 'Tipo Movimento',
            'SegnoMovimento' => 'Segno Movimento',
            'IvaSospesa' => 'Iva Sospesa',
            'SezioneIva' => 'Sezione Iva',
            'BollaDoganale' => 'Bolla Doganale',
            'Corrispettivo' => 'Corrispettivo',
            'Ritenuta' => 'Ritenuta',
            'Cd_CGEsercizio_R' => 'Cd Cg Esercizio R',
            'DtSaldo' => 'Dt Saldo',
            'Cd_CGEsercizio_S' => 'Cd Cg Esercizio S',
            'DtRif' => 'Dt Rif',
            'NumRif' => 'Num Rif',
            'NumProt' => 'Num Prot',
            'Descrizione' => 'Descrizione',
            'Note_' => 'Note',
            'Cd_CGCausale' => 'Cd Cg Causale',
            'TipoCausale' => 'Tipo Causale',
            'Cd_CGRegistro' => 'Cd Cg Registro',
            'Cd_CN' => 'Cd Cn',
            'Cd_VL' => 'Cd Vl',
            'Cd_PG' => 'Cd Pg',
            'Cd_CF' => 'Cd Cf',
            'Cd_CGConto' => 'Cd Cg Conto',
            'Cd_CGConto_Banca' => 'Cd Cg Conto Banca',
            'Cambio' => 'Cambio',
            'TotIvaE' => 'Tot Iva E',
            'TotIvaV' => 'Tot Iva V',
            'TotIndE' => 'Tot Ind E',
            'TotIndV' => 'Tot Ind V',
            'ImportoE' => 'Importo E',
            'ImportoV' => 'Importo V',
            'ImponibileE' => 'Imponibile E',
            'ImponibileV' => 'Imponibile V',
            'TotDareE' => 'Tot Dare E',
            'TotDareV' => 'Tot Dare V',
            'TotAvereE' => 'Tot Avere E',
            'TotAvereV' => 'Tot Avere V',
            'AccontoE' => 'Acconto E',
            'AccontoV' => 'Acconto V',
            'AbbuonoE' => 'Abbuono E',
            'AbbuonoV' => 'Abbuono V',
            'ScontoE' => 'Sconto E',
            'ScontoV' => 'Sconto V',
            'Omaggio_ME' => 'Omaggio Me',
            'Omaggio_MV' => 'Omaggio Mv',
            'Omaggio_IE' => 'Omaggio Ie',
            'Omaggio_IV' => 'Omaggio Iv',
            'NPagGiornale' => 'N Pag Giornale',
            'NRigaDef' => 'N Riga Def',
            'NRigaRgIva' => 'N Riga Rg Iva',
            'PartNum' => 'Part Num',
            'PartAnno' => 'Part Anno',
            'Cd_Agente_1' => 'Cd Agente 1',
            'Provvigione_1' => 'Provvigione 1',
            'ValProvvigioneE_1' => 'Val Provvigione E 1',
            'ValProvvigioneV_1' => 'Val Provvigione V 1',
            'Cd_Agente_2' => 'Cd Agente 2',
            'Provvigione_2' => 'Provvigione 2',
            'ValProvvigioneE_2' => 'Val Provvigione E 2',
            'ValProvvigioneV_2' => 'Val Provvigione V 2',
            'Decimali' => 'Decimali',
            'ProvvisorioDaDocumento' => 'Provvisorio Da Documento',
            'Cd_Simulazione' => 'Cd Simulazione',
            'Id_CGLiqIva' => 'Id Cg Liq Iva',
            'Iban' => 'Iban',
            'BicCode' => 'Bic Code',
            'Cd_Abicab' => 'Cd Abicab',
            'DataPag' => 'Data Pag',
            'DtCompRA' => 'Dt Comp Ra',
            'ConsumatoreFinale' => 'Consumatore Finale',
            'Compensazione' => 'Compensazione',
            'OS_Exported' => 'Os Exported',
            'SP_Exported' => 'Sp Exported',
            'CGCorrispondenzaIvaMerce' => 'Cg Corrispondenza Iva Merce',
            'PeriodoRiferimento' => 'Periodo Riferimento',
            'Cd_CF_BL' => 'Cd Cf Bl',
            'Cd_CGOver3000C' => 'Cd Cg Over3000c',
            'UE' => 'Ue',
            'NoLiquidazione' => 'No Liquidazione',
            'EsclusoSpesometro' => 'Escluso Spesometro',
            'Noleggio' => 'Noleggio',
            'IvaNonEsposta' => 'Iva Non Esposta',
            'DocumentoRiepilogativo' => 'Documento Riepilogativo',
            'Autofattura' => 'Autofattura',
            'Reserved_1' => 'Reserved 1',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'IvaSplit' => 'Iva Split',
            'CD_ReverseCharge' => 'Cd Reverse Charge',
            'TotIvaRCV' => 'Tot Iva Rcv',
            'TotIvaRCE' => 'Tot Iva Rce',
            'DF_Escluso' => 'Df Escluso',
            'IvaMargine' => 'Iva Margine',
            'DF_TipoDocumento' => 'Df Tipo Documento',
            'FTE_Status' => 'Fte Status',
            'TotIvaSPV' => 'Tot Iva Spv',
            'TotIvaSPE' => 'Tot Iva Spe',
            'xGirocontoRitenuta' => 'X Giroconto Ritenuta',
            'Provvisorio' => 'Provvisorio',
        ];
    }
}
