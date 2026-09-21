<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "x_Statistiche".
 *
 * @property string $Cd_Do
 * @property string|null $Cd_Agente_1
 * @property string $DataDoc
 * @property string|null $NumeroDoc
 * @property string|null $Cd_AR
 * @property string|null $Descrizione
 * @property float|null $x_ore
 * @property string|null $x_cd_cf
 * @property string|null $x_citta
 * @property string|null $x_data
 */
class x_Statistiche extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'x_Statistiche';
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
            [['Cd_Do', 'DataDoc'], 'required'],
            [['DataDoc', 'x_data'], 'safe'],
            [['x_ore'], 'number'],
            [['Cd_Do', 'Cd_Agente_1'], 'string', 'max' => 3],
            [['NumeroDoc'], 'string', 'max' => 10],
            [['Cd_AR'], 'string', 'max' => 20],
            [['Descrizione'], 'string', 'max' => 80],
            [['x_cd_cf'], 'string', 'max' => 7],
            [['x_citta'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Cd_Do' => 'Cd Do',
            'Cd_Agente_1' => 'Cd Agente 1',
            'DataDoc' => 'Data Doc',
            'NumeroDoc' => 'Numero Doc',
            'Cd_AR' => 'Cd Ar',
            'Descrizione' => 'Descrizione',
            'x_ore' => 'X Ore',
            'x_cd_cf' => 'X Cd Cf',
            'x_citta' => 'X Citta',
            'x_data' => 'X Data',
        ];
    }
}
