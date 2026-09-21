<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dash_menu_utente".
 *
 * @property int $id
 * @property int $user_id
 * @property int $menu_id
 */
class DashMenuUtente extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'dash_menu_utente';
    }

    public function rules()
    {
        return [
            [['user_id', 'menu_id'], 'required'],
            [['user_id', 'menu_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Utente',
            'menu_id' => 'Voce di menu',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getMenu()
    {
        return $this->hasOne(DashMenu::className(), ['id' => 'menu_id']);
    }

    public static function getMenuIdsForUser($userId)
    {
        return array_map('intval', self::find()
            ->select('menu_id')
            ->where(['user_id' => $userId])
            ->column());
    }
}
