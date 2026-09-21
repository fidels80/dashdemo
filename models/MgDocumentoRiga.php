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
            [['id_documento', 'id_articolo', 'ordine'], 'integer'],
            [['qta', 'prezzo', 'sconto', 'iva', 'totale'], 'number'],
            [['codice_articolo'], 'string', 'max' => 25],
            [['descrizione'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_documento' => 'Documento',
            'id_articolo' => 'Articolo',
            'codice_articolo' => 'Codice',
            'descrizione' => 'Descrizione',
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

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $qta = (float) $this->qta;
            $prezzo = (float) $this->prezzo;
            $sconto = (float) $this->sconto;
            $this->totale = round($qta * $prezzo * (1 - $sconto / 100), 2);
            return true;
        }
        return false;
    }
}
