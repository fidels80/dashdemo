<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "plist".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property string $tipo
 */
class Plist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'plist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codice', 'descrizione', 'tipo'], 'required'],
            [['codice'], 'string', 'max' => 10],
            [['descrizione'], 'string', 'max' => 200],
            [['tipo'], 'string', 'max' => 1],
            [['codice'], 'unique'],
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
            'tipo' => 'Tipo',
        ];
    }
}
