<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gac_sottocommessa".
 *
 * @property string $commessa_p
 * @property string $codice
 * @property string|null $descrizione
 */
class GacSottocommessa extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gac_sottocommessa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['commessa_p', 'codice'], 'required'],
            [['commessa_p', 'codice', 'descrizione'], 'string', 'max' => 10],
            [['codice'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'commessa_p' => 'Commessa P',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
        ];
    }
}
