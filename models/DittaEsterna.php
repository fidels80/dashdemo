<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dittaesterna".
 *
 * @property string $codice
 * @property string $descrizione
 *
 * @property Planning[] $plannings
 */
class DittaEsterna extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dittaesterna';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['codice'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 255],
            [['codice'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
        ];
    }

    /**
     * Gets query for [[Plannings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlannings()
    {
        return $this->hasMany(Planning::className(), ['ditta_esterna' => 'codice']);
    }
}
