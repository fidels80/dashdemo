<?php

namespace app\models;

/**
 * This is the model class for table "mg_anagrafica_contatto".
 *
 * Contatti di un'anagrafica (N contatti per anagrafica, di tipo diverso).
 *
 * @property int $id
 * @property int $id_anagrafica
 * @property int $id_tipo_contatto
 * @property string $valore
 * @property string|null $etichetta
 * @property bool $predefinito
 * @property string|null $note
 * @property bool $attivo
 * @property string|null $created_at
 *
 * @property MgAnagrafica $anagrafica
 * @property MgTipoContatto $tipoContatto
 */
class MgAnagraficaContatto extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_anagrafica_contatto';
    }

    public function rules()
    {
        return [
            [['id_anagrafica', 'id_tipo_contatto', 'valore'], 'required'],
            [['id_anagrafica', 'id_tipo_contatto'], 'integer'],
            [['predefinito', 'attivo'], 'boolean'],
            [['valore'], 'string', 'max' => 200],
            [['etichetta'], 'string', 'max' => 100],
            [['note'], 'string', 'max' => 500],
            [['created_at'], 'safe'],
            [['id_anagrafica', 'id_tipo_contatto', 'valore'], 'unique',
                'targetAttribute' => ['id_anagrafica', 'id_tipo_contatto', 'valore'],
                'message' => 'Questo contatto è già presente per l\'anagrafica.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_anagrafica' => 'Anagrafica',
            'id_tipo_contatto' => 'Tipo contatto',
            'valore' => 'Contatto',
            'etichetta' => 'Etichetta',
            'predefinito' => 'Predefinito',
            'note' => 'Note',
            'attivo' => 'Attivo',
            'created_at' => 'Creato il',
        ];
    }

    public function getAnagrafica()
    {
        return $this->hasOne(MgAnagrafica::className(), ['id' => 'id_anagrafica']);
    }

    public function getTipoContatto()
    {
        return $this->hasOne(MgTipoContatto::className(), ['id' => 'id_tipo_contatto']);
    }

    public function getTipoLabel()
    {
        return $this->tipoContatto ? $this->tipoContatto->descrizione : '';
    }

    public function getIcona()
    {
        if ($this->tipoContatto && !empty($this->tipoContatto->icona)) {
            return $this->tipoContatto->icona;
        }
        return 'fas fa-address-card';
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord && empty($this->created_at)) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            if ($this->etichetta === '') {
                $this->etichetta = null;
            }
            if ($this->note === '') {
                $this->note = null;
            }
            return true;
        }
        return false;
    }
}
