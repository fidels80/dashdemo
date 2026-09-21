<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mail".
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string|null $Soggetto
 * @property string|null $Corpo
 * @property string|null $allegati
 * @property string|null $data
 * @property int|null $status
 * @property int|null $usr_resp
 * @property int|null $letto
 * @property int|null $id_padre
 */
class Elemail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'email'], 'required'],
            [['nome', 'email', 'Soggetto', 'Corpo', 'allegati'], 'string'],
            [['data'], 'safe'],
            [['status', 'usr_resp', 'letto', 'id_padre'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'Email',
            'Soggetto' => 'Soggetto',
            'Corpo' => 'Corpo',
            'allegati' => 'Allegati',
            'data' => 'Data',
            'status' => 'Status',
            'usr_resp' => 'Usr Resp',
            'letto' => 'Letto',
            'id_padre' => 'Id Padre',
        ];
    }
    
    public function getChild(){
        return $this->hasMany(elemail::className(),['id_padre'=>'id'])->
         orderBy(['id' => SORT_ASC]);
    }
}
