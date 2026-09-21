<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ai_message".
 *
 * @property int $id
 * @property int|null $conversation_id
 * @property string|null $role
 * @property string|null $content
 * @property string|null $created_at
 *
 * @property AiConversation $conversation
 */
class Aimessage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ai_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['conversation_id'], 'integer'],
            [['content'], 'string'],
            [['created_at'], 'safe'],
            [['role'], 'string', 'max' => 20],
            [['conversation_id'], 'exist', 'skipOnError' => true, 'targetClass' => AiConversation::className(), 'targetAttribute' => ['conversation_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'conversation_id' => 'Conversation ID',
            'role' => 'Role',
            'content' => 'Content',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Conversation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConversation()
    {
        return $this->hasOne(AiConversation::className(), ['id' => 'conversation_id']);
    }
}
