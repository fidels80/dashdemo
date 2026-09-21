<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "api_token".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $descrizione
 * @property string $token_hash
 * @property string|null $scopes
 * @property int|null $expires_at
 * @property int $created_at
 * @property int|null $last_used_at
 */
class ApiToken extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'api_token';
    }

    public function rules()
    {
        return [
            [['token_hash', 'created_at'], 'required'],
            [['user_id', 'expires_at', 'created_at', 'last_used_at'], 'integer'],
            [['token_hash'], 'string', 'max' => 64],
            [['descrizione'], 'string', 'max' => 200],
            [['scopes'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Utente',
            'descrizione' => 'Descrizione',
            'token_hash' => 'Token',
            'scopes' => 'Permessi',
            'expires_at' => 'Scadenza',
            'created_at' => 'Creato il',
            'last_used_at' => 'Ultimo utilizzo',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public static function hashToken($token)
    {
        return hash('sha256', $token);
    }

    /**
     * Crea un nuovo token e restituisce il valore in chiaro (mostrato una sola volta).
     */
    public static function generate($descrizione, $userId = null, $expiresAt = null, $scopes = '*')
    {
        $plain = bin2hex(Yii::$app->security->generateRandomKey(32));

        $model = new self();
        $model->user_id = $userId;
        $model->descrizione = $descrizione;
        $model->token_hash = self::hashToken($plain);
        $model->scopes = $scopes;
        $model->expires_at = $expiresAt;
        $model->created_at = time();

        if (!$model->save()) {
            return null;
        }

        return $plain;
    }

    /**
     * Trova un token valido (non scaduto) a partire dal valore in chiaro.
     */
    public static function findValid($plainToken)
    {
        if (empty($plainToken)) {
            return null;
        }

        $model = self::find()->where(['token_hash' => self::hashToken($plainToken)])->one();
        if (!$model) {
            return null;
        }

        if ($model->expires_at !== null && $model->expires_at < time()) {
            return null;
        }

        return $model;
    }

    public function markUsed()
    {
        if ($this->isNewRecord) {
            return false;
        }
        $this->last_used_at = time();
        return $this->updateAttributes(['last_used_at']);
    }
}
