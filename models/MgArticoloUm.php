<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_articolo_um".
 *
 * Unità di misura associata a un articolo, con fattore di conversione
 * verso l'unità base e flag di unità predefinita.
 *
 * @property int $id
 * @property int $id_articolo
 * @property int $id_unita_misura
 * @property float $fattore
 * @property bool $predefinita
 * @property bool $attivo
 *
 * @property MgArticolo $articolo
 * @property MgUnitaMisura $unitaMisura
 */
class MgArticoloUm extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_articolo_um';
    }

    public function rules()
    {
        return [
            [['id_articolo', 'id_unita_misura'], 'required'],
            [['id_articolo', 'id_unita_misura'], 'integer'],
            [['fattore'], 'number', 'min' => 0],
            [['predefinita', 'attivo'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_articolo' => 'Articolo',
            'id_unita_misura' => 'Unità di misura',
            'fattore' => 'Fattore conversione',
            'predefinita' => 'Predefinita',
            'attivo' => 'Attivo',
        ];
    }

    public function getArticolo()
    {
        return $this->hasOne(MgArticolo::className(), ['id' => 'id_articolo']);
    }

    public function getUnitaMisura()
    {
        return $this->hasOne(MgUnitaMisura::className(), ['id' => 'id_unita_misura']);
    }

    public function getCodiceUm()
    {
        return $this->unitaMisura ? $this->unitaMisura->codice : '';
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->fattore === '' || $this->fattore === null) {
                $this->fattore = 1;
            }
            return true;
        }
        return false;
    }
}
