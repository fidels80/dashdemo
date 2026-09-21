<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['grid_color','sidebar_color','gruppo'],'string'],
            // AGGIUNTO L'EMAIL QUI SOTTO:
            [['email'], 'email'],
            [['email'], 'string', 'max' => 255],
            [['reports','moduli','piva','cd_cli','password_reset_token','password_hash',
            'az_grp'],'string'],
            [['username'],'string','max'=>30],
            [ ['file'],'file'],
            ['lastlogin','safe'],
            [['ischief' ,'istourmanager'],'integer'],
            [['cd_agente'],'string','max'=>3],
            //,['rest',integer]
 
   
        ];
    }

    

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by e-mail
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
      //  if (!static::isPasswordResetTokenValid($token)) {
        //    return null;
       // }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    public function getLvl()
    {
        return $this->level();
    }
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
 public function getCli()
    {
        return $this->cd_cli;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = 
        Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


        public function upload() {
        if ($this->validate()) {
           $this->file->saveAs('../web/uploads/'.$this->id.'_'. str_replace(' ', '_',$this->file->baseName) . '.' .
              $this->file->extension);
           return true;
        } else {
           return false;
        }
     }

    /**
     * Ritorna la lista dei soli Tour Manager attivi
     * @return array
     */
    /**
     * Ritorna un array [id => username] dei soli utenti abilitati come Tour Manager
     * @return array
     */
    public static function getTourManagerList()
    {
        return \yii\helpers\ArrayHelper::map(
            static::find()
                ->where([
                    'status' => self::STATUS_ACTIVE,
                    'istourmanager' => 1 // <--- Assicurati che sia scritto correttamente qui
                ])
                ->orderBy('username')
                ->all(),
            'id',
            'username'
        );
    }
    /**
     * Questo evento viene lanciato SUBITO DOPO che Yii2 ha caricato i dati dal Database.
     * Serve per convertire i valori binari sporchi di SQL Server in veri interi (1 o 0)
     * in modo che la checkbox nel Form li legga correttamente.
     */
    public function afterFind()
    {
        parent::afterFind();

        // Forza la conversione del campo ischief
        if ($this->ischief !== null) {
            $this->ischief = (int) $this->ischief;
        }

        // Forza la conversione del campo istourmanager
        if ($this->istourmanager !== null) {
            $this->istourmanager = (int) $this->istourmanager;
        }
    }

    /**
     * Questo evento viene lanciato PRIMA che Yii2 salvi i dati nel Database.
     * Assicuriamoci che i valori siano puliti (0 o 1) per il campo BIT di SQL Server.
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            $this->ischief = $this->ischief ? 1 : 0;
            $this->istourmanager = $this->istourmanager ? 1 : 0;

            return true;
        }
        return false;
    }
}