<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Richiesta/ticket CRM aperta da un utente esterno o interno.
 *
 * @property int $id
 * @property int $anno
 * @property int $numero
 * @property int $id_tipo
 * @property int $id_stato
 * @property int|null $id_priorita
 * @property int|null $id_anagrafica
 * @property int|null $id_utente
 * @property int|null $id_articolo
 * @property int|null $id_matricola
 * @property int|null $id_documento
 * @property string $oggetto
 * @property string|null $descrizione
 * @property int|null $assegnato_a
 * @property string $data_apertura
 * @property string|null $data_presa_carico
 * @property string|null $data_chiusura
 * @property string|null $data_scadenza
 * @property string|null $created_by
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property MgCrmTipo $tipo
 * @property MgCrmStato $stato
 * @property MgCrmPriorita $priorita
 * @property MgAnagrafica $anagrafica
 * @property User|null $utente
 * @property User|null $assegnato
 * @property MgArticolo|null $articolo
 * @property MgMatricola|null $matricola
 * @property MgDocumento|null $documento
 * @property MgCrmTicketMessaggio[] $messaggi
 */
class MgCrmTicket extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_crm_ticket';
    }

    public function rules()
    {
        return [
            [['id_tipo', 'id_stato', 'oggetto'], 'required'],
            [['anno', 'numero', 'id_tipo', 'id_stato', 'id_priorita', 'id_anagrafica', 'id_utente',
                'id_articolo', 'id_matricola', 'id_documento', 'assegnato_a'], 'integer'],
            [['data_apertura', 'data_presa_carico', 'data_chiusura', 'data_scadenza',
                'created_at', 'updated_at'], 'safe'],
            [['oggetto'], 'string', 'max' => 250],
            [['descrizione'], 'string', 'max' => 2000],
            [['created_by'], 'string', 'max' => 50],
            [['tempo_impiegato_ore'], 'number', 'min' => 0],
            [['soluzione'], 'string', 'max' => 4000],
            [['id_rapportino'], 'string', 'max' => 36],
            [['evaso'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'anno' => 'Anno',
            'numero' => 'Numero',
            'id_tipo' => 'Tipo richiesta',
            'id_stato' => 'Stato',
            'id_priorita' => 'Priorità',
            'id_anagrafica' => 'Cliente',
            'id_utente' => 'Aperto da',
            'id_articolo' => 'Articolo',
            'id_matricola' => 'Matricola',
            'id_documento' => 'Documento collegato',
            'oggetto' => 'Oggetto',
            'descrizione' => 'Descrizione',
            'assegnato_a' => 'Assegnato a',
            'data_apertura' => 'Data apertura',
            'data_presa_carico' => 'Data presa in carico',
            'data_chiusura' => 'Data chiusura',
            'data_scadenza' => 'Data scadenza',
            'created_by' => 'Creato da',
            'created_at' => 'Creato il',
            'updated_at' => 'Aggiornato il',
            'tempo_impiegato_ore' => 'Tempo impiegato (ore)',
            'soluzione' => 'Soluzione',
            'id_rapportino' => 'Rapportino',
            'evaso' => 'Evaso in rapportino',
        ];
    }

    public function getTipo()
    {
        return $this->hasOne(MgCrmTipo::className(), ['id' => 'id_tipo']);
    }

    public function getStato()
    {
        return $this->hasOne(MgCrmStato::className(), ['id' => 'id_stato']);
    }

    public function getPriorita()
    {
        return $this->hasOne(MgCrmPriorita::className(), ['id' => 'id_priorita']);
    }

    public function getAnagrafica()
    {
        return $this->hasOne(MgAnagrafica::className(), ['id' => 'id_anagrafica']);
    }

    public function getUtente()
    {
        return $this->hasOne(User::className(), ['id' => 'id_utente']);
    }

    public function getAssegnato()
    {
        return $this->hasOne(User::className(), ['id' => 'assegnato_a']);
    }

    public function getArticolo()
    {
        return $this->hasOne(MgArticolo::className(), ['id' => 'id_articolo']);
    }

    public function getMatricola()
    {
        return $this->hasOne(MgMatricola::className(), ['id' => 'id_matricola']);
    }

    public function getDocumento()
    {
        return $this->hasOne(MgDocumento::className(), ['id' => 'id_documento']);
    }

    public function getMessaggi()
    {
        return $this->hasMany(MgCrmTicketMessaggio::className(), ['id_ticket' => 'id'])
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public function getAllegati()
    {
        return $this->hasMany(MgCrmAllegato::className(), ['id_ticket' => 'id'])
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC]);
    }

    /**
     * Allegati di testata del ticket (non legati a un commento).
     */
    public function getAllegatiTestata()
    {
        return $this->getAllegati()->andWhere(['id_messaggio' => null]);
    }

    public function getFaq()
    {
        return $this->hasOne(MgCrmFaq::className(), ['id_ticket' => 'id']);
    }

    /**
     * Commento attualmente marcato come soluzione (se presente).
     */
    public function getMessaggioSoluzione()
    {
        return MgCrmTicketMessaggio::find()
            ->where(['id_ticket' => $this->id, 'is_soluzione' => 1])
            ->orderBy(['id' => SORT_DESC])
            ->one();
    }

    /**
     * Messaggi visibili al cliente (esclude le note interne).
     */
    public function getMessaggiPubblici()
    {
        return $this->getMessaggi()
            ->andWhere(['is_nota_interna' => 0]);
    }

    public function getEtichetta()
    {
        return 'CRM ' . $this->anno . '/' . str_pad((string) $this->numero, 4, '0', STR_PAD_LEFT);
    }

    public function isChiuso()
    {
        return $this->stato && $this->stato->is_chiuso;
    }

    /**
     * Etichetta del colore Bootstrap dello stato (per i badge).
     */
    public function getStatoColore()
    {
        return $this->stato && $this->stato->colore ? $this->stato->colore : 'secondary';
    }

    public function getPrioritaColore()
    {
        return $this->priorita && $this->priorita->colore ? $this->priorita->colore : 'secondary';
    }

    /**
     * Primo numero libero per l'anno (progressivo semplice).
     *
     * @param int $anno
     * @return int
     */
    public static function proponiNumero($anno)
    {
        $max = (int) self::find()->where(['anno' => (int) $anno])->max('numero');
        return $max + 1;
    }

    /**
     * Query filtrata sulle anagrafiche visibili a un utente esterno.
     *
     * @param int $userId
     * @return \yii\db\ActiveQuery
     */
    public static function queryEsterno($userId)
    {
        $ids = MgCrmUtenteAnagrafica::idsAnagraficheDiUtente($userId);
        if (empty($ids)) {
            return self::find()->where('1=0');
        }
        return self::find()->where(['id_anagrafica' => $ids]);
    }

    /**
     * Ticket assegnabili a un operatore (per le tendine).
     */
    public static function mapOperatori()
    {
        $operatori = User::find()
            ->where(['status' => User::STATUS_ACTIVE])
            ->andWhere(['>=', 'level', 70])
            ->andWhere(['not', ['is_esterno' => 1]])
            ->orderBy(['username' => SORT_ASC])
            ->all();
        return ArrayHelper::map($operatori, 'id', 'username');
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            foreach (['id_priorita', 'id_anagrafica', 'id_utente', 'id_articolo', 'id_matricola', 'id_documento', 'assegnato_a'] as $attr) {
                if ($this->$attr === '') {
                    $this->$attr = null;
                }
            }
            if (empty($this->data_apertura)) {
                $this->data_apertura = new \yii\db\Expression('GETDATE()');
            }
            if (empty($this->anno) && !empty($this->data_apertura)) {
                $this->anno = (int) date('Y', strtotime($this->data_apertura));
            }
            if (empty($this->anno)) {
                $this->anno = (int) date('Y');
            }
            if (empty($this->numero)) {
                $this->numero = self::proponiNumero($this->anno);
            }
            $this->evaso = $this->evaso ? 1 : 0;
            if ($insert) {
                $this->created_at = new \yii\db\Expression('GETDATE()');
                if (empty($this->created_by)) {
                    $this->created_by = Yii::$app->user->identity->username ?? null;
                }
            }
            $this->updated_at = new \yii\db\Expression('GETDATE()');
            return true;
        }
        return false;
    }

    /**
     * Un ticket evaso in rapportino non puo' essere eliminato.
     */
    public function puoEliminare(&$motivo = null)
    {
        $motivo = null;
        if ($this->evaso || !empty($this->id_rapportino)) {
            $motivo = 'Impossibile eliminare: il ticket è stato evaso in un rapportino.';
            return false;
        }
        return true;
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        $motivo = null;
        if (!$this->puoEliminare($motivo)) {
            return false;
        }
        // Elimina gli allegati (e i relativi file) del ticket e dei suoi commenti.
        foreach (MgCrmAllegato::find()->where(['id_ticket' => $this->id])->all() as $a) {
            $a->delete();
        }
        return true;
    }
}
