<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "DmsDocument".
 *
 * @property int $Id_DmsDocument
 * @property int|null $Id_DmsDocument_Parent
 * @property int $Id_DmsClass1
 * @property int $Id_DmsClass2
 * @property string $DmsClass3
 * @property string $Cd_DmsType
 * @property string $Tipo
 * @property string $DocumentDate
 * @property string $Descrizione
 * @property string $NumeroRif
 * @property string|null $Note
 * @property string|null $EntityId
 * @property string|null $EntityTable
 * @property string|null $MasterEntityId
 * @property string|null $MasterEntityTable
 * @property string|null $MixedEntityTable
 * @property string|null $MixedEntityId
 * @property bool $MixedIsMaster
 * @property string|null $EntityDescription
 * @property bool $LinkedToFS
 * @property int $LinkedToEntity
 * @property resource|null $Content
 * @property bool $Ciphered
 * @property int $ContentSize
 * @property string $ComputerName
 * @property string|null $FilePath
 * @property string $FileName
 * @property string $FileExt
 * @property int $FileSize
 * @property string $Tag TAG per riconosce tipo record.
 * @property bool $Use4Fepa
 * @property bool $Use4FepaEx
 * @property bool $Use4Peppol
 * @property int|null $Id_ARKmanager
 * @property int $ARK_State
 * @property int|null $IdFatturaRX
 * @property string|null $FTEInfo
 * @property int|null $Id_VdA
 * @property string|null $Id_PdV
 * @property string|null $Ud_CSmart
 * @property string|null $Attributi
 * @property string|null $WebDesk Riferimenti invio a WebDesk
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string $Ts
 * @property string|null $ExtraInfo Informazioni extra del documen
 * @property string|null $DescrizioneEx
 *
 * @property Dmsdocument $dmsDocumentParent
 * @property Dmsdocument[] $dmsdocuments
 * @property DmsType $cdDmsType
 * @property DmsClass2 $dmsClass1
 */
class Dmsdocument extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'DmsDocument';
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
            [['Id_DmsDocument_Parent', 'Id_DmsClass1', 'Id_DmsClass2', 'LinkedToEntity', 'ContentSize', 'FileSize', 'Id_ARKmanager', 'ARK_State', 'IdFatturaRX', 'Id_VdA'], 'integer'],
            [['DocumentDate', 'Descrizione', 'MixedIsMaster', 'LinkedToFS', 'LinkedToEntity', 'ContentSize', 'FileName', 'FileExt', 'FileSize', 'Use4Fepa', 'Use4FepaEx', 'Use4Peppol', 'Ts'], 'required'],
            [['DocumentDate', 'TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['Note', 'EntityId', 'MasterEntityId', 'MixedEntityId', 'EntityDescription', 'Content', 'FTEInfo', 'Ud_CSmart', 'Attributi', 'WebDesk', 'ExtraInfo'], 'string'],
            [['MixedIsMaster', 'LinkedToFS', 'Ciphered', 'Use4Fepa', 'Use4FepaEx', 'Use4Peppol'], 'boolean'],
            [['DmsClass3', 'NumeroRif'], 'string', 'max' => 20],
            [['Cd_DmsType'], 'string', 'max' => 2],
            [['Tipo'], 'string', 'max' => 1],
            [['Descrizione'], 'string', 'max' => 200],
            [['EntityTable', 'MasterEntityTable', 'MixedEntityTable'], 'string', 'max' => 128],
            [['ComputerName', 'Id_PdV'], 'string', 'max' => 50],
            [['FilePath', 'FileName', 'FileExt'], 'string', 'max' => 260],
            [['Tag'], 'string', 'max' => 3],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['DescrizioneEx'], 'string', 'max' => 219],
            [['Id_DmsDocument_Parent'], 'exist', 'skipOnError' => true, 'targetClass' => Dmsdocument::className(), 'targetAttribute' => ['Id_DmsDocument_Parent' => 'Id_DmsDocument']],
            [['Cd_DmsType'], 'exist', 'skipOnError' => true, 'targetClass' => DmsType::className(), 'targetAttribute' => ['Cd_DmsType' => 'Cd_DmsType']],
            [['Id_DmsClass1', 'Id_DmsClass2'], 'exist', 'skipOnError' => true, 'targetClass' => DmsClass2::className(), 'targetAttribute' => ['Id_DmsClass1' => 'Id_DmsClass1', 'Id_DmsClass2' => 'Id_DmsClass2']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_DmsDocument' => 'Id Dms Document',
            'Id_DmsDocument_Parent' => 'Id Dms Document Parent',
            'Id_DmsClass1' => 'Id Dms Class1',
            'Id_DmsClass2' => 'Id Dms Class2',
            'DmsClass3' => 'Dms Class3',
            'Cd_DmsType' => 'Cd Dms Type',
            'Tipo' => 'Tipo',
            'DocumentDate' => 'Document Date',
            'Descrizione' => 'Descrizione',
            'NumeroRif' => 'Numero Rif',
            'Note' => 'Note',
            'EntityId' => 'Entity ID',
            'EntityTable' => 'Entity Table',
            'MasterEntityId' => 'Master Entity ID',
            'MasterEntityTable' => 'Master Entity Table',
            'MixedEntityTable' => 'Mixed Entity Table',
            'MixedEntityId' => 'Mixed Entity ID',
            'MixedIsMaster' => 'Mixed Is Master',
            'EntityDescription' => 'Entity Description',
            'LinkedToFS' => 'Linked To Fs',
            'LinkedToEntity' => 'Linked To Entity',
            'Content' => 'Content',
            'Ciphered' => 'Ciphered',
            'ContentSize' => 'Content Size',
            'ComputerName' => 'Computer Name',
            'FilePath' => 'File Path',
            'FileName' => 'File Name',
            'FileExt' => 'File Ext',
            'FileSize' => 'File Size',
            'Tag' => 'Tag',
            'Use4Fepa' => 'Use4fepa',
            'Use4FepaEx' => 'Use4fepa Ex',
            'Use4Peppol' => 'Use4peppol',
            'Id_ARKmanager' => 'Id Ar Kmanager',
            'ARK_State' => 'Ark State',
            'IdFatturaRX' => 'Id Fattura Rx',
            'FTEInfo' => 'Fte Info',
            'Id_VdA' => 'Id Vd A',
            'Id_PdV' => 'Id Pd V',
            'Ud_CSmart' => 'Ud C Smart',
            'Attributi' => 'Attributi',
            'WebDesk' => 'Web Desk',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'ExtraInfo' => 'Extra Info',
            'DescrizioneEx' => 'Descrizione Ex',
        ];
    }

    /**
     * Gets query for [[DmsDocumentParent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDmsDocumentParent()
    {
        return $this->hasOne(Dmsdocument::className(), ['Id_DmsDocument' => 'Id_DmsDocument_Parent']);
    }

    /**
     * Gets query for [[Dmsdocuments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDmsdocuments()
    {
        return $this->hasMany(Dmsdocument::className(), ['Id_DmsDocument_Parent' => 'Id_DmsDocument']);
    }

    /**
     * Gets query for [[CdDmsType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCdDmsType()
    {
        return $this->hasOne(DmsType::className(), ['Cd_DmsType' => 'Cd_DmsType']);
    }

    /**
     * Gets query for [[DmsClass1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDmsClass1()
    {
        return $this->hasOne(DmsClass2::className(), ['Id_DmsClass1' => 'Id_DmsClass1', 'Id_DmsClass2' => 'Id_DmsClass2']);
    }
}
