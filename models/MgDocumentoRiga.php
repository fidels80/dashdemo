<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_documento_riga".
 */
class MgDocumentoRiga extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_documento_riga';
    }

    public function rules()
    {
        return [
            [['id_documento'], 'required'],
            [['id_documento', 'id_articolo', 'id_unita_misura', 'ordine'], 'integer'],
            [['id_rapportino'], 'safe'],
            [['qta', 'prezzo', 'sconto', 'iva', 'totale', 'fattore'], 'number'],
            [['codice_articolo'], 'string', 'max' => 25],
            [['um'], 'string', 'max' => 10],
            [['taglia', 'colore', 'tessuto'], 'string', 'max' => 50],
            [['descrizione'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_documento' => 'Documento',
            'id_articolo' => 'Articolo',
            'id_rapportino' => 'Rapportino',
            'codice_articolo' => 'Codice',
            'descrizione' => 'Descrizione',
            'id_unita_misura' => 'Unità di misura',
            'um' => 'U.M.',
            'taglia' => 'Taglia',
            'colore' => 'Colore',
            'tessuto' => 'Tessuto',
            'fattore' => 'Fattore conversione',
            'qta' => 'Q.tà',
            'prezzo' => 'Prezzo',
            'sconto' => 'Sconto %',
            'iva' => 'IVA %',
            'totale' => 'Totale',
            'ordine' => 'Ordine',
        ];
    }

    public function getDocumento()
    {
        return $this->hasOne(MgDocumento::className(), ['id' => 'id_documento']);
    }

    public function getArticolo()
    {
        return $this->hasOne(MgArticolo::className(), ['id' => 'id_articolo']);
    }

    public function getUnitaMisura()
    {
        return $this->hasOne(MgUnitaMisura::className(), ['id' => 'id_unita_misura']);
    }

    public function getRapportino()
    {
        return $this->hasOne(Rapportini::className(), ['id' => 'id_rapportino']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $qta = (float) $this->qta;
            $prezzo = (float) $this->prezzo;
            $sconto = (float) $this->sconto;
            $this->totale = round($qta * $prezzo * (1 - $sconto / 100), 2);
            if ($this->fattore === '' || $this->fattore === null) {
                $this->fattore = 1;
            }
            return true;
        }
        return false;
    }
}
