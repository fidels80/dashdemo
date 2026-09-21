<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_anagrafica".
 */
class MgAnagrafica extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_anagrafica';
    }

    public function rules()
    {
        return [
            [['codice', 'ragione_sociale'], 'required'],
            [['attivo'], 'boolean'],
            [['codice'], 'string', 'max' => 20],
            [['ragione_sociale'], 'string', 'max' => 200],
            [['partita_iva', 'codice_fiscale'], 'string', 'max' => 20],
            [['indirizzo'], 'string', 'max' => 200],
            [['cap'], 'string', 'max' => 10],
            [['citta'], 'string', 'max' => 100],
            [['provincia'], 'string', 'max' => 3],
            [['telefono'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 100],
            [['tipo'], 'string', 'max' => 20],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'ragione_sociale' => 'Ragione sociale',
            'partita_iva' => 'Partita IVA',
            'codice_fiscale' => 'Codice fiscale',
            'indirizzo' => 'Indirizzo',
            'cap' => 'CAP',
            'citta' => 'Città',
            'provincia' => 'Provincia',
            'telefono' => 'Telefono',
            'email' => 'Email',
            'tipo' => 'Tipo',
            'attivo' => 'Attivo',
        ];
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['ragione_sociale' => SORT_ASC])->all(),
            'id',
            'ragione_sociale'
        );
    }
}
