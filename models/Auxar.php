<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ar".
 *
 * @property int $Id_AR
 * @property string $Cd_AR Codice Articolo
 * @property string $Descrizione Descrizione breve dell'articol
 * @property string $DescrizioneBreve
 * @property string|null $VBDescrizione
 * @property string|null $Note_AR Descrizone lunga.
 * @property string|null $Cd_ARGruppo1 Codice Gruppo di primo livello
 * @property string|null $Cd_ARGruppo2 Codice gruppo di secondo livel
 * @property string|null $Cd_ARGruppo3 Codice Gruppo di terzo livello
 * @property string|null $Cd_ARClasse1 Codice classe di primo livello
 * @property string|null $Cd_ARClasse2 codice classe di secondo livel
 * @property string|null $Cd_ARClasse3 Codice classe di terzo livello
 * @property string|null $Cd_VbReparto
 * @property string|null $Cd_Aliquota_A Codice IVA acquisti
 * @property string|null $Cd_Aliquota_V Codice IVA vendite
 * @property string|null $Cd_CGConto_VI
 * @property string|null $Cd_CGConto_VE
 * @property string|null $Cd_CGConto_AI
 * @property string|null $Cd_CGConto_AE
 * @property string|null $Cd_CAVda_VI
 * @property string|null $Cd_CAVda_VE
 * @property string|null $Cd_CAVda_AI
 * @property string|null $Cd_CAVda_AE
 * @property string|null $Cd_ARStato Codice stato dell'Articolo.
 * @property string|null $Cd_ARMarca
 * @property string|null $Cd_ARNomenclatura Codice Nomenclatura combinata
 * @property string|null $Cd_ARPrdClasse Classe di pianificazione
 * @property int|null $Id_ARCategoria
 * @property string|null $Cd_IntraServizio
 * @property string $IntraTipo
 * @property string $Modello
 * @property string $Sconto Espressione per la % di sconto
 * @property string $Provvigione Espressione per la % di provvi
 * @property string $Ricarica
 * @property float $ScortaMinima Scorta minima
 * @property float $ScortaMassima
 * @property float $LottoMinimo Lotto minimo di riordino
 * @property float $LottoRiordino Multipli del lotto di riordino
 * @property string|null $Cd_ARMisura
 * @property float $PesoLordo Peso Lordo
 * @property float $PesoNetto Peso Netto
 * @property float $PesoFattore Unità di misura in cui sono es
 * @property float $PesoLordoMks Peso Lordo in kilogrammi
 * @property float $PesoNettoMks Peso Netto in Kg
 * @property float $Altezza Altezza
 * @property float $Lunghezza Lunghezza
 * @property float $Larghezza Larghezza
 * @property float $DimensioniFattore Fattore in cui sono espresse A
 * @property float $AltezzaMks Altezza in metri
 * @property float $LunghezzaMks Lunghezza in metri
 * @property float $LarghezzaMks Larghezza in metri
 * @property float $VolumeMks Volume in metri cubi
 * @property float $CostoStandard Costo Standard
 * @property string $ClasseAbc Classe ABC 
 * @property int $TipoValorizzazione Tipo di valorizzazione da util
 * @property int $Fittizio
 * @property int $Obsoleto
 * @property int $DBKit
 * @property int $NoInventario .T. se l'articolo non deve app
 * @property int $NoGiornale .T. se l'articolo non deve ess
 * @property int $DBFantasma Articolo Fantasma se utilizzat
 * @property int $MG_LottoObbligatorio
 * @property int $MG_MatricolaObbligatoria
 * @property int $MG_GiacenzaNonNegativa
 * @property int $TipoGestComm
 * @property int $MrpGiorniRiordino Giorni di riordino.
 * @property float $MrpProduzioneMassima Produzione massima giornaliera
 * @property int $MrpIncludi Includi nell'elaborazione Mrp
 * @property int $MrpGiorniCopertura Giorni di copertura proposta o
 * @property int $MrpResa % di resa (incrementa/decremen
 * @property int $MrpLottoRiordino True se l'Mrp nelle proposte d
 * @property int $MrpLottoMinimo True se l'Mrp nelle proposte d
 * @property int $MrpPuntoRiordino 1 - Riordino quando Disponibil
 * @property int $MrpIgnoraDistinta True se l'Mrp deve trattare l'
 * @property int $WebB2CPubblica True se l'articolo deve essere
 * @property int $WebB2BPubblica
 * @property string $WebDescrizione Descrizione per il  Web Catalo
 * @property string|null $WebNote_AR
 * @property string $WebInfoLink
 * @property float $WebGiacenza
 * @property int $AmsManaged
 * @property string|null $NoteOfferta
 * @property string|null $Attributi
 * @property string|null $NoteXML
 * @property string $UserIns Utente che ha eseguito l'Inser
 * @property string $UserUpd Utente che ha eseguito l'ultim
 * @property string $TimeIns Data inserimento del record.
 
 * @property string $TimeUpd Data ultimo aggiornamento del 
 * @property string|null $Ts
 * @property string|null $FTE_CodiceTipo
 * @property string|null $FTE_CodiceValore
 * @property string|null $Cd_ARClasse12
 * @property string|null $Cd_ARClasse123
 * @property string|null $Cd_ARGruppo12
 * @property string|null $Cd_ARGruppo123
 * @property string|null $Cd_Nazione_Origine
 * @property string|null $ExtraInfo
 * @property int|null $x_isspesa
 * @property string|null $Cd_Provincia_Origine
 * @property int $MrpAggiornaCostoDistinta Aggiorna il costo di Distinta 
 * @property int $MrpForzaExpDistinta Se MrpIgnoraDistinta è attivo 
 */
class Auxar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'AR';
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
            [['Cd_AR', 'PesoLordoMks', 'PesoNettoMks', 'AltezzaMks', 'LunghezzaMks', 'LarghezzaMks', 'VolumeMks'], 'required'],
            [['Note_AR', 'WebNote_AR', 'NoteOfferta', 'Attributi', 'NoteXML', 'ExtraInfo'], 'string'],
            [['Id_ARCategoria', 'TipoValorizzazione', 'Fittizio', 'Obsoleto', 'DBKit', 'NoInventario', 'NoGiornale', 'DBFantasma', 'MG_LottoObbligatorio', 'MG_MatricolaObbligatoria', 'MG_GiacenzaNonNegativa', 'TipoGestComm', 'MrpGiorniRiordino', 'MrpIncludi', 'MrpGiorniCopertura', 'MrpResa', 'MrpLottoRiordino', 'MrpLottoMinimo', 'MrpPuntoRiordino', 'MrpIgnoraDistinta', 'WebB2CPubblica', 'WebB2BPubblica', 'AmsManaged', 'x_isspesa', 'MrpAggiornaCostoDistinta', 'MrpForzaExpDistinta'], 'integer'],
            [['ScortaMinima', 'ScortaMassima', 'LottoMinimo', 'LottoRiordino', 'PesoLordo', 'PesoNetto', 'PesoFattore', 'PesoLordoMks', 'PesoNettoMks', 'Altezza', 'Lunghezza', 'Larghezza', 'DimensioniFattore', 'AltezzaMks', 'LunghezzaMks', 'LarghezzaMks', 'VolumeMks', 'CostoStandard', 'MrpProduzioneMassima', 'WebGiacenza'], 'number'],
            [['TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['Cd_AR', 'Cd_ARMarca', 'Modello'], 'string', 'max' => 20],
            [['Descrizione', 'WebDescrizione'], 'string', 'max' => 80],
            [['DescrizioneBreve'], 'string', 'max' => 40],
            [['VBDescrizione'], 'string', 'max' => 30],
            [['Cd_ARGruppo1', 'Cd_ARGruppo2', 'Cd_ARGruppo3', 'Cd_ARClasse1', 'Cd_ARClasse2', 'Cd_ARClasse3', 'Cd_Aliquota_A', 'Cd_Aliquota_V', 'Cd_ARStato', 'Cd_Provincia_Origine'], 'string', 'max' => 3],
            [['Cd_VbReparto', 'Cd_ARMisura', 'Cd_Nazione_Origine'], 'string', 'max' => 2],
            [['Cd_CGConto_VI', 'Cd_CGConto_VE', 'Cd_CGConto_AI', 'Cd_CGConto_AE', 'Cd_CAVda_VI', 'Cd_CAVda_VE', 'Cd_CAVda_AI', 'Cd_CAVda_AE'], 'string', 'max' => 12],
            [['Cd_ARNomenclatura'], 'string', 'max' => 8],
            [['Cd_ARPrdClasse'], 'string', 'max' => 5],
            [['Cd_IntraServizio', 'Cd_ARClasse12', 'Cd_ARGruppo12'], 'string', 'max' => 6],
            [['IntraTipo', 'ClasseAbc'], 'string', 'max' => 1],
            [['Sconto', 'Provvigione'], 'string', 'max' => 10],
            [['Ricarica'], 'string', 'max' => 15],
            [['WebInfoLink'], 'string', 'max' => 128],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['FTE_CodiceTipo', 'FTE_CodiceValore'], 'string', 'max' => 35],
            [['Cd_ARClasse123', 'Cd_ARGruppo123'], 'string', 'max' => 9],
            [['Cd_AR'], 'unique'],
            [['Cd_ARMisura'], 'exist', 'skipOnError' => true, 'targetClass' => ARMisura::className(), 'targetAttribute' => ['Cd_ARMisura' => 'Cd_ARMisura']],
            [['Cd_ARStato'], 'exist', 'skipOnError' => true, 'targetClass' => ARStato::className(), 'targetAttribute' => ['Cd_ARStato' => 'Cd_ARStato']],
            [['Cd_ARGruppo1', 'Cd_ARGruppo2'], 'exist', 'skipOnError' => true, 'targetClass' => ARGruppo2::className(), 'targetAttribute' => ['Cd_ARGruppo1' => 'Cd_ARGruppo1', 'Cd_ARGruppo2' => 'Cd_ARGruppo2']],
            [['Cd_ARGruppo1'], 'exist', 'skipOnError' => true, 'targetClass' => ARGruppo1::className(), 'targetAttribute' => ['Cd_ARGruppo1' => 'Cd_ARGruppo1']],
            [['Cd_ARClasse1', 'Cd_ARClasse2', 'Cd_ARClasse3'], 'exist', 'skipOnError' => true, 'targetClass' => ARClasse3::className(), 'targetAttribute' => ['Cd_ARClasse1' => 'Cd_ARClasse1', 'Cd_ARClasse2' => 'Cd_ARClasse2', 'Cd_ARClasse3' => 'Cd_ARClasse3']],
            [['Cd_ARClasse1', 'Cd_ARClasse2'], 'exist', 'skipOnError' => true, 'targetClass' => ARClasse2::className(), 'targetAttribute' => ['Cd_ARClasse1' => 'Cd_ARClasse1', 'Cd_ARClasse2' => 'Cd_ARClasse2']],
            [['Cd_CAVda_AE'], 'exist', 'skipOnError' => true, 'targetClass' => CAVda::className(), 'targetAttribute' => ['Cd_CAVda_AE' => 'Cd_CAVda']],
            [['Cd_ARClasse1'], 'exist', 'skipOnError' => true, 'targetClass' => ARClasse1::className(), 'targetAttribute' => ['Cd_ARClasse1' => 'Cd_ARClasse1']],
            [['Cd_CAVda_VI'], 'exist', 'skipOnError' => true, 'targetClass' => CAVda::className(), 'targetAttribute' => ['Cd_CAVda_VI' => 'Cd_CAVda']],
            [['Id_ARCategoria'], 'exist', 'skipOnError' => true, 'targetClass' => ARCategoria::className(), 'targetAttribute' => ['Id_ARCategoria' => 'Id_ARCategoria']],
            [['Cd_CGConto_VE'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto_VE' => 'Cd_CGConto']],
            [['Cd_ARPrdClasse'], 'exist', 'skipOnError' => true, 'targetClass' => ARPrdClasse::className(), 'targetAttribute' => ['Cd_ARPrdClasse' => 'Cd_ARPrdClasse']],
            [['Cd_CAVda_AI'], 'exist', 'skipOnError' => true, 'targetClass' => CAVda::className(), 'targetAttribute' => ['Cd_CAVda_AI' => 'Cd_CAVda']],
            [['Cd_ARGruppo1', 'Cd_ARGruppo2', 'Cd_ARGruppo3'], 'exist', 'skipOnError' => true, 'targetClass' => ARGruppo3::className(), 'targetAttribute' => ['Cd_ARGruppo1' => 'Cd_ARGruppo1', 'Cd_ARGruppo2' => 'Cd_ARGruppo2', 'Cd_ARGruppo3' => 'Cd_ARGruppo3']],
            [['Cd_CGConto_VI'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto_VI' => 'Cd_CGConto']],
            [['Cd_VbReparto'], 'exist', 'skipOnError' => true, 'targetClass' => VBReparto::className(), 'targetAttribute' => ['Cd_VbReparto' => 'Cd_VbReparto']],
            [['Cd_CAVda_VE'], 'exist', 'skipOnError' => true, 'targetClass' => CAVda::className(), 'targetAttribute' => ['Cd_CAVda_VE' => 'Cd_CAVda']],
            [['Cd_ARMarca'], 'exist', 'skipOnError' => true, 'targetClass' => ARMarca::className(), 'targetAttribute' => ['Cd_ARMarca' => 'Cd_ARMarca']],
            [['Cd_CGConto_AE'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto_AE' => 'Cd_CGConto']],
            [['Cd_CGConto_AI'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto_AI' => 'Cd_CGConto']],
            [['Cd_Aliquota_A'], 'exist', 'skipOnError' => true, 'targetClass' => Aliquota::className(), 'targetAttribute' => ['Cd_Aliquota_A' => 'Cd_Aliquota']],
            [['Cd_Aliquota_V'], 'exist', 'skipOnError' => true, 'targetClass' => Aliquota::className(), 'targetAttribute' => ['Cd_Aliquota_V' => 'Cd_Aliquota']],
            [['Cd_Nazione_Origine'], 'exist', 'skipOnError' => true, 'targetClass' => Nazione::className(), 'targetAttribute' => ['Cd_Nazione_Origine' => 'Cd_Nazione']],
            [['Cd_Nazione_Origine', 'Cd_Provincia_Origine'], 'exist', 'skipOnError' => true, 'targetClass' => Provincia::className(), 'targetAttribute' => ['Cd_Nazione_Origine' => 'Cd_Nazione', 'Cd_Provincia_Origine' => 'Cd_Provincia']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_AR' => 'Id Ar',
            'Cd_AR' => 'Cd Ar',
            'Descrizione' => 'Descrizione',
            'DescrizioneBreve' => 'Descrizione Breve',
            'VBDescrizione' => 'Vb Descrizione',
            'Note_AR' => 'Note Ar',
            'Cd_ARGruppo1' => 'Cd Ar Gruppo1',
            'Cd_ARGruppo2' => 'Cd Ar Gruppo2',
            'Cd_ARGruppo3' => 'Cd Ar Gruppo3',
            'Cd_ARClasse1' => 'Cd Ar Classe1',
            'Cd_ARClasse2' => 'Cd Ar Classe2',
            'Cd_ARClasse3' => 'Cd Ar Classe3',
            'Cd_VbReparto' => 'Cd Vb Reparto',
            'Cd_Aliquota_A' => 'Cd Aliquota A',
            'Cd_Aliquota_V' => 'Cd Aliquota V',
            'Cd_CGConto_VI' => 'Cd Cg Conto Vi',
            'Cd_CGConto_VE' => 'Cd Cg Conto Ve',
            'Cd_CGConto_AI' => 'Cd Cg Conto Ai',
            'Cd_CGConto_AE' => 'Cd Cg Conto Ae',
            'Cd_CAVda_VI' => 'Cd Ca Vda Vi',
            'Cd_CAVda_VE' => 'Cd Ca Vda Ve',
            'Cd_CAVda_AI' => 'Cd Ca Vda Ai',
            'Cd_CAVda_AE' => 'Cd Ca Vda Ae',
            'Cd_ARStato' => 'Cd Ar Stato',
            'Cd_ARMarca' => 'Cd Ar Marca',
            'Cd_ARNomenclatura' => 'Cd Ar Nomenclatura',
            'Cd_ARPrdClasse' => 'Cd Ar Prd Classe',
            'Id_ARCategoria' => 'Id Ar Categoria',
            'Cd_IntraServizio' => 'Cd Intra Servizio',
            'IntraTipo' => 'Intra Tipo',
            'Modello' => 'Modello',
            'Sconto' => 'Sconto',
            'Provvigione' => 'Provvigione',
            'Ricarica' => 'Ricarica',
            'ScortaMinima' => 'Scorta Minima',
            'ScortaMassima' => 'Scorta Massima',
            'LottoMinimo' => 'Lotto Minimo',
            'LottoRiordino' => 'Lotto Riordino',
            'Cd_ARMisura' => 'Cd Ar Misura',
            'PesoLordo' => 'Peso Lordo',
            'PesoNetto' => 'Peso Netto',
            'PesoFattore' => 'Peso Fattore',
            'PesoLordoMks' => 'Peso Lordo Mks',
            'PesoNettoMks' => 'Peso Netto Mks',
            'Altezza' => 'Altezza',
            'Lunghezza' => 'Lunghezza',
            'Larghezza' => 'Larghezza',
            'DimensioniFattore' => 'Dimensioni Fattore',
            'AltezzaMks' => 'Altezza Mks',
            'LunghezzaMks' => 'Lunghezza Mks',
            'LarghezzaMks' => 'Larghezza Mks',
            'VolumeMks' => 'Volume Mks',
            'CostoStandard' => 'Costo Standard',
            'ClasseAbc' => 'Classe Abc',
            'TipoValorizzazione' => 'Tipo Valorizzazione',
            'Fittizio' => 'Fittizio',
            'Obsoleto' => 'Obsoleto',
            'DBKit' => 'Db Kit',
            'NoInventario' => 'No Inventario',
            'NoGiornale' => 'No Giornale',
            'DBFantasma' => 'Db Fantasma',
            'MG_LottoObbligatorio' => 'Mg Lotto Obbligatorio',
            'MG_MatricolaObbligatoria' => 'Mg Matricola Obbligatoria',
            'MG_GiacenzaNonNegativa' => 'Mg Giacenza Non Negativa',
            'TipoGestComm' => 'Tipo Gest Comm',
            'MrpGiorniRiordino' => 'Mrp Giorni Riordino',
            'MrpProduzioneMassima' => 'Mrp Produzione Massima',
            'MrpIncludi' => 'Mrp Includi',
            'MrpGiorniCopertura' => 'Mrp Giorni Copertura',
            'MrpResa' => 'Mrp Resa',
            'MrpLottoRiordino' => 'Mrp Lotto Riordino',
            'MrpLottoMinimo' => 'Mrp Lotto Minimo',
            'MrpPuntoRiordino' => 'Mrp Punto Riordino',
            'MrpIgnoraDistinta' => 'Mrp Ignora Distinta',
            'WebB2CPubblica' => 'Web B2c Pubblica',
            'WebB2BPubblica' => 'Web B2b Pubblica',
            'WebDescrizione' => 'Web Descrizione',
            'WebNote_AR' => 'Web Note Ar',
            'WebInfoLink' => 'Web Info Link',
            'WebGiacenza' => 'Web Giacenza',
            'AmsManaged' => 'Ams Managed',
            'NoteOfferta' => 'Note Offerta',
            'Attributi' => 'Attributi',
            'NoteXML' => 'Note Xml',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'FTE_CodiceTipo' => 'Fte Codice Tipo',
            'FTE_CodiceValore' => 'Fte Codice Valore',
            'Cd_ARClasse12' => 'Cd Ar Classe12',
            'Cd_ARClasse123' => 'Cd Ar Classe123',
            'Cd_ARGruppo12' => 'Cd Ar Gruppo12',
            'Cd_ARGruppo123' => 'Cd Ar Gruppo123',
            'Cd_Nazione_Origine' => 'Cd Nazione Origine',
            'ExtraInfo' => 'Extra Info',
            'x_isspesa' => 'X Isspesa',
            'Cd_Provincia_Origine' => 'Cd Provincia Origine',
            'MrpAggiornaCostoDistinta' => 'Mrp Aggiorna Costo Distinta',
            'MrpForzaExpDistinta' => 'Mrp Forza Exp Distinta',
        ];
    }
}
