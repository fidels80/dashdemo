<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stack_post_tags".
 *
 * @property string|null $somma
 * @property string|null $tagname
 * @property int $id
 */
class Stackposttags extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stack_post_tags';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['somma', 'tagname'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'somma' => 'Somma',
            'tagname' => 'Tagname',
            'id' => 'ID',
        ];
    }
}
