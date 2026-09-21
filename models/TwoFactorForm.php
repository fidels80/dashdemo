<?php

namespace app\models;

use Yii;
use yii\base\Model;

class TwoFactorForm extends Model
{
    public $code;
    public $rememberDevice = false;

    public function rules()
    {
        return [
            [['code'], 'required'],
            [['code'], 'string', 'max' => 6],
            ['code', 'match', 'pattern' => '/^[0-9]{6}$/',
             'message' => 'Il codice deve essere di 6 cifre'],
            [['rememberDevice'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => 'Codice di Verifica',
            'rememberDevice' => 'Ricordami su questo dispositivo',
        ];
    }

    /**
     * Verifica il codice 2FA e completa il login.
     * @return bool
     */
    public function verify()
    {
        if (!$this->validate()) {
            return false;
        }

        $userId = Yii::$app->session->get('pending_2fa_user_id');
        if (!$userId) {
            $this->addError('code', 'Sessione scaduta. Effettua nuovamente il login.');
            return false;
        }

        $user = User::findOne(['id' => $userId, 'status' => User::STATUS_ACTIVE]);
        if (!$user) {
            $this->addError('code', 'Utente non trovato.');
            return false;
        }

        if (!$user->verifyTwoFactorCode($this->code)) {
            $this->addError('code', 'Codice non valido. Riprova.');
            return false;
        }

        $remember = Yii::$app->session->get('pending_2fa_remember', false);
        $duration = $remember ? 3600 * 24 * 30 : 0;

        Yii::$app->session->remove('pending_2fa_user_id');
        Yii::$app->session->remove('pending_2fa_remember');

        if ($this->rememberDevice) {
            $token = UserTrustedDevice::createForUser($user->id);
            if ($token) {
                Yii::$app->response->cookies->add(new \yii\web\Cookie([
                    'name' => 'dash_trusted_device',
                    'value' => $token,
                    'expire' => time() + UserTrustedDevice::TRUST_DURATION,
                    'httpOnly' => true,
                    'secure' => Yii::$app->request->isSecureConnection,
                ]));
            }
        }

        $loginResult = Yii::$app->user->login($user, $duration);

        if ($loginResult === null || $loginResult === false) {
            $this->addError('code', 'Errore durante il login. Riprova.');
            return false;
        }

        $connection = Yii::$app->getDb();
        $connection->createCommand(
            "update [user] set lastlogin=getdate() where id=:id",
            [':id' => $user->id]
        )->execute();

        return true;
    }

    /**
     * Mostra il form di verifica 2FA?
     */
    public static function isPending()
    {
        return Yii::$app->session->has('pending_2fa_user_id');
    }
}
