<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_aliquota_iva".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property float $percentuale
 * @property bool $attivo
 * @property string|null $fe_natura
 */
class MgAliquotaIva extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_aliquota_iva';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['percentuale'], 'number'],
            [['attivo'], 'boolean'],
            [['codice'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 100],
            [['fe_natura'], 'string', 'max' => 4],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'percentuale' => '%',
            'attivo' => 'Attivo',
            'fe_natura' => 'Natura IVA (SDI)',
        ];
    }

    /**
     * Etichetta della natura IVA SDI.
     */
    public function getFeNaturaLabel()
    {
        $opzioni = \app\components\FatturaElettronica::opzioniNaturaIva();
        return $opzioni[$this->fe_natura] ?? $this->fe_natura;
    }

    /**
     * Natura IVA da usare per una riga con la percentuale indicata.
     * Cerca l'aliquota attiva con quella percentuale e ne restituisce la
     * natura; null se non trovata.
     *
     * @param float $percentuale
     * @return string|null
     */
    public static function naturaPerPercentuale($percentuale)
    {
        $aliquota = self::find()
            ->where(['percentuale' => $percentuale])
            ->orderBy(['attivo' => SORT_DESC, 'id' => SORT_ASC])
            ->one();
        return $aliquota ? $aliquota->fe_natura : null;
    }

    /**
     * Etichetta composta per le tendine: "22 - IVA 22% (22,00%)".
     */
    public function getEtichetta()
    {
        return $this->codice . ' - ' . $this->descrizione
            . ' (' . number_format((float) $this->percentuale, 2, ',', '.') . '%)';
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['percentuale' => SORT_DESC, 'codice' => SORT_ASC])->all(),
            'id',
            function ($m) {
                return $m->etichetta;
            }
        );
    }

    public static function mapAttivi()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['attivo' => 1])->orderBy(['percentuale' => SORT_DESC, 'codice' => SORT_ASC])->all(),
            'id',
            function ($m) {
                return $m->etichetta;
            }
        );
    }

    /**
     * Mappa id => percentuale, usata per proporre l'IVA nelle righe documento.
     */
    public static function mapPercentuali()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->all(),
            'id',
            'percentuale'
        );
    }
}
