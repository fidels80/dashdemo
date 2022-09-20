<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\model\anacli;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $piva;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\User',
             'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['piva', 'required'],
            ['piva', 'string', 'min' => 12],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->piva=$this->piva;
        
        
        $cli= anacli::find
        ->Select(['cd_cli'])
        ->where (['PartitaIva'=>$this->piva])
        ->AsArray()
        ->One();

        if (empty($cli)==false){
        $user->cd_cli= $cli->cd_cli;
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        return $user->save() ? $user : null;
    }
}
