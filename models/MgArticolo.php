<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_articolo".
 */
class MgArticolo extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_articolo';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['prezzo', 'iva'], 'number'],
            [['attivo'], 'boolean'],
            [['codice'], 'string', 'max' => 25],
            [['descrizione'], 'string', 'max' => 250],
            [['um'], 'string', 'max' => 10],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'um' => 'U.M.',
            'prezzo' => 'Prezzo',
            'iva' => 'IVA %',
            'attivo' => 'Attivo',
        ];
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }
}
