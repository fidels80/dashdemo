<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "AR".
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
 * @property float|null $xPerc Perc
 * @property string|null $ExtraInfo
 * @property int $xSoggettoRitAcconto Soggetto al calcolo della Rite
 * @property int $xSoggettoRitEnasarco Soggetto al calcolo della Rite
 * @property int|null $xEA_Lunedi Lunedì
 * @property int|null $xEA_Martedi Martedì
 * @property int|null $xEA_Mercoledi Mercoledì
 * @property int|null $xEA_Giovedi Giovedì
 * @property int|null $xEA_Venerdi Venerdì
 * @property int|null $xEA_Sabato Sabato
 * @property int|null $xEA_Domenica Domenica
 * @property string|null $Cd_Provincia_Origine
 *
 * @property Aliquota $cdAliquotaA
 * @property Aliquota $cdAliquotaV
 * @property ARCategoria $aRCategoria
 * @property ARClasse3 $cdARClasse1
 * @property ARMisura $cdARMisura
 * @property ARGruppo3 $cdARGruppo1
 * @property ARMarca $cdARMarca
 * @property ARPrdClasse $cdARPrdClasse
 * @property ARStato $cdARStato
 * @property CAVda $cdCAVdaAE
 * @property CAVda $cdCAVdaAI
 * @property CAVda $cdCAVdaVE
 * @property CAVda $cdCAVdaVI
 * @property Provincia $cdNazioneOrigine
 * @property CGConto $cdCGContoAE
 * @property CGConto $cdCGContoAI
 * @property CGConto $cdCGContoVE
 * @property ARGruppo1 $cdARGruppo10
 * @property CGConto $cdCGContoVI
 * @property Nazione $cdNazioneOrigine0
 * @property VBReparto $cdVbReparto
 * @property ARClasse1 $cdARClasse10
 * @property ARGruppo2 $cdARGruppo11
 * @property ARClasse2 $cdARClasse11
 * @property ARAlias[] $aRAliases
 * @property ARARMisura[] $aRARMisuras
 * @property ARMisura[] $cdARMisuras
 * @property ARCodCF[] $aRCodCFs
 * @property CF[] $cdCFs
 * @property ARCodFE[] $aRCodves
 * @property ARCostoDBItem[] $aRCostoDBItems
 * @property ARCostoDB[] $cdMGEsercizios
 * @property ARCostoItem[] $aRCostoItems
 * @property ARCosto[] $cdMGEsercizios0
 * @property ARImg[] $aRImgs
 * @property ARImgWeb[] $aRImgWebs
 * @property ARLingua[] $aRLinguas
 * @property Lingua[] $linguas
 * @property ARLotto[] $aRLottos
 * @property ARMatricola[] $aRMatricolas
 * @property ARMGUbicazione[] $aRMGUbicaziones
 * @property MGUbicazione[] $cdMGs
 * @property DB $dB0
 * @property DBFase[] $dBFases
 * @property DBMateriale[] $dBMateriales
 * @property DBVariante[] $dBVariantes
 * @property DODB[] $dODBs
 * @property DODBAttivita[] $dODBAttivitas
 * @property DODBMateriale[] $dODBMateriales
 * @property DORig[] $dORigs
 * @property EShopOrderItem[] $eShopOrderItems
 * @property LSArticolo[] $lSArticolos
 * @property LSRevisione[] $lSRevisiones
 * @property LSScARCFGruppo[] $lSScARCFGruppos
 * @property MGMov[] $mGMovs
 * @property MGOrdImp[] $mGOrdImps
 * @property MPSnap[] $mPSnaps
 * @property PRBLAttivita[] $pRBLAttivitas
 * @property PRBLMateriale[] $pRBLMateriales
 * @property PROL[] $pROLs
 * @property PROLAttivita[] $pROLAttivitas
 * @property PROLMateriale[] $pROLMateriales
 * @property PRTRMateriale[] $pRTRMateriales
 * @property PRVRAttivita[] $pRVRAttivitas
 * @property PRVRMateriale[] $pRVRMateriales
 * @property VBSconR[] $vBSconRs
 * @property XARFormato[] $xARFormatos
 * @property XGestioneEmailAuto[] $xGestioneEmailAutos
 */
