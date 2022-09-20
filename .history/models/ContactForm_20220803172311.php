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
            ['files','file' ,'maxFiles' => 10],
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

        //die(print_r($this));
        if ($this->validate()) {
     
     
     
     
     
     
     
     
     
     
     
     
            $message=Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom([Yii::$app->params['senderEmail'] => 
                Yii::$app->params['senderName']])
                ->setReplyTo([$this->email => $this->name])
                ->setSubject($this->subject)
                ->setTextBody($this->body);
foreach ($atcs as $value){
   $path = Yii::getAlias('@webroot') . '/uploads/mail/';

   
    
$value->saveAs(
    // $t=$t.
    ($path . $value->baseName . '.' . $value->extension));
$message->attach((($path . $value->baseName . '.' . $value->extension)));

}
                
                
                 $message->send();
   $message=Yii::$app->mailer->compose()
    ->setTo($this->email)
    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
    ->setReplyTo([$this->email => $this->name])
    ->setSubject($this->subject)
    ->setTextBody($this->body);
   //  ->attach($this->files);

foreach ($atcs as $value) {
    $path = Yii::getAlias('@webroot') . '/uploads/mail/';

    $value->saveAs(
        // $t=$t.
        ($path . $value->baseName . '.' . $value->extension));
    $message->attach((($path . $value->baseName . '.' . $value->extension)));

}


   $message ->send();

           
die(print_r($message));




            return true;
        }
         die('non validato');

        return false;
    }
}
