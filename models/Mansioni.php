<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mansioni".
 *
 * @property string $codice
 * @property string $descrizione
 */
class Mansioni extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mansioni';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['codice'], 'string', 'max' => 10],
            [['descrizione'], 'string', 'max' => 100],
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
