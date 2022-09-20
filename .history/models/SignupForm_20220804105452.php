<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\anacli;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $piva;
    public $strength;
 const WEAK = 0;
const STRONG = 1;


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
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Utente già registrato con questa email'],

            ['password', 'required'],
            ['password', 'passwordStrength', 'strength'=>self::STRONG],
            ['piva', 'required'],
            ['piva', 'string', 'min' => 8 ,'max'=>13],
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
        
        
        $cli= anacli::find()
        ->Select(['cd_cli'])
        ->where (['PartitaIva'=>$this->piva])
        ->AsArray()
        ->One();
        yii::warning($cli);
        if (empty($cli)==false){
        $user->cd_cli= $cli['cd_cli'];
       }
        
        
        
        
        
        Yii::$app
    ->mailer
    ->compose(
        ['html' => 'welcome-html',
         'text' => 'welcome-text'],
        ['user' => $user]
    )
    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
    ->setTo($this->email)
    ->setSubject('Benvenuto nel portale ' . Yii::$app->name)
    ->send();

        
        
        
        
        
        
        
        
        
        //var_dump($cli);
        
        
        return  $user->save() ? $user : null;
    }


    public function passwordStrength($attribute,$params)
{
    if ($params['strength'] === self::WEAK)
        $pattern = '/^(?=.*[a-zA-Z0-9]).{5,}$/';  
    elseif ($params['strength'] === self::STRONG)
        $pattern = '/^(?=.*\d(?=.*\d))(?=.*[a-zA-Z](?=.*[a-zA-Z])).{5,}$/';  

    if(!preg_match($pattern, $this->$attribute))
      $this->addError($attribute, 'your password is not strong enough!');
}
}
