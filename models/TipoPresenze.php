<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_presenze".
 *
 * @property string $codice
 * @property string $descrizione
 */
class TipoPresenze extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_presenze';
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
    // In app\models\Planning.php
public function behaviors()
{
    return [
        \app\components\LogBehavior::class,
    ];
}
}
