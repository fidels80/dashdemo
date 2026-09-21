<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uec_provincia".
 *
 * @property string $Cd_Provincia
 * @property string $Descrizione
 */
class UecProvincia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uec_provincia';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Cd_Provincia', 'Descrizione'], 'required'],
            [['Cd_Provincia'], 'string', 'max' => 3],
            [['Descrizione'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Cd_Provincia' => 'Cd Provincia',
            'Descrizione' => 'Descrizione',
        ];
    }
}
