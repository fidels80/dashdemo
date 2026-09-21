<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ai_knowledge".
 *
 * @property int $id
 * @property string $software_name
 * @property string|null $section_title
 * @property string $content
 * @property string|null $category
 * @property int|null $created_at
 */
class Aiknowledge extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ai_knowledge';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['software_name', 'content'], 'required'],
            [['content'], 'string'],
            [['created_at'], 'integer'],
            [['software_name', 'section_title', 'category'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'software_name' => 'Software Name',
            'section_title' => 'Section Title',
            'content' => 'Content',
            'category' => 'Category',
            'created_at' => 'Created At',
        ];
    }
}
