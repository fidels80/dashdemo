<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "SC".
 *
 * @property int $Id_SC ID Univoco per scadenza.
 * @property int|null $Id_CGMovT ID di collegamento al moviment
 * @property int|null $Id_DOTes
 * @property int|null $Id_SCDistinta
 * @property int|null $Id_SC_P_Split Id della scadenza Parent nel c
 * @property int|null $Id_SC_P_Ins
 * @property int|null $Id_SC_P_Gruppo Id della scadenza madre.
 * @property int|null $Id_SCGruppo Id del raggruppamento.
 * @property int $TipoGruppo 1 (Normale); 2 (Madre); 3(Figl
 * @property string|null $Tipolink
 * @property string $Cd_CF Codice del Cliente/Fornitore.
 * @property string|null $Cd_CGConto_Banca
 * @property string|null $Cd_CGConto_Portafoglio
 * @property string|null $Cd_CGConto_InPortafoglio
 * @property string|null $Cd_CGConto_InSbf
 * @property string $Cd_VL Codice della valuta preferenzi
 * @property float $Cambio Valore del cambio
 * @property int $Decimali Decimali della valuta
 * @property string|null $Cd_PG Codice pagamento
 * @property string|null $Descrizione
 * @property string $DataScadenza Data scadenza.
 * @property string|null $DataPagamento Data Pagamento.
 * @property string|null $DataFattura Data della fattura che ha gene
 * @property string|null $NumFattura Numero della fattura che ha ge
 * @property string|null $Protocollo
 * @property int|null $PartAnno Anno della partita.
 * @property string|null $PartNum Numero della partita.
 * @property string $TipoRata Tipo scadenza:
   D = Rimessa 
 * @property int $Bloccata ??
 * @property int $Emessa Flag per effetto emesso.
 * @property int $Contabilizzata Flag per effetto contabilizzat
 * @property int $Pagata Flag per effetto Pagato.
 * @property int $Insoluta Flag per effetto insoluto.
 * @property int $Compensata
 * @property int $RiemessaSuInsoluto
 * @property int $NumEffetto Numero progressivo dell'effett
 * @property int $TotEffetti Totali effetti emessi per quel
 * @property float $ImportoE Importo dell'effetto in Euro.
 * @property float $ImportoV Importo effetto in valuta.
 * @property float $EmessoV
 * @property float $EmessoE
 * @property float $IncassoV
 * @property float $PercImponibile
 * @property float $PercImposta
 * @property float $PercProvvigione
 * @property string|null $NoteSC
 * @property string|null $Piazza Piazza.
 * @property string|null $Traente Traente.
 * @property int $Girate Numero girate.
 * @property int $Sollecito Sollecito.
 * @property string|null $Cd_SL Codice Sollecito
 * @property string|null $DataUltimoSollecito
 * @property string|null $DataValuta Data Valuta calcolata in autom
 * @property string|null $Cd_Simulazione
 * @property int $ProvvisorioDaDocumento
 * @property string|null $Iban
 * @property string|null $BicCode
 * @property string|null $Cd_Abicab
 * @property string|null $ContoCorrente
 * @property string|null $Cin_It
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string|null $Ts
 * @property float|null $CambioStorico
 * @property string|null $DataRivalutazione
 * @property string|null $NoteXML
 * @property string|null $CIG
 * @property string|null $CUP
 * @property string|null $SDD_IdMandato
 * @property string|null $SDD_DtMandato
 * @property string|null $SDD_SqMandato
 * @property string|null $ExtraInfo
 * @property int|null $Riconciliato
 * @property int|null $Id_RBTes
 * @property string|null $FTE_TipoPagamento
 * @property float $xQuotaV_RA Quota Ritenuta Acconto
 * @property float $xQuotaV_RE Quota Ritenuta Enasarco
 * @property float $xQuotaE_RA Quota Ritenuta Acconto ?
 * @property float $xQuotaE_RE Quota Ritenuta Enasarco ?
 * @property float|null $xImportoVNettoRitenute Importo in valuta al netto del
 * @property float|null $xImportoENettoRitenute Importo in Euro al netto delle
 * @property int $Provvisorio
 */
class Sc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'SC';
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
            [['Id_CGMovT', 'Id_DOTes', 'Id_SCDistinta', 'Id_SC_P_Split', 'Id_SC_P_Ins', 'Id_SC_P_Gruppo', 'Id_SCGruppo', 'TipoGruppo', 'Decimali', 'PartAnno', 'Bloccata', 'Emessa', 'Contabilizzata', 'Pagata', 'Insoluta', 'Compensata', 'RiemessaSuInsoluto', 'NumEffetto', 'TotEffetti', 'Girate', 'Sollecito', 'ProvvisorioDaDocumento', 'Riconciliato', 'Id_RBTes', 'Provvisorio'], 'integer'],
            [['Cd_CF', 'RiemessaSuInsoluto', 'Provvisorio'], 'required'],
            [['Cambio', 'ImportoE', 'ImportoV', 'EmessoV', 'EmessoE', 'IncassoV', 'PercImponibile', 'PercImposta', 'PercProvvigione', 'CambioStorico', 'xQuotaV_RA', 'xQuotaV_RE', 'xQuotaE_RA', 'xQuotaE_RE', 'xImportoVNettoRitenute', 'xImportoENettoRitenute'], 'number'],
            [['DataScadenza', 'DataPagamento', 'DataFattura', 'DataUltimoSollecito', 'DataValuta', 'TimeIns', 'TimeUpd', 'Ts', 'DataRivalutazione', 'SDD_DtMandato'], 'safe'],
            [['NoteSC', 'NoteXML', 'ExtraInfo'], 'string'],
            [['Tipolink', 'TipoRata', 'Cin_It', 'SDD_SqMandato'], 'string', 'max' => 1],
            [['Cd_CF'], 'string', 'max' => 7],
            [['Cd_CGConto_Banca', 'Cd_CGConto_Portafoglio', 'Cd_CGConto_InPortafoglio', 'Cd_CGConto_InSbf'], 'string', 'max' => 12],
            [['Cd_VL'], 'string', 'max' => 3],
            [['Cd_PG', 'FTE_TipoPagamento'], 'string', 'max' => 4],
            [['Descrizione'], 'string', 'max' => 80],
            [['NumFattura', 'Piazza', 'Traente'], 'string', 'max' => 20],
            [['Protocollo', 'PartNum', 'Cd_SL', 'Cd_Simulazione', 'Cd_Abicab'], 'string', 'max' => 10],
            [['Iban', 'ContoCorrente'], 'string', 'max' => 34],
            [['BicCode'], 'string', 'max' => 11],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['CIG', 'CUP'], 'string', 'max' => 15],
            [['SDD_IdMandato'], 'string', 'max' => 35],
        
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_SC' => 'Id Sc',
            'Id_CGMovT' => 'Id Cg Mov T',
            'Id_DOTes' => 'Id Do Tes',
            'Id_SCDistinta' => 'Id Sc Distinta',
            'Id_SC_P_Split' => 'Id Sc P Split',
            'Id_SC_P_Ins' => 'Id Sc P Ins',
            'Id_SC_P_Gruppo' => 'Id Sc P Gruppo',
            'Id_SCGruppo' => 'Id Sc Gruppo',
            'TipoGruppo' => 'Tipo Gruppo',
            'Tipolink' => 'Tipolink',
            'Cd_CF' => 'Cd Cf',
            'Cd_CGConto_Banca' => 'Cd Cg Conto Banca',
            'Cd_CGConto_Portafoglio' => 'Cd Cg Conto Portafoglio',
            'Cd_CGConto_InPortafoglio' => 'Cd Cg Conto In Portafoglio',
            'Cd_CGConto_InSbf' => 'Cd Cg Conto In Sbf',
            'Cd_VL' => 'Cd Vl',
            'Cambio' => 'Cambio',
            'Decimali' => 'Decimali',
            'Cd_PG' => 'Cd Pg',
            'Descrizione' => 'Descrizione',
            'DataScadenza' => 'Data Scadenza',
            'DataPagamento' => 'Data Pagamento',
            'DataFattura' => 'Data Fattura',
            'NumFattura' => 'Num Fattura',
            'Protocollo' => 'Protocollo',
            'PartAnno' => 'Part Anno',
            'PartNum' => 'Part Num',
            'TipoRata' => 'Tipo Rata',
            'Bloccata' => 'Bloccata',
            'Emessa' => 'Emessa',
            'Contabilizzata' => 'Contabilizzata',
            'Pagata' => 'Pagata',
            'Insoluta' => 'Insoluta',
            'Compensata' => 'Compensata',
            'RiemessaSuInsoluto' => 'Riemessa Su Insoluto',
            'NumEffetto' => 'Num Effetto',
            'TotEffetti' => 'Tot Effetti',
            'ImportoE' => 'Importo E',
            'ImportoV' => 'Importo V',
            'EmessoV' => 'Emesso V',
            'EmessoE' => 'Emesso E',
            'IncassoV' => 'Incasso V',
            'PercImponibile' => 'Perc Imponibile',
            'PercImposta' => 'Perc Imposta',
            'PercProvvigione' => 'Perc Provvigione',
            'NoteSC' => 'Note Sc',
            'Piazza' => 'Piazza',
            'Traente' => 'Traente',
            'Girate' => 'Girate',
            'Sollecito' => 'Sollecito',
            'Cd_SL' => 'Cd Sl',
            'DataUltimoSollecito' => 'Data Ultimo Sollecito',
            'DataValuta' => 'Data Valuta',
            'Cd_Simulazione' => 'Cd Simulazione',
            'ProvvisorioDaDocumento' => 'Provvisorio Da Documento',
            'Iban' => 'Iban',
            'BicCode' => 'Bic Code',
            'Cd_Abicab' => 'Cd Abicab',
            'ContoCorrente' => 'Conto Corrente',
            'Cin_It' => 'Cin It',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'CambioStorico' => 'Cambio Storico',
            'DataRivalutazione' => 'Data Rivalutazione',
            'NoteXML' => 'Note Xml',
            'CIG' => 'Cig',
            'CUP' => 'Cup',
            'SDD_IdMandato' => 'Sdd Id Mandato',
            'SDD_DtMandato' => 'Sdd Dt Mandato',
            'SDD_SqMandato' => 'Sdd Sq Mandato',
            'ExtraInfo' => 'Extra Info',
            'Riconciliato' => 'Riconciliato',
            'Id_RBTes' => 'Id Rb Tes',
            'FTE_TipoPagamento' => 'Fte Tipo Pagamento',
            'xQuotaV_RA' => 'X Quota V Ra',
            'xQuotaV_RE' => 'X Quota V Re',
            'xQuotaE_RA' => 'X Quota E Ra',
            'xQuotaE_RE' => 'X Quota E Re',
            'xImportoVNettoRitenute' => 'X Importo V Netto Ritenute',
            'xImportoENettoRitenute' => 'X Importo E Netto Ritenute',
            'Provvisorio' => 'Provvisorio',
        ];
    }
}
