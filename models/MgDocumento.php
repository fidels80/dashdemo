<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_documento".
 *
 * @property int $id
 * @property int $id_tipo
 * @property string $codice_tipo
 * @property int $anno
 * @property int $numero
 * @property string $suffisso
 * @property string $data
 * @property int|null $id_anagrafica
 * @property string|null $descrizione
 * @property string|null $stato
 * @property float|null $totale
 * @property int|null $id_metodo_pagamento
 * @property string|null $note
 * @property string|null $created_by
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class MgDocumento extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_documento';
    }

    public function rules()
    {
        return [
            [['id_tipo', 'anno', 'numero', 'data'], 'required'],
            [['id_tipo', 'anno', 'numero', 'id_anagrafica', 'id_metodo_pagamento'], 'integer'],
            [['data', 'created_at', 'updated_at'], 'safe'],
            [['totale'], 'number'],
            [['codice_tipo'], 'string', 'max' => 20],
            [['suffisso'], 'string', 'max' => 10],
            [['descrizione'], 'string', 'max' => 500],
            [['stato'], 'string', 'max' => 20],
            [['note'], 'string', 'max' => 2000],
            [['created_by'], 'string', 'max' => 50],
            ['numero', 'validateNumero'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_tipo' => 'Tipo documento',
            'codice_tipo' => 'Codice tipo',
            'anno' => 'Anno',
            'numero' => 'Numero',
            'suffisso' => 'Suffisso',
            'data' => 'Data',
            'id_anagrafica' => 'Cliente/Fornitore',
            'descrizione' => 'Descrizione',
            'stato' => 'Stato',
            'totale' => 'Totale',
            'id_metodo_pagamento' => 'Metodo di pagamento',
            'note' => 'Note',
            'created_by' => 'Creato da',
            'created_at' => 'Creato il',
            'updated_at' => 'Aggiornato il',
        ];
    }

    public function getTipo()
    {
        return $this->hasOne(MgTipoDocumento::className(), ['id' => 'id_tipo']);
    }

    public function getAnagrafica()
    {
        return $this->hasOne(MgAnagrafica::className(), ['id' => 'id_anagrafica']);
    }

    public function getRighe()
    {
        return $this->hasMany(MgDocumentoRiga::className(), ['id_documento' => 'id'])
            ->orderBy(['ordine' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public function getMetodoPagamento()
    {
        return $this->hasOne(MgMetodoPagamento::className(), ['id' => 'id_metodo_pagamento']);
    }

    public function getScadenze()
    {
        return $this->hasMany(MgScadenza::className(), ['id_documento' => 'id'])
            ->orderBy(['progressivo' => SORT_ASC]);
    }

    /**
     * Rigenera le scadenze del documento in base al metodo di pagamento.
     * Il flag "crea scadenze" è definito sul tipo documento; se spento o
     * manca il metodo, elimina le scadenze esistenti.
     *
     * @return int numero di scadenze generate
     */
    public function generaScadenze()
    {
        MgScadenza::deleteAll(['id_documento' => $this->id]);

        $tipo = $this->tipo;
        if (!$tipo || !$tipo->crea_scadenze || !$this->id_metodo_pagamento) {
            return 0;
        }

        $metodo = MgMetodoPagamento::findOne($this->id_metodo_pagamento);
        if (!$metodo) {
            return 0;
        }

        $totale = (float) $this->totale;
        $create = 0;
        foreach ($metodo->calcolaScadenze($this->data) as $r) {
            $scadenza = new MgScadenza();
            $scadenza->id_documento = $this->id;
            $scadenza->id_metodo_pagamento = $metodo->id;
            $scadenza->progressivo = $r['progressivo'];
            $scadenza->data_scadenza = $r['data'];
            $scadenza->percentuale = $r['percentuale'];
            $scadenza->importo = round($totale * $r['percentuale'] / 100, 2);
            $scadenza->stato = 'aperta';
            $scadenza->created_at = new \yii\db\Expression('GETDATE()');
            if ($scadenza->save(false)) {
                $create++;
            }
        }
        return $create;
    }

    /**
     * True se almeno una scadenza del documento risulta pagata (stato != 'aperta').
     */
    public function hasScadenzePagate()
    {
        return MgScadenza::find()
            ->where(['id_documento' => $this->id])
            ->andWhere(['not', ['stato' => 'aperta']])
            ->exists();
    }

    /**
     * True se si possono prelevare/evadere rapportini: documento in bozza
     * e senza scadenze già pagate.
     */
    public function puoPrelevare()
    {
        return in_array($this->stato, [null, '', 'bozza'], true) && !$this->hasScadenzePagate();
    }

    /**
     * Etichetta completa del documento: CODICE ANNO/NUMERO[/suffisso].
     */
    public function getEtichetta()
    {
        $s = $this->codice_tipo . ' ' . $this->anno . '/' . $this->numero;
        if (!empty($this->suffisso)) {
            $s .= '/' . $this->suffisso;
        }
        return $s;
    }

    /**
     * Verifica unicità (tipo+anno+numero+suffisso) e congruità numero/data.
     */
    public function validateNumero($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }
        if (!$this->id_tipo || !$this->anno || $this->numero === null || empty($this->data)) {
            return;
        }

        // Unicità numero + suffisso per tipo/anno
        $query = self::find()->where([
            'id_tipo' => $this->id_tipo,
            'anno' => $this->anno,
            'numero' => $this->numero,
            'suffisso' => (string) $this->suffisso,
        ]);
        if (!$this->isNewRecord) {
            $query->andWhere(['not', ['id' => $this->id]]);
        }
        if ($query->exists()) {
            $this->addError('numero',
                'Esiste già un documento con questo numero e suffisso per il tipo e l\'anno selezionati.');
            return;
        }

        // Congruità numeri/date
        if (!self::numeroCongruente($this->id_tipo, $this->anno, $this->numero, $this->data, $this->id)) {
            $this->addError('numero',
                'Numero non congruente con le date: non è possibile usare un numero più alto per una data precedente (o viceversa).');
        }
    }

    /**
     * Proponi il primo numero libero per tipo/anno (gap-filling).
     * Tiene conto del flag di congruità del tipo documento.
     *
     * @return int|null null se la numerazione non è automatica
     */
    public static function proponiNumero($idTipo, $anno, $data = null)
    {
        $tipo = MgTipoDocumento::findOne($idTipo);
        if (!$tipo || !$tipo->usa_progressivo) {
            return null;
        }
        $data = $data ?: date('Y-m-d');

        $numeri = self::find()
            ->select(['numero'])
            ->where(['id_tipo' => $idTipo, 'anno' => $anno])
            ->column();

        $esistenti = array_map('intval', $numeri);
        $max = empty($esistenti) ? 0 : max($esistenti);

        // Primo numero libero che rispetta anche la congruità delle date
        for ($n = 1; $n <= $max + 1; $n++) {
            if (in_array($n, $esistenti, true)) {
                continue;
            }
            if (self::numeroCongruente($idTipo, $anno, $n, $data)) {
                return $n;
            }
        }

        return $max + 1;
    }

    /**
     * Verifica se il numero è congruente con la data rispetto agli altri documenti.
     * Se il tipo non ha il flag di congruità attivo, restituisce sempre true.
     */
    public static function numeroCongruente($idTipo, $anno, $numero, $data, $escludiId = null)
    {
        $tipo = MgTipoDocumento::findOne($idTipo);
        if (!$tipo || !$tipo->congruita) {
            return true;
        }

        $base = self::find()->where(['id_tipo' => $idTipo, 'anno' => $anno]);
        if (!empty($escludiId)) {
            $base->andWhere(['not', ['id' => $escludiId]]);
        }

        // Esiste un numero superiore con data precedente?
        $violazione = (clone $base)
            ->andWhere(['>', 'numero', $numero])
            ->andWhere(['<', 'data', $data])
            ->exists();
        if ($violazione) {
            return false;
        }

        // Esiste un numero inferiore con data successiva?
        $violazione2 = (clone $base)
            ->andWhere(['<', 'numero', $numero])
            ->andWhere(['>', 'data', $data])
            ->exists();

        return !$violazione2;
    }

    /**
     * Ricalcola il totale del documento dalla somma delle righe.
     */
    public function calcolaTotale()
    {
        $tot = (float) MgDocumentoRiga::find()
            ->where(['id_documento' => $this->id])
            ->sum('totale');
        $this->totale = $tot;
        return $this->save(false, ['totale', 'updated_at']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Anno derivato dalla data se non impostato
            if (empty($this->anno) && !empty($this->data)) {
                $this->anno = (int) date('Y', strtotime($this->data));
            }
            if ($this->suffisso === null) {
                $this->suffisso = '';
            }
            if ($this->id_anagrafica === '') {
                $this->id_anagrafica = null;
            }
            if ($this->id_metodo_pagamento === '') {
                $this->id_metodo_pagamento = null;
            }
            // Codice tipo denormalizzato
            if ($this->id_tipo) {
                $tipo = MgTipoDocumento::findOne($this->id_tipo);
                if ($tipo) {
                    $this->codice_tipo = $tipo->codice;
                }
            }
            if (empty($this->stato)) {
                $this->stato = 'bozza';
            }
            if ($insert) {
                $this->created_at = new \yii\db\Expression('GETDATE()');
                $this->created_by = Yii::$app->user->identity->username ?? null;
            }
            $this->updated_at = new \yii\db\Expression('GETDATE()');
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Aggiorna il contatore del tipo documento
        $tipo = MgTipoDocumento::findOne($this->id_tipo);
        if ($tipo) {
            $tipo->aggiornaContatore($this->numero, $this->anno);
        }
    }
}
