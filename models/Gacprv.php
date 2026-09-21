<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gac_prv".
 *
 * @property int $id_prv
 * @property string $descrizione
 */
class Gacprv extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gac_prv';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descrizione'], 'required'],
            [['descrizione'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_prv' => 'Id Prv',
            'descrizione' => 'Descrizione',
        ];
    }
}
