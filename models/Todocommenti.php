<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "todo_commenti".
 *
 * @property string $id
 * @property string $id_todo
 * @property string $user
 * @property string $commento
 * @property string $data
 */
class Todocommenti extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'to_do_commenti';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_todo', 'commento', 'id_commento'], 'string'],
            [['id_todo', 'user', 'commento'], 'required'],
            [['data'], 'safe'],
            [['user'], 'string', 'max' => 50],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_todo' => 'Id Todo',
            'user' => 'User',
            'commento' => 'Commento',
            'data' => 'Data',
        ];
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Exclude data from being set during insert
            if ($this->isNewRecord) {
                unset($this->data);
            }
            return true;
        } else {
            return false;
        }
    }
    public function getParentComment()
    {
        return $this->hasOne(self::className(), ['id' => 'id_commento']);
    }

    public function getReplies()
    {
        return $this->hasMany(self::className(), ['id_commento' => 'id']);
    }
}
