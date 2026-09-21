<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uec_articoli".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 */
class Uecarticoli extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uec_articoli';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['codice'], 'string', 'max' => 25],
            [['descrizione'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
        ];
    }
}
