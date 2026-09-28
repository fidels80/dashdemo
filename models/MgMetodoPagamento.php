<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_metodo_pagamento".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property int|null $id_tipo_pagamento
 * @property string $partenza
 * @property int $giorni_partenza
 * @property int $n_rate
 * @property bool $attivo
 * @property string|null $created_at
 *
 * @property MgMetodoPagamentoRata[] $rate
 * @property MgTipoPagamento $tipoPagamento
 */
class MgMetodoPagamento extends \yii\db\ActiveRecord
{
    const PARTENZA_EMISSIONE = 'emissione';
    const PARTENZA_GIORNI_DOPO = 'giorni_dopo';
    const PARTENZA_INIZIO_MESE = 'inizio_mese';
    const PARTENZA_FINE_MESE = 'fine_mese';

    public static function tableName()
    {
        return 'mg_metodo_pagamento';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['id_tipo_pagamento', 'giorni_partenza', 'n_rate'], 'integer'],
            [['attivo'], 'boolean'],
            [['created_at'], 'safe'],
            [['codice'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 200],
            [['partenza'], 'string', 'max' => 20],
            [['partenza'], 'in', 'range' => array_keys(self::opzioniPartenza())],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'id_tipo_pagamento' => 'Tipologia',
            'partenza' => 'Partenza rate',
            'giorni_partenza' => 'Giorni dalla data documento',
            'n_rate' => 'Numero rate',
            'attivo' => 'Attivo',
            'created_at' => 'Creato il',
        ];
    }

    public static function opzioniPartenza()
    {
        return [
            self::PARTENZA_EMISSIONE => 'Data emissione documento',
            self::PARTENZA_GIORNI_DOPO => 'X giorni dopo la data documento',
            self::PARTENZA_INIZIO_MESE => 'Inizio mese',
            self::PARTENZA_FINE_MESE => 'Fine mese',
        ];
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public static function mapAttivi()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['attivo' => 1])->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public function getTipoPagamento()
    {
        return $this->hasOne(MgTipoPagamento::className(), ['id' => 'id_tipo_pagamento']);
    }

    public function getRate()
    {
        return $this->hasMany(MgMetodoPagamentoRata::className(), ['id_metodo' => 'id'])
            ->orderBy(['progressivo' => SORT_ASC]);
    }

    public function getPartenzaLabel()
    {
        $opzioni = self::opzioniPartenza();
        return $opzioni[$this->partenza] ?? $this->partenza;
    }

    /**
     * Calcola la data di partenza delle rate a partire dalla data del documento.
     *
     * @param string $dataDocumento data in formato Y-m-d
     * @return string data in formato Y-m-d
     */
    public function calcolaDataPartenza($dataDocumento)
    {
        $ts = strtotime($dataDocumento);
        if ($ts === false) {
            $ts = time();
        }
        $ts = strtotime(($this->giorni_partenza >= 0 ? '+' : '') . (int) $this->giorni_partenza . ' day', $ts);

        switch ($this->partenza) {
            case self::PARTENZA_INIZIO_MESE:
                return date('Y-m-01', $ts);
            case self::PARTENZA_FINE_MESE:
                return date('Y-m-t', $ts);
            case self::PARTENZA_GIORNI_DOPO:
            case self::PARTENZA_EMISSIONE:
            default:
                return date('Y-m-d', $ts);
        }
    }

    /**
     * Calcola le scadenze (data + percentuale) del metodo per un documento.
     *
     * @param string $dataDocumento
     * @return array[] ogni elemento: ['progressivo','giorni','percentuale','data']
     */
    public function calcolaScadenze($dataDocumento)
    {
        $partenza = $this->calcolaDataPartenza($dataDocumento);
        $out = [];
        foreach ($this->rate as $rata) {
            $data = date('Y-m-d', strtotime($partenza . ' ' . (int) $rata->giorni . ' day'));
            $out[] = [
                'progressivo' => (int) $rata->progressivo,
                'giorni' => (int) $rata->giorni,
                'percentuale' => (float) $rata->percentuale,
                'data' => $data,
            ];
        }
        return $out;
    }

    /**
     * Allinea n_rate al numero di rate configurate.
     */
    public function sincronizzaNumeroRate()
    {
        $n = (int) $this->getRate()->count();
        if ($n > 0 && (int) $this->n_rate !== $n) {
            $this->n_rate = $n;
            $this->save(false, ['n_rate']);
        }
    }
}
