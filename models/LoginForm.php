<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $email; //
    public $password;
    public $rememberMe = true;
    public $twoFactorCode;
    public $pendingUserId;

    private $_user = false;
    private $_loginState = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required (solo al primo step)
            [['email', 'password'], 'required', 'when' => function ($model) {
                return empty($model->pendingUserId);
            }],
            [['email'], 'email', 'when' => function ($model) {
                return empty($model->pendingUserId);
            }], //
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword', 'when' => function ($model) {
                return empty($model->pendingUserId);
            }],
            [['twoFactorCode'], 'string', 'max' => 6],
            ['twoFactorCode', 'match', 'pattern' => '/^[0-9]{6}$/',
             'message' => 'Il codice deve essere di 6 cifre',
             'when' => function ($model) {
                return !empty($model->pendingUserId);
            }],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * Restituisce true/false oppure la stringa '2fa' quando e' richiesta
     * la verifica a due fattori.
     * @return bool|string whether the user is logged in successfully
     */
    public function login()
    {
        if (!$this->validate()) {
            Yii::$app->session->setFlash('Login fallita');
            return false;
        }

        $user = $this->getUser();

        if ($user && $user->isTwoFactorEnabled()) {
            // Se il dispositivo e' fidato (cookie valido e non scaduto),
            // salta la verifica 2FA ed effettua il login diretto.
            $trustedToken = Yii::$app->request->cookies->getValue('dash_trusted_device');
            if ($trustedToken) {
                $trustedDevice = UserTrustedDevice::findValidByToken($trustedToken, $user->id);
                if ($trustedDevice) {
                    $trustedDevice->markUsed();
                    $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;
                    $result = Yii::$app->user->login($user, $duration);
                    if ($result) {
                        $this->updateLastLogin($user->id);
                    }
                    return $result;
                }
            }

            $this->pendingUserId = $user->id;
            $this->_loginState = '2fa';
            return '2fa';
        }

        $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;
        $result = Yii::$app->user->login($user, $duration);
        if ($result) {
            $this->updateLastLogin($user->id);
        }
        return $result;
    }

    /**
     * Aggiorna la data dell'ultimo accesso.
     */
    private function updateLastLogin($id)
    {
        Yii::$app->getDb()->createCommand(
            "update [user] set lastlogin=getdate() where id=:id",
            [':id' => $id]
        )->execute();
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByEmail($this->email); //
        }

        return $this->_user;
    }

    public function getIsTwoFactorPending()
    {
        return !empty($this->pendingUserId);
    }
}
