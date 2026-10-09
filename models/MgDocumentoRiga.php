<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_documento_riga".
 *
 * @property int|null $id_aliquota_iva
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
            [['id_documento', 'id_articolo', 'id_unita_misura', 'id_sottocommessa', 'ordine'], 'integer'],
            [['id_aliquota_iva'], 'integer'],
            [['id_aliquota_iva'], 'exist',
                'targetClass' => MgAliquotaIva::className(),
                'targetAttribute' => ['id_aliquota_iva' => 'id'],
                'skipOnEmpty' => true],
            [['id_magazzino_partenza', 'id_magazzino_arrivo'], 'integer'],
            [['id_magazzino_partenza'], 'exist',
                'targetClass' => MgMagazzino::className(),
                'targetAttribute' => ['id_magazzino_partenza' => 'id'],
                'skipOnEmpty' => true],
            [['id_magazzino_arrivo'], 'exist',
                'targetClass' => MgMagazzino::className(),
                'targetAttribute' => ['id_magazzino_arrivo' => 'id'],
                'skipOnEmpty' => true],
            [['id_rapportino'], 'safe'],
            [['qta', 'prezzo', 'sconto', 'iva', 'totale', 'fattore'], 'number'],
            [['codice_articolo'], 'string', 'max' => 25],
            [['codice_tipo'], 'string', 'max' => 20],
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
            'codice_tipo' => 'Codice tipo documento',
            'descrizione' => 'Descrizione',
            'id_unita_misura' => 'Unità di misura',
            'id_aliquota_iva' => 'Aliquota IVA',
            'id_sottocommessa' => 'Sottocommessa',
            'id_magazzino_partenza' => 'Magazzino partenza',
            'id_magazzino_arrivo' => 'Magazzino arrivo',
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

    public function getAliquotaIva()
    {
        return $this->hasOne(MgAliquotaIva::className(), ['id' => 'id_aliquota_iva']);
    }

    public function getRapportino()
    {
        return $this->hasOne(Rapportini::className(), ['id' => 'id_rapportino']);
    }

    public function getDettagli()
    {
        return $this->hasMany(MgDocumentoRigaDettaglio::className(), ['id_documento_riga' => 'id'])
            ->orderBy(['ordine' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public function getSottocommessa()
    {
        return $this->hasOne(MgSottocommessa::className(), ['id' => 'id_sottocommessa']);
    }

    public function getMagazzinoPartenza()
    {
        return $this->hasOne(MgMagazzino::className(), ['id' => 'id_magazzino_partenza']);
    }

    public function getMagazzinoArrivo()
    {
        return $this->hasOne(MgMagazzino::className(), ['id' => 'id_magazzino_arrivo']);
    }

    public function getMagazzinoPartenzaLabel()
    {
        return $this->magazzinoPartenza ? $this->magazzinoPartenza->etichetta : null;
    }

    public function getMagazzinoArrivoLabel()
    {
        return $this->magazzinoArrivo ? $this->magazzinoArrivo->etichetta : null;
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
