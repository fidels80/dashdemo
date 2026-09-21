<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ai_conversation".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property AiMessage[] $aiMessages
 */
class AiConversation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ai_conversation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            [['user_id'], 'required'], // Obbligatorio
            [['user_id'], 'integer'],
        ];
    }

    /**
     * Gets query for [[AiMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAiMessages()
    {
        return $this->hasMany(AiMessage::className(), ['conversation_id' => 'id']);
    }
    // Aggiungi questo metodo per filtrare automaticamente le chat per l'utente loggato
    public static function findMine()
    {
        return static::find()->where(['user_id' => Yii::$app->user->id]);
    }
}
