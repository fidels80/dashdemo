<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "SL".
 *
 * @property int $Id_SL Identificativo univoco
 * @property string $Cd_SL Codice del sollecito
 * @property string $Descrizione Descrizione
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string|null $Ts
 * @property string|null $Note_SL
 */
class Sl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'SL';
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
            [['Cd_SL', 'Descrizione'], 'required'],
            [['TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['Note_SL'], 'string'],
            [['Cd_SL'], 'string', 'max' => 10],
            [['Descrizione'], 'string', 'max' => 60],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['Cd_SL'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_SL' => 'Id Sl',
            'Cd_SL' => 'Cd Sl',
            'Descrizione' => 'Descrizione',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
            'Note_SL' => 'Note Sl',
        ];
    }
}
