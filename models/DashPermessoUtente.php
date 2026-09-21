<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dash_permesso_utente".
 *
 * @property int $id
 * @property int $user_id
 * @property int $permesso_id
 * @property bool $can_view
 * @property bool $can_create
 * @property bool $can_update
 * @property bool $can_delete
 */
class DashPermessoUtente extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dash_permesso_utente';
    }

    public function rules()
    {
        return [
            [['user_id', 'permesso_id'], 'required'],
            [['user_id', 'permesso_id'], 'integer'],
            [['can_view', 'can_create', 'can_update', 'can_delete'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Utente',
            'permesso_id' => 'Risorsa',
            'can_view' => 'Vista',
            'can_create' => 'Crea',
            'can_update' => 'Modifica',
            'can_delete' => 'Elimina',
        ];
    }

    public function getPermesso()
    {
        return $this->hasOne(DashPermesso::className(), ['id' => 'permesso_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Restituisce le assegnazioni di un utente indicizzate per permesso_id.
     */
    public static function getByUser($userId)
    {
        $rows = self::find()->where(['user_id' => $userId])->all();
        $out = [];
        foreach ($rows as $r) {
            $out[(int) $r->permesso_id] = $r;
        }
        return $out;
    }
}