class AR extends \yii\db\ActiveRecord
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
        //return Yii::$app->get('db2');
    return Yii::$app->get('db4');
 
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Cd_AR', 'PesoLordoMks', 'PesoNettoMks', 'AltezzaMks', 'LunghezzaMks', 'LarghezzaMks', 'VolumeMks'], 'required'],
            [['Note_AR', 'WebNote_AR', 'NoteOfferta', 'Attributi', 'NoteXML', 'ExtraInfo'], 'string'],
            [['Id_ARCategoria', 'TipoValorizzazione', 'Fittizio', 'Obsoleto', 'DBKit', 'NoInventario', 'NoGiornale', 'DBFantasma', 'MG_LottoObbligatorio', 'MG_MatricolaObbligatoria', 'MG_GiacenzaNonNegativa', 'TipoGestComm', 'MrpGiorniRiordino', 'MrpIncludi', 'MrpGiorniCopertura', 'MrpResa', 'MrpLottoRiordino', 'MrpLottoMinimo', 'MrpPuntoRiordino', 'MrpIgnoraDistinta', 'WebB2CPubblica', 'WebB2BPubblica', 'AmsManaged', 'xSoggettoRitAcconto', 'xSoggettoRitEnasarco', 'xEA_Lunedi', 'xEA_Martedi', 'xEA_Mercoledi', 'xEA_Giovedi', 'xEA_Venerdi', 'xEA_Sabato', 'xEA_Domenica'], 'integer'],
            [['ScortaMinima', 'ScortaMassima', 'LottoMinimo', 'LottoRiordino', 'PesoLordo', 'PesoNetto', 'PesoFattore', 'PesoLordoMks', 'PesoNettoMks', 'Altezza', 'Lunghezza', 'Larghezza', 'DimensioniFattore', 'AltezzaMks', 'LunghezzaMks', 'LarghezzaMks', 'VolumeMks', 'CostoStandard', 'MrpProduzioneMassima', 'WebGiacenza', 'xPerc'], 'number'],
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
            [['Cd_Aliquota_A'], 'exist', 'skipOnError' => true, 'targetClass' => Aliquota::className(), 'targetAttribute' => ['Cd_Aliquota_A' => 'Cd_Aliquota']],
            [['Cd_Aliquota_V'], 'exist', 'skipOnError' => true, 'targetClass' => Aliquota::className(), 'targetAttribute' => ['Cd_Aliquota_V' => 'Cd_Aliquota']],
            [['Id_ARCategoria'], 'exist', 'skipOnError' => true, 'targetClass' => ARCategoria::className(), 'targetAttribute' => ['Id_ARCategoria' => 'Id_ARCategoria']],
            [['Cd_ARClasse1', 'Cd_ARClasse2', 'Cd_ARClasse3'], 'exist', 'skipOnError' => true, 'targetClass' => ARClasse3::className(), 'targetAttribute' => ['Cd_ARClasse1' => 'Cd_ARClasse1', 'Cd_ARClasse2' => 'Cd_ARClasse2', 'Cd_ARClasse3' => 'Cd_ARClasse3']],
            [['Cd_ARMisura'], 'exist', 'skipOnError' => true, 'targetClass' => ARMisura::className(), 'targetAttribute' => ['Cd_ARMisura' => 'Cd_ARMisura']],
            [['Cd_ARGruppo1', 'Cd_ARGruppo2', 'Cd_ARGruppo3'], 'exist', 'skipOnError' => true, 'targetClass' => ARGruppo3::className(), 'targetAttribute' => ['Cd_ARGruppo1' => 'Cd_ARGruppo1', 'Cd_ARGruppo2' => 'Cd_ARGruppo2', 'Cd_ARGruppo3' => 'Cd_ARGruppo3']],
            [['Cd_ARMarca'], 'exist', 'skipOnError' => true, 'targetClass' => ARMarca::className(), 'targetAttribute' => ['Cd_ARMarca' => 'Cd_ARMarca']],
            [['Cd_ARPrdClasse'], 'exist', 'skipOnError' => true, 'targetClass' => ARPrdClasse::className(), 'targetAttribute' => ['Cd_ARPrdClasse' => 'Cd_ARPrdClasse']],
            [['Cd_ARStato'], 'exist', 'skipOnError' => true, 'targetClass' => ARStato::className(), 'targetAttribute' => ['Cd_ARStato' => 'Cd_ARStato']],
            [['Cd_CAVda_AE'], 'exist', 'skipOnError' => true, 'targetClass' => CAVda::className(), 'targetAttribute' => ['Cd_CAVda_AE' => 'Cd_CAVda']],
            [['Cd_CAVda_AI'], 'exist', 'skipOnError' => true, 'targetClass' => CAVda::className(), 'targetAttribute' => ['Cd_CAVda_AI' => 'Cd_CAVda']],
            [['Cd_CAVda_VE'], 'exist', 'skipOnError' => true, 'targetClass' => CAVda::className(), 'targetAttribute' => ['Cd_CAVda_VE' => 'Cd_CAVda']],
            [['Cd_CAVda_VI'], 'exist', 'skipOnError' => true, 'targetClass' => CAVda::className(), 'targetAttribute' => ['Cd_CAVda_VI' => 'Cd_CAVda']],
            [['Cd_Nazione_Origine', 'Cd_Provincia_Origine'], 'exist', 'skipOnError' => true, 'targetClass' => Provincia::className(), 'targetAttribute' => ['Cd_Nazione_Origine' => 'Cd_Nazione', 'Cd_Provincia_Origine' => 'Cd_Provincia']],
            [['Cd_CGConto_AE'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto_AE' => 'Cd_CGConto']],
            [['Cd_CGConto_AI'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto_AI' => 'Cd_CGConto']],
            [['Cd_CGConto_VE'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto_VE' => 'Cd_CGConto']],
            [['Cd_ARGruppo1'], 'exist', 'skipOnError' => true, 'targetClass' => ARGruppo1::className(), 'targetAttribute' => ['Cd_ARGruppo1' => 'Cd_ARGruppo1']],
            [['Cd_CGConto_VI'], 'exist', 'skipOnError' => true, 'targetClass' => CGConto::className(), 'targetAttribute' => ['Cd_CGConto_VI' => 'Cd_CGConto']],
            [['Cd_Nazione_Origine'], 'exist', 'skipOnError' => true, 'targetClass' => Nazione::className(), 'targetAttribute' => ['Cd_Nazione_Origine' => 'Cd_Nazione']],
            [['Cd_VbReparto'], 'exist', 'skipOnError' => true, 'targetClass' => VBReparto::className(), 'targetAttribute' => ['Cd_VbReparto' => 'Cd_VbReparto']],
            [['Cd_ARClasse1'], 'exist', 'skipOnError' => true, 'targetClass' => ARClasse1::className(), 'targetAttribute' => ['Cd_ARClasse1' => 'Cd_ARClasse1']],
            [['Cd_ARGruppo1', 'Cd_ARGruppo2'], 'exist', 'skipOnError' => true, 'targetClass' => ARGruppo2::className(), 'targetAttribute' => ['Cd_ARGruppo1' => 'Cd_ARGruppo1', 'Cd_ARGruppo2' => 'Cd_ARGruppo2']],
            [['Cd_ARClasse1', 'Cd_ARClasse2'], 'exist', 'skipOnError' => true, 'targetClass' => ARClasse2::className(), 'targetAttribute' => ['Cd_ARClasse1' => 'Cd_ARClasse1', 'Cd_ARClasse2' => 'Cd_ARClasse2']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_AR' => Yii::t('app', 'Id Ar'),
            'Cd_AR' => Yii::t('app', 'Cd Ar'),
            'Descrizione' => Yii::t('app', 'Descrizione'),
            'DescrizioneBreve' => Yii::t('app', 'Descrizione Breve'),
            'VBDescrizione' => Yii::t('app', 'Vb Descrizione'),
            'Note_AR' => Yii::t('app', 'Note Ar'),
            'Cd_ARGruppo1' => Yii::t('app', 'Cd Ar Gruppo1'),
            'Cd_ARGruppo2' => Yii::t('app', 'Cd Ar Gruppo2'),
            'Cd_ARGruppo3' => Yii::t('app', 'Cd Ar Gruppo3'),
            'Cd_ARClasse1' => Yii::t('app', 'Cd Ar Classe1'),
            'Cd_ARClasse2' => Yii::t('app', 'Cd Ar Classe2'),
            'Cd_ARClasse3' => Yii::t('app', 'Cd Ar Classe3'),
            'Cd_VbReparto' => Yii::t('app', 'Cd Vb Reparto'),
            'Cd_Aliquota_A' => Yii::t('app', 'Cd Aliquota A'),
            'Cd_Aliquota_V' => Yii::t('app', 'Cd Aliquota V'),
            'Cd_CGConto_VI' => Yii::t('app', 'Cd Cg Conto Vi'),
            'Cd_CGConto_VE' => Yii::t('app', 'Cd Cg Conto Ve'),
            'Cd_CGConto_AI' => Yii::t('app', 'Cd Cg Conto Ai'),
            'Cd_CGConto_AE' => Yii::t('app', 'Cd Cg Conto Ae'),
            'Cd_CAVda_VI' => Yii::t('app', 'Cd Ca Vda Vi'),
            'Cd_CAVda_VE' => Yii::t('app', 'Cd Ca Vda Ve'),
            'Cd_CAVda_AI' => Yii::t('app', 'Cd Ca Vda Ai'),
            'Cd_CAVda_AE' => Yii::t('app', 'Cd Ca Vda Ae'),
            'Cd_ARStato' => Yii::t('app', 'Cd Ar Stato'),
            'Cd_ARMarca' => Yii::t('app', 'Cd Ar Marca'),
            'Cd_ARNomenclatura' => Yii::t('app', 'Cd Ar Nomenclatura'),
            'Cd_ARPrdClasse' => Yii::t('app', 'Cd Ar Prd Classe'),
            'Id_ARCategoria' => Yii::t('app', 'Id Ar Categoria'),
            'Cd_IntraServizio' => Yii::t('app', 'Cd Intra Servizio'),
            'IntraTipo' => Yii::t('app', 'Intra Tipo'),
            'Modello' => Yii::t('app', 'Modello'),
            'Sconto' => Yii::t('app', 'Sconto'),
            'Provvigione' => Yii::t('app', 'Provvigione'),
            'Ricarica' => Yii::t('app', 'Ricarica'),
            'ScortaMinima' => Yii::t('app', 'Scorta Minima'),
            'ScortaMassima' => Yii::t('app', 'Scorta Massima'),
            'LottoMinimo' => Yii::t('app', 'Lotto Minimo'),
            'LottoRiordino' => Yii::t('app', 'Lotto Riordino'),
            'Cd_ARMisura' => Yii::t('app', 'Cd Ar Misura'),
            'PesoLordo' => Yii::t('app', 'Peso Lordo'),
            'PesoNetto' => Yii::t('app', 'Peso Netto'),
            'PesoFattore' => Yii::t('app', 'Peso Fattore'),
            'PesoLordoMks' => Yii::t('app', 'Peso Lordo Mks'),
            'PesoNettoMks' => Yii::t('app', 'Peso Netto Mks'),
            'Altezza' => Yii::t('app', 'Altezza'),
            'Lunghezza' => Yii::t('app', 'Lunghezza'),
            'Larghezza' => Yii::t('app', 'Larghezza'),
            'DimensioniFattore' => Yii::t('app', 'Dimensioni Fattore'),
            'AltezzaMks' => Yii::t('app', 'Altezza Mks'),
            'LunghezzaMks' => Yii::t('app', 'Lunghezza Mks'),
            'LarghezzaMks' => Yii::t('app', 'Larghezza Mks'),
            'VolumeMks' => Yii::t('app', 'Volume Mks'),
            'CostoStandard' => Yii::t('app', 'Costo Standard'),
            'ClasseAbc' => Yii::t('app', 'Classe Abc'),
            'TipoValorizzazione' => Yii::t('app', 'Tipo Valorizzazione'),
            'Fittizio' => Yii::t('app', 'Fittizio'),
            'Obsoleto' => Yii::t('app', 'Obsoleto'),
            'DBKit' => Yii::t('app', 'Db Kit'),
            'NoInventario' => Yii::t('app', 'No Inventario'),
            'NoGiornale' => Yii::t('app', 'No Giornale'),
            'DBFantasma' => Yii::t('app', 'Db Fantasma'),
            'MG_LottoObbligatorio' => Yii::t('app', 'Mg Lotto Obbligatorio'),
            'MG_MatricolaObbligatoria' => Yii::t('app', 'Mg Matricola Obbligatoria'),
            'MG_GiacenzaNonNegativa' => Yii::t('app', 'Mg Giacenza Non Negativa'),
            'TipoGestComm' => Yii::t('app', 'Tipo Gest Comm'),
            'MrpGiorniRiordino' => Yii::t('app', 'Mrp Giorni Riordino'),
            'MrpProduzioneMassima' => Yii::t('app', 'Mrp Produzione Massima'),
            'MrpIncludi' => Yii::t('app', 'Mrp Includi'),
            'MrpGiorniCopertura' => Yii::t('app', 'Mrp Giorni Copertura'),
            'MrpResa' => Yii::t('app', 'Mrp Resa'),
            'MrpLottoRiordino' => Yii::t('app', 'Mrp Lotto Riordino'),
            'MrpLottoMinimo' => Yii::t('app', 'Mrp Lotto Minimo'),
            'MrpPuntoRiordino' => Yii::t('app', 'Mrp Punto Riordino'),
            'MrpIgnoraDistinta' => Yii::t('app', 'Mrp Ignora Distinta'),
            'WebB2CPubblica' => Yii::t('app', 'Web B2c Pubblica'),
            'WebB2BPubblica' => Yii::t('app', 'Web B2b Pubblica'),
            'WebDescrizione' => Yii::t('app', 'Web Descrizione'),
            'WebNote_AR' => Yii::t('app', 'Web Note Ar'),
            'WebInfoLink' => Yii::t('app', 'Web Info Link'),
            'WebGiacenza' => Yii::t('app', 'Web Giacenza'),
            'AmsManaged' => Yii::t('app', 'Ams Managed'),
            'NoteOfferta' => Yii::t('app', 'Note Offerta'),
            'Attributi' => Yii::t('app', 'Attributi'),
            'NoteXML' => Yii::t('app', 'Note Xml'),
            'UserIns' => Yii::t('app', 'User Ins'),
            'UserUpd' => Yii::t('app', 'User Upd'),
            'TimeIns' => Yii::t('app', 'Time Ins'),
            'TimeUpd' => Yii::t('app', 'Time Upd'),
            'Ts' => Yii::t('app', 'Ts'),
            'FTE_CodiceTipo' => Yii::t('app', 'Fte Codice Tipo'),
            'FTE_CodiceValore' => Yii::t('app', 'Fte Codice Valore'),
            'Cd_ARClasse12' => Yii::t('app', 'Cd Ar Classe12'),
            'Cd_ARClasse123' => Yii::t('app', 'Cd Ar Classe123'),
            'Cd_ARGruppo12' => Yii::t('app', 'Cd Ar Gruppo12'),
            'Cd_ARGruppo123' => Yii::t('app', 'Cd Ar Gruppo123'),
            'Cd_Nazione_Origine' => Yii::t('app', 'Cd Nazione Origine'),
            'xPerc' => Yii::t('app', 'X Perc'),
            'ExtraInfo' => Yii::t('app', 'Extra Info'),
            'xSoggettoRitAcconto' => Yii::t('app', 'X Soggetto Rit Acconto'),
            'xSoggettoRitEnasarco' => Yii::t('app', 'X Soggetto Rit Enasarco'),
            'xEA_Lunedi' => Yii::t('app', 'X Ea Lunedi'),
            'xEA_Martedi' => Yii::t('app', 'X Ea Martedi'),
            'xEA_Mercoledi' => Yii::t('app', 'X Ea Mercoledi'),
            'xEA_Giovedi' => Yii::t('app', 'X Ea Giovedi'),
            'xEA_Venerdi' => Yii::t('app', 'X Ea Venerdi'),
            'xEA_Sabato' => Yii::t('app', 'X Ea Sabato'),
            'xEA_Domenica' => Yii::t('app', 'X Ea Domenica'),
            'Cd_Provincia_Origine' => Yii::t('app', 'Cd Provincia Origine'),
        ];
    }

    /**
     * Gets query for [[CdAliquotaA]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdAliquotaA()
    {
        return $this->hasOne(Aliquota::className(), ['Cd_Aliquota' => 'Cd_Aliquota_A']);
    }

    /**
     * Gets query for [[CdAliquotaV]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdAliquotaV()
    {
        return $this->hasOne(Aliquota::className(), ['Cd_Aliquota' => 'Cd_Aliquota_V']);
    }

    /**
     * Gets query for [[ARCategoria]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARCategoria()
    {
        return $this->hasOne(ARCategoria::className(), ['Id_ARCategoria' => 'Id_ARCategoria']);
    }

    /**
     * Gets query for [[CdARClasse1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdARClasse1()
    {
        return $this->hasOne(ARClasse3::className(), ['Cd_ARClasse1' => 'Cd_ARClasse1', 'Cd_ARClasse2' => 'Cd_ARClasse2', 'Cd_ARClasse3' => 'Cd_ARClasse3']);
    }

    /**
     * Gets query for [[CdARMisura]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdARMisura()
    {
        return $this->hasOne(ARMisura::className(), ['Cd_ARMisura' => 'Cd_ARMisura']);
    }

    /**
     * Gets query for [[CdARGruppo1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdARGruppo1()
    {
        return $this->hasOne(ARGruppo3::className(), ['Cd_ARGruppo1' => 'Cd_ARGruppo1', 'Cd_ARGruppo2' => 'Cd_ARGruppo2', 'Cd_ARGruppo3' => 'Cd_ARGruppo3']);
    }

    /**
     * Gets query for [[CdARMarca]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdARMarca()
    {
        return $this->hasOne(ARMarca::className(), ['Cd_ARMarca' => 'Cd_ARMarca']);
    }

    /**
     * Gets query for [[CdARPrdClasse]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdARPrdClasse()
    {
        return $this->hasOne(ARPrdClasse::className(), ['Cd_ARPrdClasse' => 'Cd_ARPrdClasse']);
    }

    /**
     * Gets query for [[CdARStato]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdARStato()
    {
        return $this->hasOne(ARStato::className(), ['Cd_ARStato' => 'Cd_ARStato']);
    }

    /**
     * Gets query for [[CdCAVdaAE]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdCAVdaAE()
    {
        return $this->hasOne(CAVda::className(), ['Cd_CAVda' => 'Cd_CAVda_AE']);
    }

    /**
     * Gets query for [[CdCAVdaAI]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdCAVdaAI()
    {
        return $this->hasOne(CAVda::className(), ['Cd_CAVda' => 'Cd_CAVda_AI']);
    }

    /**
     * Gets query for [[CdCAVdaVE]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdCAVdaVE()
    {
        return $this->hasOne(CAVda::className(), ['Cd_CAVda' => 'Cd_CAVda_VE']);
    }

    /**
     * Gets query for [[CdCAVdaVI]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdCAVdaVI()
    {
        return $this->hasOne(CAVda::className(), ['Cd_CAVda' => 'Cd_CAVda_VI']);
    }

    /**
     * Gets query for [[CdNazioneOrigine]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdNazioneOrigine()
    {
        return $this->hasOne(Provincia::className(), ['Cd_Nazione' => 'Cd_Nazione_Origine', 'Cd_Provincia' => 'Cd_Provincia_Origine']);
    }

    /**
     * Gets query for [[CdCGContoAE]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdCGContoAE()
    {
        return $this->hasOne(CGConto::className(), ['Cd_CGConto' => 'Cd_CGConto_AE']);
    }

    /**
     * Gets query for [[CdCGContoAI]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdCGContoAI()
    {
        return $this->hasOne(CGConto::className(), ['Cd_CGConto' => 'Cd_CGConto_AI']);
    }

    /**
     * Gets query for [[CdCGContoVE]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdCGContoVE()
    {
        return $this->hasOne(CGConto::className(), ['Cd_CGConto' => 'Cd_CGConto_VE']);
    }

    /**
     * Gets query for [[CdARGruppo10]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdARGruppo10()
    {
        return $this->hasOne(ARGruppo1::className(), ['Cd_ARGruppo1' => 'Cd_ARGruppo1']);
    }

    /**
     * Gets query for [[CdCGContoVI]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdCGContoVI()
    {
        return $this->hasOne(CGConto::className(), ['Cd_CGConto' => 'Cd_CGConto_VI']);
    }

    /**
     * Gets query for [[CdNazioneOrigine0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdNazioneOrigine0()
    {
        return $this->hasOne(Nazione::className(), ['Cd_Nazione' => 'Cd_Nazione_Origine']);
    }

    /**
     * Gets query for [[CdVbReparto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdVbReparto()
    {
        return $this->hasOne(VBReparto::className(), ['Cd_VbReparto' => 'Cd_VbReparto']);
    }

    /**
     * Gets query for [[CdARClasse10]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdARClasse10()
    {
        return $this->hasOne(ARClasse1::className(), ['Cd_ARClasse1' => 'Cd_ARClasse1']);
    }

    /**
     * Gets query for [[CdARGruppo11]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdARGruppo11()
    {
        return $this->hasOne(ARGruppo2::className(), ['Cd_ARGruppo1' => 'Cd_ARGruppo1', 'Cd_ARGruppo2' => 'Cd_ARGruppo2']);
    }

    /**
     * Gets query for [[CdARClasse11]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdARClasse11()
    {
        return $this->hasOne(ARClasse2::className(), ['Cd_ARClasse1' => 'Cd_ARClasse1', 'Cd_ARClasse2' => 'Cd_ARClasse2']);
    }

    /**
     * Gets query for [[ARAliases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARAliases()
    {
        return $this->hasMany(ARAlias::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[ARARMisuras]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARARMisuras()
    {
        return $this->hasMany(ARARMisura::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[CdARMisuras]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdARMisuras()
    {
        return $this->hasMany(ARMisura::className(), ['Cd_ARMisura' => 'Cd_ARMisura'])->viaTable('ARARMisura', ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[ARCodCFs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARCodCFs()
    {
        return $this->hasMany(ARCodCF::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[CdCFs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdCFs()
    {
        return $this->hasMany(CF::className(), ['Cd_CF' => 'Cd_CF'])->viaTable('ARCodCF', ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[ARCodves]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARCodves()
    {
        return $this->hasMany(ARCodFE::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[ARCostoDBItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARCostoDBItems()
    {
        return $this->hasMany(ARCostoDBItem::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[CdMGEsercizios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdMGEsercizios()
    {
        return $this->hasMany(ARCostoDB::className(), ['Cd_MGEsercizio' => 'Cd_MGEsercizio', 'TipoCosto' => 'TipoCosto'])->viaTable('ARCostoDBItem', ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[ARCostoItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARCostoItems()
    {
        return $this->hasMany(ARCostoItem::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[CdMGEsercizios0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdMGEsercizios0()
    {
        return $this->hasMany(ARCosto::className(), ['Cd_MGEsercizio' => 'Cd_MGEsercizio', 'TipoCosto' => 'TipoCosto'])->viaTable('ARCostoItem', ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[ARImgs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARImgs()
    {
        return $this->hasMany(ARImg::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[ARImgWebs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARImgWebs()
    {
        return $this->hasMany(ARImgWeb::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[ARLinguas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARLinguas()
    {
        return $this->hasMany(ARLingua::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[Linguas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLinguas()
    {
        return $this->hasMany(Lingua::className(), ['Id_Lingua' => 'Id_Lingua'])->viaTable('ARLingua', ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[ARLottos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARLottos()
    {
        return $this->hasMany(ARLotto::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[ARMatricolas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARMatricolas()
    {
        return $this->hasMany(ARMatricola::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[ARMGUbicaziones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getARMGUbicaziones()
    {
        return $this->hasMany(ARMGUbicazione::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[CdMGs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdMGs()
    {
        return $this->hasMany(MGUbicazione::className(), ['Cd_MG' => 'Cd_MG', 'Cd_MGUbicazione' => 'Cd_MGUbicazione'])->viaTable('ARMGUbicazione', ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[DB0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDB0()
    {
        return $this->hasOne(DB::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[DBFases]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDBFases()
    {
        return $this->hasMany(DBFase::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[DBMateriales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDBMateriales()
    {
        return $this->hasMany(DBMateriale::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[DBVariantes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDBVariantes()
    {
        return $this->hasMany(DBVariante::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[DODBs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDODBs()
    {
        return $this->hasMany(DODB::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[DODBAttivitas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDODBAttivitas()
    {
        return $this->hasMany(DODBAttivita::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[DODBMateriales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDODBMateriales()
    {
        return $this->hasMany(DODBMateriale::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[DORigs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDORigs()
    {
        return $this->hasMany(DORig::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[EShopOrderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEShopOrderItems()
    {
        return $this->hasMany(EShopOrderItem::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[LSArticolos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLSArticolos()
    {
        return $this->hasMany(LSArticolo::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[LSRevisiones]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLSRevisiones()
    {
        return $this->hasMany(LSRevisione::className(), ['Id_LSRevisione' => 'Id_LSRevisione'])->viaTable('LSArticolo', ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[LSScARCFGruppos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLSScARCFGruppos()
    {
        return $this->hasMany(LSScARCFGruppo::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[MGMovs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMGMovs()
    {
        return $this->hasMany(MGMov::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[MGOrdImps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMGOrdImps()
    {
        return $this->hasMany(MGOrdImp::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[MPSnaps]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMPSnaps()
    {
        return $this->hasMany(MPSnap::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[PRBLAttivitas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPRBLAttivitas()
    {
        return $this->hasMany(PRBLAttivita::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[PRBLMateriales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPRBLMateriales()
    {
        return $this->hasMany(PRBLMateriale::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[PROLs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPROLs()
    {
        return $this->hasMany(PROL::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[PROLAttivitas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPROLAttivitas()
    {
        return $this->hasMany(PROLAttivita::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[PROLMateriales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPROLMateriales()
    {
        return $this->hasMany(PROLMateriale::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[PRTRMateriales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPRTRMateriales()
    {
        return $this->hasMany(PRTRMateriale::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[PRVRAttivitas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPRVRAttivitas()
    {
        return $this->hasMany(PRVRAttivita::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[PRVRMateriales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPRVRMateriales()
    {
        return $this->hasMany(PRVRMateriale::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[VBSconRs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVBSconRs()
    {
        return $this->hasMany(VBSconR::className(), ['Cd_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[XARFormatos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getXARFormatos()
    {
        return $this->hasMany(XARFormato::className(), ['CD_AR' => 'Cd_AR']);
    }

    /**
     * Gets query for [[XGestioneEmailAutos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getXGestioneEmailAutos()
    {
        return $this->hasMany(XGestioneEmailAuto::className(), ['CD_AR' => 'Cd_AR']);
    }
}
