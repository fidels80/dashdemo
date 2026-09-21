<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Vl".
 *
 * @property int $Id_VL
 * @property string $Cd_VL Codice della valuta
 * @property string $Descrizione Descrizione della valuta
 * @property string|null $Simbolo
 * @property int $Decimali Numero di decimali per rappres
 * @property int $DecimaliPrzUn Numero di decimali da consider
 * @property string|null $PicNor Picture normale (senza separat
 * @property string|null $PicSep Picture con separatori di migl
 * @property string|null $PicNorPrzUn Picture normale (senza separat
 * @property string|null $PicSepPrzUn Picture con separatori di migl
 * @property int $Euro
 * @property float|null $EuroCambio
 * @property string|null $EuroData
 * @property string $UserIns
 * @property string $UserUpd
 * @property string $TimeIns
 * @property string $TimeUpd
 * @property string|null $Ts
 */
class Vl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Vl';
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
            [['Cd_VL'], 'required'],
            [['Decimali', 'DecimaliPrzUn', 'Euro'], 'integer'],
            [['EuroCambio'], 'number'],
            [['EuroData', 'TimeIns', 'TimeUpd', 'Ts'], 'safe'],
            [['Cd_VL'], 'string', 'max' => 3],
            [['Descrizione'], 'string', 'max' => 30],
            [['Simbolo'], 'string', 'max' => 2],
            [['PicNor', 'PicSep', 'PicNorPrzUn', 'PicSepPrzUn'], 'string', 'max' => 20],
            [['UserIns', 'UserUpd'], 'string', 'max' => 48],
            [['Cd_VL'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id_VL' => 'Id Vl',
            'Cd_VL' => 'Cd Vl',
            'Descrizione' => 'Descrizione',
            'Simbolo' => 'Simbolo',
            'Decimali' => 'Decimali',
            'DecimaliPrzUn' => 'Decimali Prz Un',
            'PicNor' => 'Pic Nor',
            'PicSep' => 'Pic Sep',
            'PicNorPrzUn' => 'Pic Nor Prz Un',
            'PicSepPrzUn' => 'Pic Sep Prz Un',
            'Euro' => 'Euro',
            'EuroCambio' => 'Euro Cambio',
            'EuroData' => 'Euro Data',
            'UserIns' => 'User Ins',
            'UserUpd' => 'User Upd',
            'TimeIns' => 'Time Ins',
            'TimeUpd' => 'Time Upd',
            'Ts' => 'Ts',
        ];
    }
}
