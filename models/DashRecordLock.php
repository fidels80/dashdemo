<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * Blocco temporaneo di un record in modifica da parte di un utente.
 *
 * Una riga per coppia (tabella, id_record). La colonna scadenza indica
 * fino a quando il blocco e' valido: dopo la scadenza il record torna
 * libero e puo' essere acquisito da un altro utente.
 *
 * @property int $id
 * @property string $tabella
 * @property string $id_record
 * @property string $utente
 * @property int|null $utente_id
 * @property string $scadenza
 * @property string $creato_il
 */
class DashRecordLock extends ActiveRecord
{
    public static function tableName()
    {
        return 'dash_record_lock';
    }

    public function rules()
    {
        return [
            [['tabella', 'id_record', 'utente', 'scadenza', 'creato_il'], 'required'],
            [['utente_id'], 'integer'],
            [['scadenza', 'creato_il'], 'safe'],
            [['tabella'], 'string', 'max' => 128],
            [['id_record'], 'string', 'max' => 100],
            [['utente'], 'string', 'max' => 125],
        ];
    }
}
