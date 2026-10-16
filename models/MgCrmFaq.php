<?php

namespace app\models;

use Yii;

/**
 * FAQ generata dalla chiusura di un ticket: articolo, domanda del cliente
 * (descrizione) e soluzione.
 *
 * @property int $id
 * @property int|null $id_ticket
 * @property int|null $id_articolo
 * @property string|null $codice_articolo
 * @property string $domanda
 * @property string|null $soluzione
 * @property bool $attivo
 * @property string|null $created_by
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property MgCrmTicket|null $ticket
 * @property MgArticolo|null $articolo
 */
class MgCrmFaq extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_crm_faq';
    }

    public function rules()
    {
        return [
            [['domanda'], 'required'],
            [['id_ticket', 'id_articolo'], 'integer'],
            [['attivo'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['domanda'], 'string', 'max' => 500],
            [['soluzione'], 'string', 'max' => 4000],
            [['codice_articolo'], 'string', 'max' => 25],
            [['created_by'], 'string', 'max' => 50],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_ticket' => 'Ticket di origine',
            'id_articolo' => 'Articolo',
            'codice_articolo' => 'Codice articolo',
            'domanda' => 'Domanda',
            'soluzione' => 'Soluzione',
            'attivo' => 'Attivo',
            'created_by' => 'Creato da',
            'created_at' => 'Creato il',
            'updated_at' => 'Aggiornato il',
        ];
    }

    public function getTicket()
    {
        return $this->hasOne(MgCrmTicket::className(), ['id' => 'id_ticket']);
    }

    public function getArticolo()
    {
        return $this->hasOne(MgArticolo::className(), ['id' => 'id_articolo']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->codice_articolo === '') {
                $this->codice_articolo = null;
            }
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
     * Crea una FAQ a partire da un ticket (se non esiste gia' per quel ticket).
     *
     * @param MgCrmTicket $ticket
     * @return MgCrmFaq
     */
    public static function daTicket(MgCrmTicket $ticket)
    {
        $faq = new self();
        $faq->id_ticket = $ticket->id;
        $faq->id_articolo = $ticket->id_articolo;
        if ($ticket->articolo) {
            $faq->codice_articolo = $ticket->articolo->codice;
        }
        $faq->domanda = mb_substr((string) ($ticket->descrizione ?: $ticket->oggetto), 0, 500);
        $faq->soluzione = $ticket->soluzione;
        $faq->attivo = 1;
        return $faq;
    }
}
