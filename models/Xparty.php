<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Xparty".
 *
 * @property string $cd_party
 * @property string|null $descrizione
 */
class Xparty extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Xparty';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db5');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cd_party'], 'required'],
            [['cd_party'], 'string', 'max' => 50],
            [['descrizione'], 'string', 'max' => 255],
            [['cd_party'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cd_party' => 'Cd Party',
            'descrizione' => 'Descrizione',
        ];
    }
}
