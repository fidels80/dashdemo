<?php

namespace app\models;

use Yii;

/**
 * Messaggio/commento di un ticket CRM. Se is_nota_interna = 1 il messaggio
 * e' visibile solo agli operatori interni.
 *
 * @property int $id
 * @property int $id_ticket
 * @property int|null $user_id
 * @property string|null $autore
 * @property bool $is_nota_interna
 * @property string $messaggio
 * @property string|null $created_at
 *
 * @property MgCrmTicket $ticket
 * @property User|null $utente
 */
class MgCrmTicketMessaggio extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_crm_ticket_messaggio';
    }

    public function rules()
    {
        return [
            [['id_ticket', 'messaggio'], 'required'],
            [['id_ticket', 'user_id'], 'integer'],
            [['is_nota_interna', 'is_soluzione'], 'boolean'],
            [['created_at'], 'safe'],
            [['messaggio'], 'string', 'max' => 2000],
            [['autore'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_ticket' => 'Ticket',
            'user_id' => 'Utente',
            'autore' => 'Autore',
            'is_nota_interna' => 'Nota interna',
            'is_soluzione' => 'Soluzione',
            'messaggio' => 'Messaggio',
            'created_at' => 'Creato il',
        ];
    }

    public function getTicket()
    {
        return $this->hasOne(MgCrmTicket::className(), ['id' => 'id_ticket']);
    }

    public function getUtente()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getAllegati()
    {
        return $this->hasMany(MgCrmAllegato::className(), ['id_messaggio' => 'id'])
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC]);
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        foreach (MgCrmAllegato::find()->where(['id_messaggio' => $this->id])->all() as $a) {
            $a->delete();
        }
        return true;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->is_nota_interna = $this->is_nota_interna ? 1 : 0;
            $this->is_soluzione = $this->is_soluzione ? 1 : 0;
            if ($insert && empty($this->created_at)) {
                $this->created_at = new \yii\db\Expression('GETDATE()');
            }
            if (empty($this->autore) && !empty($this->user_id)) {
                $u = User::findOne($this->user_id);
                if ($u) {
                    $this->autore = $u->username;
                }
            }
            return true;
        }
        return false;
    }
}
