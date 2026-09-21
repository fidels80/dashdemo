<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "LS".
 *
 * @property int $Id_LS
 * @property string $Cd_LS
 * @property string $Descrizione Descrizione del listino
 Defau
 * @property string $Cd_VL Codice valuta. 
 * @property int $Ivato .T. se il listino risulta comp
 * @property int $TipoLS Tipo di listino: 1 Clienti, 2 
 * @property string $Sconto Espressione per la % di sconto
 * @property string $Provvigione Espressione per la % di provvi
 * @property int $Avanzato
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string|null $Ts
 * @property string|null $Note_LS
 *
 * @property CF[] $cFs
 * @property CF[] $cFs0
 * @property Ditta[] $dittas
 * @property DO[] $dOs
 * @property DO[] $dOs0
 * @property DO[] $dOs1
 * @property DORig[] $dORigs
 * @property DOTes[] $dOTes
 * @property DOTes[] $dOTes0
 * @property DOTes[] $dOTes1
 * @property EShopOrder[] $eShopOrders
 * @property VL $cdVL
 * @property LSRevisione[] $lSRevisiones
 * @property LSRevisione[] $lSRevisiones0
 * @property VBConfigurazione[] $vBConfiguraziones
 */
class Listini extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'LS';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db4');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Cd_LS', 'Ivato', 'TipoLS', 'Avanzato'], 'required'],
            [['Ivato', 'TipoLS', 'Avanzato'], 'integer'],
            [['TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['Note_LS'], 'string'],
            [['Cd_LS'], 'string', 'max' => 7],
            [['Descrizione'], 'string', 'max' => 50],
            [['Cd_VL'], 'string', 'max' => 3],
            [['Sconto', 'Provvigione'], 'string', 'max' => 10],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['Cd_LS'], 'unique'],
            [['Cd_VL'], 'exist', 'skipOnError' => true, 'targetClass' => VL::className(), 'targetAttribute' => ['Cd_VL' => 'Cd_VL']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_LS' => 'Id Ls',
            'Cd_LS' => 'Cd Ls',
            'Descrizione' => 'Descrizione',
            'Cd_VL' => 'Cd Vl',
            'Ivato' => 'Ivato',
            'TipoLS' => 'Tipo Ls',
            'Sconto' => 'Sconto',
            'Provvigione' => 'Provvigione',
            'Avanzato' => 'Avanzato',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'Note_LS' => 'Note Ls',
        ];
    }

    /**
     * Gets query for [[CFs]].
     *
     * @return \yii\db\ActiveQuery
     */
    }
