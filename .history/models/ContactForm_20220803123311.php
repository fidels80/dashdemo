<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class ContactForm extends Model
{
    public $name;
    public $email;
    public $subject;
    public $body;
    public $verifyCode;
    public $files;
  
    
    


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
            ['files','string'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Codice Di Verifica',
            'subject'=>'Oggetto',
            'body'=>'Richiesta',
            'name'=>'Nome'
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether the model passes validation
     */
    public function contact($email,$atcs)
    {
        if ($this->validate()) {
     
     
     
     
     
     
     
     
     
     
     
     
            $message=Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([Yii::$app->params['senderEmail'] => 
                Yii::$app->params['senderName']])
                ->setReplyTo([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($this->body);
foreach ($atc as $value){
           $message->attach($value);
}
                
                
                 $message->send();
   $message=Yii::$app->mailer->compose()
    ->setTo($this->email)
    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
    ->setReplyTo([$this->email => $this->name])
    ->setSubject($this->subject)
    ->setTextBody($this->body);
   //  ->attach($this->files);

foreach ($atc as $value) {
    $message->attach($value);
}


    ->send();

           





            return true;
        }
        return false;
    }
}
