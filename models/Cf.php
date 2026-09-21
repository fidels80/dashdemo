<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cf".
 *
 * @property int $Id_CF
 * @property string $Cd_CF Codice cliente/fornitore.
 * @property string $Descrizione Descrizione Cliente Fornitore
 * @property string|null $Indirizzo Indirizzo
 * @property string|null $Localita
 * @property string|null $Cap
 * @property string|null $Cd_Provincia
 * @property string|null $Cd_Nazione Codice ISO
 * @property string|null $Cd_NazioneProvincia
 * @property string|null $PartitaIva Partita IVA
 * @property string|null $CodiceFiscale Codice Fiscale
 * @property string|null $CodiceIPA
 * @property string|null $CodiceFPR
 * @property string $TipoDitta Allegati:
   G = persona giuri
 * @property int $Cliente
 * @property int $Fornitore
 * @property int $ProspectCliente
 * @property int $ProspectFornitore
 * @property string $TipoCF
 * @property string|null $TipoCF_1
 * @property string|null $Cd_CFClasse1 Codice Classificazione statist
 * @property string|null $Cd_CFClasse2 Codice Classificazione statist
 * @property string|null $Cd_CFClasse3 Codice Classificazione statist
 * @property string|null $Cd_CFGruppo1 Codice Gruppo clienti/fornitor
 * @property string|null $Cd_CFGruppo2 Codice Gruppo clienti/fornitor
 * @property string|null $Cd_CFGruppo3 Codice Gruppo clienti/fornitor
 * @property string|null $Cd_PG Codice Pagamento 
 * @property string|null $Cd_CGConto_Mastro
 * @property string|null $Cd_CGConto_Banca
 * @property string|null $Cd_CGConto_Merce
 * @property string|null $Cd_LS_1
 * @property string|null $Cd_LS_2
 * @property string $Cd_VL Codice Valuta
 * @property string|null $Cd_CFStato Codice blocco cliente/fornitor
 * @property string|null $Cd_CFZona Codice Zona
 * @property string|null $Cd_CFSettore Codice Settore 
 * @property string|null $Cd_DOPorto Codice Porto 
 * @property string|null $Cd_DOSped Codice Spedizione
 * @property string|null $Cd_DOVettore Codice Vettore 
 * @property string|null $Cd_SL
 * @property string|null $Cd_Agente_1 Codice Agente associato al cli
 * @property string|null $Cd_Agente_2
 * @property int $Id_Lingua
 * @property string $Sconto Espressione per la % di sconto
 * @property string $Provvigione Espressione per la % di Provvi
 * @property int $SpeseIncasso Valore Logico, se TRUE vengono
 * @property int $SpeseBolli Valore Logico, se TRUE vengono
 * @property float $Fido Importo fido applicato.
 * @property string|null $Note_CF Annotazioni libere
 * @property int $ScGiornoFisso1 Primo giorno fisso per calcolo
 * @property int $ScGiornoFisso2 Secondo giorno fisso per calco
 * @property int $ScGiornoFisso3 Terzo giorno fisso per calcolo
 * @property string|null $Cd_INTRAConsegna Codice modalità di consegna IN
 * @property string|null $Cd_INTRATrasporto Codice modalità di trasporto I
 * @property string|null $Cd_INTRATransazione Codice transazione INTRA.
 * @property string|null $Cd_Nazione_Origine Proposto come default Pease or
 * @property string|null $Cd_Provincia_Origine Proposto come default Provinci
 * @property string|null $Cd_Nazione_Destinazione Proposto come default Pease de
 * @property string|null $Cd_Provincia_Destinazione Proposto come default provinci
 * @property string|null $Cd_Nazione_Provenienza Proposto come default Nazione 
 * @property int $PdAbilita Campo relativo alla emissione 
 * @property int $PdPriorita Campo relativo alla emissione 
 * @property int $PdInt4ClienteFatturazione Campo relativo alla emissione 
 * @property int $PdBrk4DocumentoPrelevato Campo relativo alla emissione 
 * @property int $PdBrk4ClienteFornitore Campo relativo alla emissione 
 * @property int $PdBrk4Pagamento Campo relativo alla emissione 
 * @property int $PdBrk4Agente_1 Campo relativo alla emissione 
 * @property int $PdBrk4Agente_2 Campo relativo alla emissione 
 * @property int $PdBrk4Zona Campo relativo alla emissione 
 * @property int $PdBrk4SedeAmministrativa Campo relativo alla emissione 
 * @property int $PdBrk4DestinazioneDiversa Campo relativo alla emissione 
 * @property int $Ue
 * @property int $Intra
 * @property int $Ritenuta Fornitore gestito a ritenuta d
 * @property int $IvaSospesa Valore Logico usato per l'asso
 * @property int $IvaSplit
 * @property int $ProceduraConcorsuale
 * @property int $ConsumatoreFinale
 * @property int $EntePubblico
 * @property int $Obsoleto
 * @property int $Condominio
 * @property int $Elenchi
 * @property string|null $Cd_CACda
 * @property string|null $Cd_Aliquota
 * @property string|null $Cd_CF_Fatt
 * @property string|null $Iban
 * @property string|null $BicCode
 * @property string $Cd_Abicab
 * @property string $ContoCorrente
 * @property int $Tipofattura
 * @property string $Ricarica
 * @property string|null $Attributi
 * @property int $EvtnInfo
 * @property string|null $NoteXML
 * @property string|null $HistoryData
 * @property int $EsclusoOver3000
 * @property int $EsclusoSpesometro
 * @property int $EsclusoBlackList
 * @property string|null $NRea
 * @property string|null $Cd_F24Comune
 * @property string|null $Cd_AE_NaturaGiuridica
 * @property string|null $Cd_AE_Atecofin
 * @property string|null $SDD_IdMandato
 * @property string|null $SDD_DtMandato
 * @property string|null $CD_ReverseCharge
 * @property string|null $Peppol_Endpoint
 * @property string|null $SO_Indirizzo
 * @property string|null $SO_NumeroCivico
 * @property string|null $SO_Cap
 * @property string|null $SO_Localita
 * @property string|null $SO_Cd_Provincia
 * @property string|null $SO_Cd_Nazione
 * @property string|null $RF_PartitaIva
 * @property string|null $RF_RagioneSociale
 * @property string|null $RF_Nome
 * @property string|null $RF_Cognome
 * @property int $DF_Escluso
 * @property string $UserIns Utente che ha eseguito l'Inser
 * @property string $UserUpd Utente che ha eseguito l'ultim
 * @property string $TimeIns Data inserimento del record.
 
 * @property string $TimeUpd Data ultimo aggiornamento del 
 * @property string|null $Ts
 * @property int $FE_AddASW
 * @property int|null $xNOControlloNumDOC Flag Controllo cliente per Num
 * @property int|null $xBlocco_NumDoc Numero Doc per Blocco
 * @property string|null $ExtraInfo
 * @property int $FE_Ignore_CodiceFPR
 * @property int $xCalcolaRitenuta Calcola Ritenuta su fatture in
 * @property int $xCalcolaEnasarco Calcola Ritenuta Enasarco su f
 * @property int $xEnasarcoManuale Abilita l'inserimento manuale 
 * @property float $xMassimaleRitenutaAcconto Massimale Ritenuta d'acconto
 * @property float $xPercentualeRitenuta Percentuale Ritenuta d'acconto
 * @property float $xPercentualeEnasarco Percentuale Ritenuta Enasarco
 * @property float $xPercentualeImponibileRitenuta Percentuale Imponibile su cui 
 * @property float $xPercentualeImponibileEnasarco Percentuale Imponibile su cui 
 * @property string|null $xCd_CGConto_RitenutaAcconto Conto Erario c/Ritenute d'acco
 * @property string|null $xCd_CGConto_RitenutaEnasarco Conto Erario c/Ritenute Enasar
 * @property string|null $xCd_CGConto_ContributoIntegrativo Conto Contributo Integrativo
 * @property float $xPercentualeContributoIntegrativo Percentuale Contributo Integra
 * @property string|null $RF_Cd_Nazione
 * @property string|null $Cd_NazioneIva
 * @property int $FTE_AutoTipo Anagrafica dedicata al documen
 * @property string|null $FTE_RegimeFiscale Dato obbligatorio del cedente 
 * @property string|null $FTE_Cd_CN Eventuale contatore per numera
 * @property float|null $xPerc_Ribaltamento Percentuale di Ribaltamento
 * @property string|null $Cd_ParcT Codice Parcellazione
 */
