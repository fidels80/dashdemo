<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "SCDistinta".
 *
 * @property int $Id_SCDistinta
 * @property int $NumeroDistinta
 * @property string|null $DataEmissione
 * @property string|null $DataValuta
 * @property string|null $Cd_CGConto
 * @property int|null $Id_BancaSupporto
 * @property string|null $Descrizione
 * @property string|null $NoteSCDistinta
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string|null $Ts
 * @property string|null $TipoSupporto
 * @property string|null $NomeTracciato
 * @property string|null $SddSchema
 */
class Scdistinta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'SCDistinta';
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
            [['NumeroDistinta', 'Id_BancaSupporto'], 'integer'],
            [['DataEmissione', 'DataValuta', 'TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['NoteSCDistinta'], 'string'],
            [['Cd_CGConto'], 'string', 'max' => 12],
            [['Descrizione'], 'string', 'max' => 50],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['TipoSupporto'], 'string', 'max' => 5],
            [['NomeTracciato'], 'string', 'max' => 30],
            [['SddSchema'], 'string', 'max' => 4],
            [['NumeroDistinta'], 'unique'],
            [['Cd_CGConto'], 'exist', 'skipOnError' => true, 'targetClass' => Banca::className(), 'targetAttribute' => ['Cd_CGConto' => 'Cd_CGConto']],
            [['Id_BancaSupporto'], 'exist', 'skipOnError' => true, 'targetClass' => BancaSupporto::className(), 'targetAttribute' => ['Id_BancaSupporto' => 'Id_BancaSupporto']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_SCDistinta' => 'Id Sc Distinta',
            'NumeroDistinta' => 'Numero Distinta',
            'DataEmissione' => 'Data Emissione',
            'DataValuta' => 'Data Valuta',
            'Cd_CGConto' => 'Cd Cg Conto',
            'Id_BancaSupporto' => 'Id Banca Supporto',
            'Descrizione' => 'Descrizione',
            'NoteSCDistinta' => 'Note Sc Distinta',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'TipoSupporto' => 'Tipo Supporto',
            'NomeTracciato' => 'Nome Tracciato',
            'SddSchema' => 'Sdd Schema',
        ];
    }
}
