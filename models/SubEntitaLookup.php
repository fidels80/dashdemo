<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sub_entita_lookup".
 *
 * @property string $codice
 * @property string $descrizione
 */
class SubEntitaLookup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sub_entita_lookup';
    }

    /**
     * {@inheritdoc}
     */
public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['codice'], 'string', 'max' => 100],
            [['descrizione'], 'string', 'max' => 255],
            [['pgreq'], 'boolean'], // Nuovo campo
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'pgreq' => 'Richiede Pagamento?',
        ];
    }

}