class Cf extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cf';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db5');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Cd_CF', 'Cliente', 'Fornitore', 'ProspectCliente', 'ProspectFornitore', 'TipoCF', 'Ue', 'Cd_Abicab', 'ContoCorrente'], 'required'],
            [['Cliente', 'Fornitore', 'ProspectCliente', 'ProspectFornitore', 'Id_Lingua', 'SpeseIncasso', 'SpeseBolli', 'ScGiornoFisso1', 'ScGiornoFisso2', 'ScGiornoFisso3', 'PdAbilita', 'PdPriorita', 'PdInt4ClienteFatturazione', 'PdBrk4DocumentoPrelevato', 'PdBrk4ClienteFornitore', 'PdBrk4Pagamento', 'PdBrk4Agente_1', 'PdBrk4Agente_2', 'PdBrk4Zona', 'PdBrk4SedeAmministrativa', 'PdBrk4DestinazioneDiversa', 'Ue', 'Intra', 'Ritenuta', 'IvaSospesa', 'IvaSplit', 'ProceduraConcorsuale', 'ConsumatoreFinale', 'EntePubblico', 'Obsoleto', 'Condominio', 'Elenchi', 'Tipofattura', 'EvtnInfo', 'EsclusoOver3000', 'EsclusoSpesometro', 'EsclusoBlackList', 'DF_Escluso', 'FE_AddASW', 'xNOControlloNumDOC', 'xBlocco_NumDoc', 'FE_Ignore_CodiceFPR', 'xCalcolaRitenuta', 'xCalcolaEnasarco', 'xEnasarcoManuale', 'FTE_AutoTipo'], 'integer'],
            [['Fido', 'xMassimaleRitenutaAcconto', 'xPercentualeRitenuta', 'xPercentualeEnasarco', 'xPercentualeImponibileRitenuta', 'xPercentualeImponibileEnasarco', 'xPercentualeContributoIntegrativo', 'xPerc_Ribaltamento'], 'number'],
            [['Note_CF', 'Attributi', 'NoteXML', 'HistoryData', 'ExtraInfo'], 'string'],
            [['SDD_DtMandato', 'TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['Cd_CF', 'Cd_LS_1', 'Cd_LS_2', 'Cd_CF_Fatt'], 'string', 'max' => 7],
            [['Descrizione', 'Indirizzo', 'RF_RagioneSociale'], 'string', 'max' => 80],
            [['Localita', 'SO_Indirizzo', 'SO_Localita', 'RF_Nome', 'RF_Cognome'], 'string', 'max' => 60],
            [['Cap', 'Cd_SL', 'Sconto', 'Provvigione', 'Cd_Abicab', 'NRea', 'Cd_ParcT'], 'string', 'max' => 10],
            [['Cd_Provincia', 'Cd_CFClasse1', 'Cd_CFClasse2', 'Cd_CFClasse3', 'Cd_CFGruppo1', 'Cd_CFGruppo2', 'Cd_CFGruppo3', 'Cd_VL', 'Cd_CFStato', 'Cd_CFZona', 'Cd_CFSettore', 'Cd_DOPorto', 'Cd_DOSped', 'Cd_Agente_1', 'Cd_Agente_2', 'Cd_Provincia_Origine', 'Cd_Provincia_Destinazione', 'Cd_Aliquota', 'CD_ReverseCharge', 'SO_Cd_Provincia', 'FTE_Cd_CN'], 'string', 'max' => 3],
            [['Cd_Nazione', 'Cd_DOVettore', 'Cd_INTRATransazione', 'Cd_Nazione_Origine', 'Cd_Nazione_Destinazione', 'Cd_Nazione_Provenienza', 'Cd_AE_NaturaGiuridica', 'SO_Cd_Nazione', 'RF_Cd_Nazione', 'Cd_NazioneIva'], 'string', 'max' => 2],
            [['Cd_NazioneProvincia', 'SO_Cap'], 'string', 'max' => 5],
            [['PartitaIva'], 'string', 'max' => 17],
            [['CodiceFiscale'], 'string', 'max' => 16],
            [['CodiceIPA', 'Cd_AE_Atecofin'], 'string', 'max' => 6],
            [['CodiceFPR'], 'string', 'max' => 100],
            [['TipoDitta', 'TipoCF', 'TipoCF_1', 'Cd_INTRAConsegna', 'Cd_INTRATrasporto'], 'string', 'max' => 1],
            [['Cd_PG', 'Cd_F24Comune', 'FTE_RegimeFiscale'], 'string', 'max' => 4],
            [['Cd_CGConto_Mastro', 'Cd_CGConto_Banca', 'Cd_CGConto_Merce', 'xCd_CGConto_RitenutaAcconto', 'xCd_CGConto_RitenutaEnasarco', 'xCd_CGConto_ContributoIntegrativo'], 'string', 'max' => 12],
            [['Cd_CACda', 'ContoCorrente'], 'string', 'max' => 20],
            [['Iban'], 'string', 'max' => 34],
            [['BicCode'], 'string', 'max' => 11],
            [['Ricarica'], 'string', 'max' => 15],
            [['SDD_IdMandato'], 'string', 'max' => 35],
            [['Peppol_Endpoint'], 'string', 'max' => 22],
            [['SO_NumeroCivico'], 'string', 'max' => 8],
            [['RF_PartitaIva'], 'string', 'max' => 28],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['Cd_CF'], 'unique'],
            [['Cd_CACda'], 'exist', 'skipOnError' => true, 'targetClass' => CACda::className(), 'targetAttribute' => ['Cd_CACda' => 'Cd_CACda']],
            [['Cd_CF_Fatt'], 'exist', 'skipOnError' => true, 'targetClass' => CF::className(), 'targetAttribute' => ['Cd_CF_Fatt' => 'Cd_CF']],
            [['xCd_CGConto_RitenutaAcconto'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['xCd_CGConto_RitenutaAcconto' => 'Cd_CGConto']],
            [['xCd_CGConto_RitenutaEnasarco'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['xCd_CGConto_RitenutaEnasarco' => 'Cd_CGConto']],
            [['xCd_CGConto_ContributoIntegrativo'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['xCd_CGConto_ContributoIntegrativo' => 'Cd_CGConto']],
            [['Cd_ParcT'], 'exist', 'skipOnError' => true, 'targetClass' => ParcT::className(), 'targetAttribute' => ['Cd_ParcT' => 'Cd_ParcT']],
            [['Cd_INTRATransazione'], 'exist', 'skipOnError' => true, 'targetClass' => INTRATransazione::className(), 'targetAttribute' => ['Cd_INTRATransazione' => 'Cd_INTRATransazione']],
            [['Cd_AE_NaturaGiuridica'], 'exist', 'skipOnError' => true, 'targetClass' => AENaturaGiuridica::className(), 'targetAttribute' => ['Cd_AE_NaturaGiuridica' => 'Cd_AE_NaturaGiuridica']],
            [['FTE_Cd_CN'], 'exist', 'skipOnError' => true, 'targetClass' => CN::className(), 'targetAttribute' => ['FTE_Cd_CN' => 'Cd_CN']],
            [['Cd_VL'], 'exist', 'skipOnError' => true, 'targetClass' => VL::className(), 'targetAttribute' => ['Cd_VL' => 'Cd_VL']],
            [['Cd_NazioneIva'], 'exist', 'skipOnError' => true, 'targetClass' => Nazione::className(), 'targetAttribute' => ['Cd_NazioneIva' => 'Cd_Nazione']],
            [['Cd_Nazione'], 'exist', 'skipOnError' => true, 'targetClass' => Nazione::className(), 'targetAttribute' => ['Cd_Nazione' => 'Cd_Nazione']],
            [['RF_Cd_Nazione'], 'exist', 'skipOnError' => true, 'targetClass' => Nazione::className(), 'targetAttribute' => ['RF_Cd_Nazione' => 'Cd_Nazione']],
            [['Cd_Nazione_Provenienza'], 'exist', 'skipOnError' => true, 'targetClass' => Nazione::className(), 'targetAttribute' => ['Cd_Nazione_Provenienza' => 'Cd_Nazione']],
            [['SO_Cd_Nazione'], 'exist', 'skipOnError' => true, 'targetClass' => Nazione::className(), 'targetAttribute' => ['SO_Cd_Nazione' => 'Cd_Nazione']],
            [['Cd_Nazione_Destinazione'], 'exist', 'skipOnError' => true, 'targetClass' => Nazione::className(), 'targetAttribute' => ['Cd_Nazione_Destinazione' => 'Cd_Nazione']],
            [['SO_Cd_Nazione', 'SO_Cd_Provincia'], 'exist', 'skipOnError' => true, 'targetClass' => Provincia::className(), 'targetAttribute' => ['SO_Cd_Nazione' => 'Cd_Nazione', 'SO_Cd_Provincia' => 'Cd_Provincia']],
            [['Cd_Nazione_Origine'], 'exist', 'skipOnError' => true, 'targetClass' => Nazione::className(), 'targetAttribute' => ['Cd_Nazione_Origine' => 'Cd_Nazione']],
            [['CD_ReverseCharge'], 'exist', 'skipOnError' => true, 'targetClass' => ReverseCharge::className(), 'targetAttribute' => ['CD_ReverseCharge' => 'Cd_ReverseCharge']],
            [['Cd_F24Comune'], 'exist', 'skipOnError' => true, 'targetClass' => F24Comune::className(), 'targetAttribute' => ['Cd_F24Comune' => 'Cd_F24Comune']],
            [['Cd_INTRATrasporto'], 'exist', 'skipOnError' => true, 'targetClass' => INTRATrasporto::className(), 'targetAttribute' => ['Cd_INTRATrasporto' => 'Cd_INTRATrasporto']],
            [['Id_Lingua'], 'exist', 'skipOnError' => true, 'targetClass' => Lingua::className(), 'targetAttribute' => ['Id_Lingua' => 'Id_Lingua']],
            [['Cd_CFClasse1'], 'exist', 'skipOnError' => true, 'targetClass' => CFClasse1::className(), 'targetAttribute' => ['Cd_CFClasse1' => 'Cd_CFClasse1']],
            [['Cd_Nazione', 'Cd_Provincia'], 'exist', 'skipOnError' => true, 'targetClass' => Provincia::className(), 'targetAttribute' => ['Cd_Nazione' => 'Cd_Nazione', 'Cd_Provincia' => 'Cd_Provincia']],
            [['Cd_CFClasse1', 'Cd_CFClasse2'], 'exist', 'skipOnError' => true, 'targetClass' => CFClasse2::className(), 'targetAttribute' => ['Cd_CFClasse1' => 'Cd_CFClasse1', 'Cd_CFClasse2' => 'Cd_CFClasse2']],
            [['Cd_CFClasse1', 'Cd_CFClasse2', 'Cd_CFClasse3'], 'exist', 'skipOnError' => true, 'targetClass' => CFClasse3::className(), 'targetAttribute' => ['Cd_CFClasse1' => 'Cd_CFClasse1', 'Cd_CFClasse2' => 'Cd_CFClasse2', 'Cd_CFClasse3' => 'Cd_CFClasse3']],
            [['Cd_Nazione_Origine', 'Cd_Provincia_Origine'], 'exist', 'skipOnError' => true, 'targetClass' => Provincia::className(), 'targetAttribute' => ['Cd_Nazione_Origine' => 'Cd_Nazione', 'Cd_Provincia_Origine' => 'Cd_Provincia']],
            [['Cd_CFGruppo1'], 'exist', 'skipOnError' => true, 'targetClass' => CFGruppo1::className(), 'targetAttribute' => ['Cd_CFGruppo1' => 'Cd_CFGruppo1']],
            [['Cd_Nazione_Destinazione', 'Cd_Provincia_Destinazione'], 'exist', 'skipOnError' => true, 'targetClass' => Provincia::className(), 'targetAttribute' => ['Cd_Nazione_Destinazione' => 'Cd_Nazione', 'Cd_Provincia_Destinazione' => 'Cd_Provincia']],
            [['Cd_CFSettore'], 'exist', 'skipOnError' => true, 'targetClass' => CFSettore::className(), 'targetAttribute' => ['Cd_CFSettore' => 'Cd_CFSettore']],
            [['Cd_INTRAConsegna'], 'exist', 'skipOnError' => true, 'targetClass' => INTRAConsegna::className(), 'targetAttribute' => ['Cd_INTRAConsegna' => 'Cd_INTRAConsegna']],
            [['Cd_CFZona'], 'exist', 'skipOnError' => true, 'targetClass' => CFZona::className(), 'targetAttribute' => ['Cd_CFZona' => 'Cd_CFZona']],
            [['Cd_PG'], 'exist', 'skipOnError' => true, 'targetClass' => PG::className(), 'targetAttribute' => ['Cd_PG' => 'Cd_PG']],
            [['Cd_CFGruppo1', 'Cd_CFGruppo2'], 'exist', 'skipOnError' => true, 'targetClass' => CFGruppo2::className(), 'targetAttribute' => ['Cd_CFGruppo1' => 'Cd_CFGruppo1', 'Cd_CFGruppo2' => 'Cd_CFGruppo2']],
            [['Cd_LS_1'], 'exist', 'skipOnError' => true, 'targetClass' => LS::className(), 'targetAttribute' => ['Cd_LS_1' => 'Cd_LS']],
            [['Cd_LS_2'], 'exist', 'skipOnError' => true, 'targetClass' => LS::className(), 'targetAttribute' => ['Cd_LS_2' => 'Cd_LS']],
            [['Cd_Aliquota'], 'exist', 'skipOnError' => true, 'targetClass' => Aliquota::className(), 'targetAttribute' => ['Cd_Aliquota' => 'Cd_Aliquota']],
            [['Cd_CFGruppo1', 'Cd_CFGruppo2', 'Cd_CFGruppo3'], 'exist', 'skipOnError' => true, 'targetClass' => CFGruppo3::className(), 'targetAttribute' => ['Cd_CFGruppo1' => 'Cd_CFGruppo1', 'Cd_CFGruppo2' => 'Cd_CFGruppo2', 'Cd_CFGruppo3' => 'Cd_CFGruppo3']],
            [['TipoCF_1', 'Cd_CFStato'], 'exist', 'skipOnError' => true, 'targetClass' => CFStato::className(), 'targetAttribute' => ['TipoCF_1' => 'TipoCF', 'Cd_CFStato' => 'Cd_CFStato']],
            [['Cd_SL'], 'exist', 'skipOnError' => true, 'targetClass' => SL::className(), 'targetAttribute' => ['Cd_SL' => 'Cd_SL']],
            [['Cd_CGConto_Banca'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto_Banca' => 'Cd_CGConto']],
            [['Cd_CGConto_Mastro'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto_Mastro' => 'Cd_CGConto']],
            [['Cd_CGConto_Merce'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto_Merce' => 'Cd_CGConto']],
            [['Cd_DOPorto'], 'exist', 'skipOnError' => true, 'targetClass' => DOPorto::className(), 'targetAttribute' => ['Cd_DOPorto' => 'Cd_DOPorto']],
            [['Cd_DOSped'], 'exist', 'skipOnError' => true, 'targetClass' => DOSped::className(), 'targetAttribute' => ['Cd_DOSped' => 'Cd_DOSped']],
            [['Cd_Agente_1'], 'exist', 'skipOnError' => true, 'targetClass' => Agente::className(), 'targetAttribute' => ['Cd_Agente_1' => 'Cd_Agente']],
            [['Cd_Agente_2'], 'exist', 'skipOnError' => true, 'targetClass' => Agente::className(), 'targetAttribute' => ['Cd_Agente_2' => 'Cd_Agente']],
            [['Cd_DOVettore'], 'exist', 'skipOnError' => true, 'targetClass' => DOVettore::className(), 'targetAttribute' => ['Cd_DOVettore' => 'Cd_DoVettore']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_CF' => 'Id Cf',
            'Cd_CF' => 'Cd Cf',
            'Descrizione' => 'Descrizione',
            'Indirizzo' => 'Indirizzo',
            'Localita' => 'Localita',
            'Cap' => 'Cap',
            'Cd_Provincia' => 'Cd Provincia',
            'Cd_Nazione' => 'Cd Nazione',
            'Cd_NazioneProvincia' => 'Cd Nazione Provincia',
            'PartitaIva' => 'Partita Iva',
            'CodiceFiscale' => 'Codice Fiscale',
            'CodiceIPA' => 'Codice Ipa',
            'CodiceFPR' => 'Codice Fpr',
            'TipoDitta' => 'Tipo Ditta',
            'Cliente' => 'Cliente',
            'Fornitore' => 'Fornitore',
            'ProspectCliente' => 'Prospect Cliente',
            'ProspectFornitore' => 'Prospect Fornitore',
            'TipoCF' => 'Tipo Cf',
            'TipoCF_1' => 'Tipo Cf 1',
            'Cd_CFClasse1' => 'Cd Cf Classe1',
            'Cd_CFClasse2' => 'Cd Cf Classe2',
            'Cd_CFClasse3' => 'Cd Cf Classe3',
            'Cd_CFGruppo1' => 'Cd Cf Gruppo1',
            'Cd_CFGruppo2' => 'Cd Cf Gruppo2',
            'Cd_CFGruppo3' => 'Cd Cf Gruppo3',
            'Cd_PG' => 'Cd Pg',
            'Cd_CGConto_Mastro' => 'Cd Cg Conto Mastro',
            'Cd_CGConto_Banca' => 'Cd Cg Conto Banca',
            'Cd_CGConto_Merce' => 'Cd Cg Conto Merce',
            'Cd_LS_1' => 'Cd Ls 1',
            'Cd_LS_2' => 'Cd Ls 2',
            'Cd_VL' => 'Cd Vl',
            'Cd_CFStato' => 'Cd Cf Stato',
            'Cd_CFZona' => 'Cd Cf Zona',
            'Cd_CFSettore' => 'Cd Cf Settore',
            'Cd_DOPorto' => 'Cd Do Porto',
            'Cd_DOSped' => 'Cd Do Sped',
            'Cd_DOVettore' => 'Cd Do Vettore',
            'Cd_SL' => 'Cd Sl',
            'Cd_Agente_1' => 'Cd Agente 1',
            'Cd_Agente_2' => 'Cd Agente 2',
            'Id_Lingua' => 'Id Lingua',
            'Sconto' => 'Sconto',
            'Provvigione' => 'Provvigione',
            'SpeseIncasso' => 'Spese Incasso',
            'SpeseBolli' => 'Spese Bolli',
            'Fido' => 'Fido',
            'Note_CF' => 'Note Cf',
            'ScGiornoFisso1' => 'Sc Giorno Fisso1',
            'ScGiornoFisso2' => 'Sc Giorno Fisso2',
            'ScGiornoFisso3' => 'Sc Giorno Fisso3',
            'Cd_INTRAConsegna' => 'Cd Intra Consegna',
            'Cd_INTRATrasporto' => 'Cd Intra Trasporto',
            'Cd_INTRATransazione' => 'Cd Intra Transazione',
            'Cd_Nazione_Origine' => 'Cd Nazione Origine',
            'Cd_Provincia_Origine' => 'Cd Provincia Origine',
            'Cd_Nazione_Destinazione' => 'Cd Nazione Destinazione',
            'Cd_Provincia_Destinazione' => 'Cd Provincia Destinazione',
            'Cd_Nazione_Provenienza' => 'Cd Nazione Provenienza',
            'PdAbilita' => 'Pd Abilita',
            'PdPriorita' => 'Pd Priorita',
            'PdInt4ClienteFatturazione' => 'Pd Int4cliente Fatturazione',
            'PdBrk4DocumentoPrelevato' => 'Pd Brk4documento Prelevato',
            'PdBrk4ClienteFornitore' => 'Pd Brk4cliente Fornitore',
            'PdBrk4Pagamento' => 'Pd Brk4pagamento',
            'PdBrk4Agente_1' => 'Pd Brk4agente 1',
            'PdBrk4Agente_2' => 'Pd Brk4agente 2',
            'PdBrk4Zona' => 'Pd Brk4zona',
            'PdBrk4SedeAmministrativa' => 'Pd Brk4sede Amministrativa',
            'PdBrk4DestinazioneDiversa' => 'Pd Brk4destinazione Diversa',
            'Ue' => 'Ue',
            'Intra' => 'Intra',
            'Ritenuta' => 'Ritenuta',
            'IvaSospesa' => 'Iva Sospesa',
            'IvaSplit' => 'Iva Split',
            'ProceduraConcorsuale' => 'Procedura Concorsuale',
            'ConsumatoreFinale' => 'Consumatore Finale',
            'EntePubblico' => 'Ente Pubblico',
            'Obsoleto' => 'Obsoleto',
            'Condominio' => 'Condominio',
            'Elenchi' => 'Elenchi',
            'Cd_CACda' => 'Cd Ca Cda',
            'Cd_Aliquota' => 'Cd Aliquota',
            'Cd_CF_Fatt' => 'Cd Cf Fatt',
            'Iban' => 'Iban',
            'BicCode' => 'Bic Code',
            'Cd_Abicab' => 'Cd Abicab',
            'ContoCorrente' => 'Conto Corrente',
            'Tipofattura' => 'Tipofattura',
            'Ricarica' => 'Ricarica',
            'Attributi' => 'Attributi',
            'EvtnInfo' => 'Evtn Info',
            'NoteXML' => 'Note Xml',
            'HistoryData' => 'History Data',
            'EsclusoOver3000' => 'Escluso Over3000',
            'EsclusoSpesometro' => 'Escluso Spesometro',
            'EsclusoBlackList' => 'Escluso Black List',
            'NRea' => 'N Rea',
            'Cd_F24Comune' => 'Cd F24comune',
            'Cd_AE_NaturaGiuridica' => 'Cd Ae Natura Giuridica',
            'Cd_AE_Atecofin' => 'Cd Ae Atecofin',
            'SDD_IdMandato' => 'Sdd Id Mandato',
            'SDD_DtMandato' => 'Sdd Dt Mandato',
            'CD_ReverseCharge' => 'Cd Reverse Charge',
            'Peppol_Endpoint' => 'Peppol Endpoint',
            'SO_Indirizzo' => 'So Indirizzo',
            'SO_NumeroCivico' => 'So Numero Civico',
            'SO_Cap' => 'So Cap',
            'SO_Localita' => 'So Localita',
            'SO_Cd_Provincia' => 'So Cd Provincia',
            'SO_Cd_Nazione' => 'So Cd Nazione',
            'RF_PartitaIva' => 'Rf Partita Iva',
            'RF_RagioneSociale' => 'Rf Ragione Sociale',
            'RF_Nome' => 'Rf Nome',
            'RF_Cognome' => 'Rf Cognome',
            'DF_Escluso' => 'Df Escluso',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'FE_AddASW' => 'Fe Add Asw',
            'xNOControlloNumDOC' => 'X No Controllo Num Doc',
            'xBlocco_NumDoc' => 'X Blocco Num Doc',
            'ExtraInfo' => 'Extra Info',
            'FE_Ignore_CodiceFPR' => 'Fe Ignore Codice Fpr',
            'xCalcolaRitenuta' => 'X Calcola Ritenuta',
            'xCalcolaEnasarco' => 'X Calcola Enasarco',
            'xEnasarcoManuale' => 'X Enasarco Manuale',
            'xMassimaleRitenutaAcconto' => 'X Massimale Ritenuta Acconto',
            'xPercentualeRitenuta' => 'X Percentuale Ritenuta',
            'xPercentualeEnasarco' => 'X Percentuale Enasarco',
            'xPercentualeImponibileRitenuta' => 'X Percentuale Imponibile Ritenuta',
            'xPercentualeImponibileEnasarco' => 'X Percentuale Imponibile Enasarco',
            'xCd_CGConto_RitenutaAcconto' => 'X Cd Cg Conto Ritenuta Acconto',
            'xCd_CGConto_RitenutaEnasarco' => 'X Cd Cg Conto Ritenuta Enasarco',
            'xCd_CGConto_ContributoIntegrativo' => 'X Cd Cg Conto Contributo Integrativo',
            'xPercentualeContributoIntegrativo' => 'X Percentuale Contributo Integrativo',
            'RF_Cd_Nazione' => 'Rf Cd Nazione',
            'Cd_NazioneIva' => 'Cd Nazione Iva',
            'FTE_AutoTipo' => 'Fte Auto Tipo',
            'FTE_RegimeFiscale' => 'Fte Regime Fiscale',
            'FTE_Cd_CN' => 'Fte Cd Cn',
            'xPerc_Ribaltamento' => 'X Perc Ribaltamento',
            'Cd_ParcT' => 'Cd Parc T',
        ];
    }
}
