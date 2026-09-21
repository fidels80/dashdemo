<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dash_permesso".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property string|null $gruppo
 * @property int $ordine
 * @property bool $attivo
 * @property string|null $created_at
 */
class DashPermesso extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dash_permesso';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['ordine'], 'integer'],
            [['attivo'], 'boolean'],
            [['created_at'], 'safe'],
            [['codice'], 'string', 'max' => 50],
            [['descrizione'], 'string', 'max' => 150],
            [['gruppo'], 'string', 'max' => 50],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice (controller)',
            'descrizione' => 'Descrizione',
            'gruppo' => 'Gruppo',
            'ordine' => 'Ordine',
            'attivo' => 'Attivo',
            'created_at' => 'Creato il',
        ];
    }

    public function getAssegnazioni()
    {
        return $this->hasMany(DashPermessoUtente::className(), ['permesso_id' => 'id']);
    }

    public static function findByCodice($codice)
    {
        return self::findOne(['codice' => $codice, 'attivo' => 1]);
    }
}
