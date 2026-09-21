<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Modello per il dispositivo fidato 2FA.
 *
 * Ogni record rappresenta un dispositivo (browser) autorizzato a saltare
 * la verifica 2FA per un periodo di tempo limitato (scadenza: expires_at).
 *
 * @property integer $id
 * @property integer $user_id
 * @property string  $token_hash
 * @property integer $expires_at
 * @property string  $user_agent
 * @property integer $created_at
 * @property integer $last_used_at
 */
class UserTrustedDevice extends ActiveRecord
{
    // Durata di validita' di un dispositivo fidato (30 giorni)
    const TRUST_DURATION = 2592000; // 60 * 60 * 24 * 30

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_trusted_device}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'token_hash', 'expires_at', 'created_at'], 'required'],
            [['user_id', 'expires_at', 'created_at', 'last_used_at'], 'integer'],
            [['token_hash'], 'string', 'max' => 64],
            [['user_agent'], 'string', 'max' => 500],
        ];
    }

    /**
     * Genera un token casuale sicuro per il dispositivo fidato.
     */
    public static function generateToken()
    {
        return bin2hex(Yii::$app->security->generateRandomKey(32));
    }

    /**
     * Genera l'hash del token da salvare nel DB.
     */
    public static function hashToken($token)
    {
        return hash('sha256', $token);
    }

    /**
     * Crea e salva un nuovo dispositivo fidato per l'utente.
     * Restituisce il token grezzo (da mettere nel cookie).
     */
    public static function createForUser($userId)
    {
        $token = self::generateToken();

        $model = new self();
        $model->user_id = $userId;
        $model->token_hash = self::hashToken($token);
        $model->expires_at = time() + self::TRUST_DURATION;
        $model->user_agent = substr((string) Yii::$app->request->userAgent, 0, 500);
        $model->created_at = time();
        $model->last_used_at = time();

        if (!$model->save()) {
            return null;
        }

        return $token;
    }

    /**
     * Verifica un token e restituisce il record se valido e non scaduto.
     */
    public static function findValidByToken($token, $userId)
    {
        if (empty($token)) {
            return null;
        }

        /** @var self $model */
        $model = self::find()
            ->where(['user_id' => $userId, 'token_hash' => self::hashToken($token)])
            ->one();

        if (!$model) {
            return null;
        }

        // Rimuove i record scaduti e restituisce null
        if ($model->expires_at < time()) {
            $model->delete();
            return null;
        }

        return $model;
    }

    /**
     * Aggiorna il timestamp dell'ultimo utilizzo.
     */
    public function markUsed()
    {
        $this->last_used_at = time();

        if ($this->isNewRecord) {
            return false;
        }

        $this->updateAttributes(['last_used_at']);
        return true;
    }

    /**
     * Elimina tutti i dispositivi fidati di un utente (revoca).
     */
    public static function revokeAllForUser($userId)
    {
        return self::deleteAll(['user_id' => $userId]);
    }

    /**
     * Elimina i dispositivi fidati scaduti (pulizia).
     */
    public static function deleteExpired()
    {
        return self::deleteAll(['<', 'expires_at', time()]);
    }
}
