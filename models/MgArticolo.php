<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_articolo".
 *
 * @property int $id
 * @property string $guid
 * @property string $codice
 * @property string $descrizione
 * @property string|null $um
 * @property float $prezzo
 * @property float $iva
 * @property int|null $id_iva_vendita
 * @property int|null $id_iva_acquisto
 * @property int|null $id_marca
 * @property int|null $id_modello
 * @property int|null $id_tessuto
 * @property int|null $id_taglia
 * @property int|null $id_colore
 * @property bool $attivo
 *
 * @property MgAliquotaIva $ivaVendita
 * @property MgAliquotaIva $ivaAcquisto
 * @property MgAttributoArticolo $marca
 * @property MgAttributoArticolo $modello
 * @property MgAttributoArticolo $tessuto
 * @property MgAttributoArticolo $taglia
 * @property MgAttributoArticolo $colore
 * @property MgArticoloUm[] $unitaMisura
 */
class MgArticolo extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_articolo';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['prezzo', 'iva'], 'number'],
            [['id_iva_vendita', 'id_iva_acquisto', 'id_marca', 'id_modello', 'id_tessuto', 'id_taglia', 'id_colore'], 'integer'],
            [['attivo'], 'boolean'],
            [['guid'], 'safe'],
            [['codice'], 'string', 'max' => 25],
            [['descrizione'], 'string', 'max' => 250],
            [['um'], 'string', 'max' => 10],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'guid' => 'GUID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'um' => 'U.M.',
            'prezzo' => 'Prezzo',
            'iva' => 'IVA %',
            'id_iva_vendita' => 'IVA vendita',
            'id_iva_acquisto' => 'IVA acquisto',
            'id_marca' => 'Marca',
            'id_modello' => 'Modello',
            'id_tessuto' => 'Tessuto',
            'id_taglia' => 'Taglia',
            'id_colore' => 'Colore',
            'attivo' => 'Attivo',
        ];
    }

    public function getIvaVendita()
    {
        return $this->hasOne(MgAliquotaIva::className(), ['id' => 'id_iva_vendita']);
    }

    public function getIvaAcquisto()
    {
        return $this->hasOne(MgAliquotaIva::className(), ['id' => 'id_iva_acquisto']);
    }

    public function getMarca()
    {
        return $this->hasOne(MgAttributoArticolo::className(), ['id' => 'id_marca']);
    }

    public function getModello()
    {
        return $this->hasOne(MgAttributoArticolo::className(), ['id' => 'id_modello']);
    }

    public function getTessuto()
    {
        return $this->hasOne(MgAttributoArticolo::className(), ['id' => 'id_tessuto']);
    }

    public function getTaglia()
    {
        return $this->hasOne(MgAttributoArticolo::className(), ['id' => 'id_taglia']);
    }

    public function getColore()
    {
        return $this->hasOne(MgAttributoArticolo::className(), ['id' => 'id_colore']);
    }

    public function getUnitaMisura()
    {
        return $this->hasMany(MgArticoloUm::className(), ['id_articolo' => 'id'])
            ->orderBy(['predefinita' => SORT_DESC, 'id' => SORT_ASC]);
    }

    /**
     * Unità di misura predefinita dell'articolo (o la prima disponibile).
     */
    public function getUnitaPredefinita()
    {
        foreach ($this->unitaMisura as $u) {
            if ($u->predefinita) {
                return $u;
            }
        }
        $prime = $this->unitaMisura;
        return !empty($prime) ? $prime[0] : null;
    }

    /**
     * Etichetta varianti: marca/modello/taglia/colore.
     */
    public function getVariantiLabel()
    {
        $parti = [];
        foreach (['marca', 'modello', 'tessuto', 'taglia', 'colore'] as $rel) {
            if ($this->$rel) {
                $parti[] = $this->$rel->descrizione;
            }
        }
        return implode(' / ', $parti);
    }

    /**
     * Percentuale IVA di vendita (fallback sul vecchio campo iva).
     */
    public function getIvaVenditaPerc()
    {
        if ($this->ivaVendita) {
            return (float) $this->ivaVendita->percentuale;
        }
        return (float) $this->iva;
    }

    /**
     * Percentuale IVA di acquisto (fallback sul vecchio campo iva).
     */
    public function getIvaAcquistoPerc()
    {
        if ($this->ivaAcquisto) {
            return (float) $this->ivaAcquisto->percentuale;
        }
        return (float) $this->iva;
    }

    /**
     * Matrice taglie per un modello: righe = tessuto+colore, colonne = taglie.
     * Ogni cella riporta l'articolo (variante) corrispondente.
     *
     * @return array{taglie: array, righe: array}
     */
    public static function matriceTaglie($idModello)
    {
        $articoli = self::find()
            ->with(['tessuto', 'colore', 'taglia', 'ivaVendita'])
            ->where(['id_modello' => $idModello])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $taglie = [];   // key => label, in ordine di prima occorrenza
        $righe = [];    // "tessuto|colore" => riga
        foreach ($articoli as $a) {
            $tKey = $a->id_tessuto ? (int) $a->id_tessuto : 0;
            $cKey = $a->id_colore ? (int) $a->id_colore : 0;
            $zKey = $a->id_taglia ? (int) $a->id_taglia : 0;

            if (!array_key_exists($zKey, $taglie)) {
                $taglie[$zKey] = $a->taglia ? $a->taglia->descrizione : '—';
            }

            $rKey = $tKey . '|' . $cKey;
            if (!isset($righe[$rKey])) {
                $righe[$rKey] = [
                    'tessuto' => $a->tessuto ? $a->tessuto->descrizione : '',
                    'colore' => $a->colore ? $a->colore->descrizione : '',
                    'celle' => [],
                ];
            }
            $righe[$rKey]['celle'][(string) $zKey] = [
                'id_articolo' => (int) $a->id,
                'codice' => $a->codice,
                'descrizione' => $a->descrizione,
                'prezzo' => (float) $a->prezzo,
                'iva' => $a->ivaVenditaPerc,
                'um' => $a->um,
                'taglia' => $a->taglia ? $a->taglia->descrizione : '',
                'colore' => $a->colore ? $a->colore->descrizione : '',
                'tessuto' => $a->tessuto ? $a->tessuto->descrizione : '',
            ];
        }

        $taglieOut = [];
        foreach ($taglie as $k => $label) {
            $taglieOut[] = ['key' => (string) $k, 'label' => $label];
        }

        return ['taglie' => $taglieOut, 'righe' => array_values($righe)];
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->id_iva_vendita === '') {
                $this->id_iva_vendita = null;
            }
            if ($this->id_iva_acquisto === '') {
                $this->id_iva_acquisto = null;
            }
            foreach (['id_marca', 'id_modello', 'id_tessuto', 'id_taglia', 'id_colore'] as $attr) {
                if ($this->$attr === '') {
                    $this->$attr = null;
                }
            }
            // Mantiene allineato il vecchio campo iva con l'aliquota di vendita
            if ($this->id_iva_vendita) {
                $al = MgAliquotaIva::findOne($this->id_iva_vendita);
                if ($al) {
                    $this->iva = (float) $al->percentuale;
                }
            }
            return true;
        }
        return false;
    }
}
