<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "to_do_rel_tags".
 *
 * @property string $id
 * @property string $id_to_do
 * @property string $tag
 */
class Todoreltags extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'to_do_rel_tags';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_to_do'], 'string'],
            [['id_to_do', 'tag'], 'required'],
            [['tag'], 'string', 'max' => 255],
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
            'id_to_do' => 'Id To Do',
            'tag' => 'Tag',
        ];
    }
}
