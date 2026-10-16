<?php

namespace app\models;

/**
 * Associazione N:N utente esterno <-> anagrafica (mg_anagrafica).
 * Un'azienda puo' avere piu' utenti; un utente puo' vedere piu' aziende.
 *
 * @property int $id
 * @property int $user_id
 * @property int $id_anagrafica
 * @property string|null $created_at
 *
 * @property MgAnagrafica $anagrafica
 * @property User $utente
 */
class MgCrmUtenteAnagrafica extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_crm_utente_anagrafica';
    }

    public function rules()
    {
        return [
            [['user_id', 'id_anagrafica'], 'required'],
            [['user_id', 'id_anagrafica'], 'integer'],
            [['created_at'], 'safe'],
            [['user_id', 'id_anagrafica'], 'unique', 'targetAttribute' => ['user_id', 'id_anagrafica'],
                'message' => 'Questa anagrafica è già collegata all\'utente.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Utente',
            'id_anagrafica' => 'Anagrafica',
            'created_at' => 'Creato il',
        ];
    }

    public function getAnagrafica()
    {
        return $this->hasOne(MgAnagrafica::className(), ['id' => 'id_anagrafica']);
    }

    public function getUtente()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->created_at)) {
                $this->created_at = new \yii\db\Expression('GETDATE()');
            }
            return true;
        }
        return false;
    }

    /**
     * Id delle anagrafiche collegate a un utente.
     *
     * @param int $userId
     * @return int[]
     */
    public static function idsAnagraficheDiUtente($userId)
    {
        return array_map('intval', self::find()
            ->select('id_anagrafica')
            ->where(['user_id' => (int) $userId])
            ->column());
    }

    /**
     * Aziende collegate a un utente esterno.
     *
     * @param int $userId
     * @return MgAnagrafica[]
     */
    public static function anagraficheDiUtente($userId)
    {
        return MgAnagrafica::find()
            ->innerJoin('mg_crm_utente_anagrafica ua', 'ua.id_anagrafica = mg_anagrafica.id')
            ->where(['ua.user_id' => (int) $userId])
            ->orderBy(['mg_anagrafica.ragione_sociale' => SORT_ASC])
            ->all();
    }

    /**
     * Crea il collegamento se non esiste gia'.
     *
     * @param int $userId
     * @param int $idAnagrafica
     * @return MgCrmUtenteAnagrafica
     */
    public static function findOrCreate($userId, $idAnagrafica)
    {
        $link = self::findOne(['user_id' => (int) $userId, 'id_anagrafica' => (int) $idAnagrafica]);
        if ($link !== null) {
            return $link;
        }
        $link = new self();
        $link->user_id = (int) $userId;
        $link->id_anagrafica = (int) $idAnagrafica;
        $link->save(false);
        return $link;
    }

    /**
     * Sostituisce in blocco le anagrafiche collegate a un utente.
     *
     * @param int $userId
     * @param int[] $idsAnagrafiche
     */
    public static function sincronizza($userId, array $idsAnagrafiche)
    {
        $userId = (int) $userId;
        $ids = array_values(array_unique(array_map('intval', $idsAnagrafiche)));

        self::deleteAll(['user_id' => $userId]);
        foreach ($ids as $idAnagrafica) {
            $link = new self();
            $link->user_id = $userId;
            $link->id_anagrafica = $idAnagrafica;
            $link->save(false);
        }
    }
}
