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
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'Utente già'],

            ['password', 'required'],
            ['password', 'string', 'min' => 8],
            ['piva', 'required'],
            ['piva', 'string', 'min' => 8 ,'max'=>13],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */


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
        $user->piva = $this->piva;

        // --- FIX FONDAMENTALE PER SQL SERVER ---
        // Impedisce l'errore sql_variant durante la registrazione
        unset($user->file);

        // Impostiamo un livello di default per l'utente auto-registrato
        $user->level = 70;

        // Prepariamo i campi array vuoti per evitare errori
        $user->moduli = serialize(['Booking']);
        $user->reports = serialize([]);
        $user->cd_cli = serialize([
            'CFEST1 ',
            'CFEST2 ',
            'CFEST3 ',
            'CFEST4 ',
            'CDEMO  ' // 5 lettere + 2 spazi = 7 caratteri
        ]);
        // Invio Email di Benvenuto
        try {
            Yii::$app->mailer->compose(
                ['html' => 'welcome-html', 'text' => 'welcome-text'],
                ['user' => $user]
            )
                ->setFrom([Yii::$app->params['supportEmail'] => 'Planorys DASHBOARD'])
                ->setTo($this->email)
                ->setBcc('gianni.cicalese@tourme.it')
                ->setSubject('Benvenuto nella Planorys DASHBOARD')
                ->send();
        } catch (\Exception $e) {
            // Ignoriamo l'errore mail in questa fase per non bloccare la registrazione,
            // oppure loggiamolo se necessario
            Yii::error("Errore invio mail welcome: " . $e->getMessage());
        }
        if (!$user->save()) {
            echo "<div style='background: white; padding: 20px; border: 2px solid red; z-index: 9999; position: relative;'>";
            echo "<h3>ERRORE DI VALIDAZIONE USER:</h3><pre>";
            print_r($user->getErrors());
            echo "</pre></div>";
            die();
        }

        // Se arriviamo qui, il save() precedente è andato a buon fine!
        return $user;
    }




    public function old_signup()
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
        
       /* 
        $cli= anacli::find()
        ->Select(['cd_cli'])
        ->where (['PartitaIva'=>$this->piva])
        ->AsArray()
        ->One();
        yii::warning($cli);
        if (empty($cli)==false){
        $user->cd_cli= $cli['cd_cli'];
       }
        
        */
        
        
        
        Yii::$app
    ->mailer
    ->compose(
        ['html' => 'welcome-html',
         'text' => 'welcome-text'],
        ['user' => $user]
    )
    ->setFrom([Yii::$app->params['supportEmail'] =>  'Vivenda DASHBOARD'])
    ->setTo($this->email)
    ->setSubject('Benvenuto nel portale ' .'Vivenda DASHBOARD')
    ->send();

        
        
        
        
        
        
        
        
        
        //var_dump($cli);
        
        
        return  $user->save() ? $user : null;
    }
}
