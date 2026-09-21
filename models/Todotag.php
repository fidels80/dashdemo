<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "to_do_tag".
 *
 * @property string $id
 * @property string|null $Tag
 */
class Todotag extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'to_do_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'string'],
            [['Tag'], 'string', 'max' => 250],
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
            'Tag' => 'Tag',
        ];
    }
}
