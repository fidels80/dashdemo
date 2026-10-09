<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_anagrafica".
 *
 * @property string|null $fe_codice_destinatario
 * @property string|null $fe_pec
 * @property string|null $fe_id_paese
 * @property string|null $fe_nazione
 * @property string|null $fe_tipo_soggetto
 * @property string|null $fe_nome
 * @property string|null $fe_cognome
 * @property string|null $fe_regime_fiscale
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
            [['attivo', 'is_cliente', 'is_fornitore', 'is_agente'], 'boolean'],
            [['id_metodo_pagamento', 'id_aliquota_iva'], 'integer'],
            [['perc_provvigione'], 'number', 'min' => 0, 'max' => 100],
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
            [['fe_codice_destinatario'], 'string', 'max' => 7],
            [['fe_pec'], 'string', 'max' => 100],
            [['fe_id_paese', 'fe_nazione'], 'string', 'max' => 2],
            [['fe_tipo_soggetto'], 'string', 'max' => 1],
            [['fe_nome', 'fe_cognome'], 'string', 'max' => 100],
            [['fe_regime_fiscale'], 'string', 'max' => 4],
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
            'is_cliente' => 'Cliente',
            'is_fornitore' => 'Fornitore',
            'is_agente' => 'Agente',
            'perc_provvigione' => '% provvigione',
            'id_metodo_pagamento' => 'Metodo di pagamento',
            'id_aliquota_iva' => 'Aliquota IVA',
            'fe_codice_destinatario' => 'Codice destinatario',
            'fe_pec' => 'PEC',
            'fe_id_paese' => 'Paese (ISO)',
            'fe_nazione' => 'Nazione (ISO)',
            'fe_tipo_soggetto' => 'Tipo soggetto',
            'fe_nome' => 'Nome',
            'fe_cognome' => 'Cognome',
            'fe_regime_fiscale' => 'Regime fiscale',
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

    /**
     * Clienti (codice => ragione sociale) per le tendine che salvano il codice.
     */
    public static function mapClienti()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['is_cliente' => 1])->orderBy(['ragione_sociale' => SORT_ASC])->all(),
            'codice',
            'ragione_sociale'
        );
    }

    /**
     * Anagrafiche filtrate per destinazione del documento ('cliente'|'fornitore').
     * Usata dalla form documento per mostrare solo gli intestatari coerenti.
     */
    public static function mapForDestinazione($destinazione)
    {
        $query = self::find();
        if ($destinazione === MgTipoDocumento::DEST_FORNITORE) {
            $query->where(['is_fornitore' => 1]);
        } else {
            $query->where(['is_cliente' => 1]);
        }
        return \yii\helpers\ArrayHelper::map(
            $query->orderBy(['ragione_sociale' => SORT_ASC])->all(),
            'id',
            'ragione_sociale'
        );
    }

    public function getMetodoPagamento()
    {
        return $this->hasOne(MgMetodoPagamento::className(), ['id' => 'id_metodo_pagamento']);
    }

    public function getAliquotaIva()
    {
        return $this->hasOne(MgAliquotaIva::className(), ['id' => 'id_aliquota_iva']);
    }

    public function getContatti()
    {
        return $this->hasMany(MgAnagraficaContatto::className(), ['id_anagrafica' => 'id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->id_metodo_pagamento === '') {
                $this->id_metodo_pagamento = null;
            }
            if ($this->id_aliquota_iva === '') {
                $this->id_aliquota_iva = null;
            }
            if ($this->perc_provvigione === '' || $this->perc_provvigione === null) {
                $this->perc_provvigione = 0;
            }
            return true;
        }
        return false;
    }

    /**
     * Etichetta dei ruoli attivi (Cliente, Fornitore, Agente).
     */
    public function getTipiLabel()
    {
        $tipi = [];
        if ($this->is_cliente) {
            $tipi[] = 'Cliente';
        }
        if ($this->is_fornitore) {
            $tipi[] = 'Fornitore';
        }
        if ($this->is_agente) {
            $tipi[] = 'Agente';
        }
        return implode(', ', $tipi);
    }
}
