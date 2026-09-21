<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "xruoli".
 *
 * @property string $cd_ruolo
 * @property string|null $descrizione
 */
class Xruoli extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xruoli';
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
            [['cd_ruolo'], 'required'],
            [['cd_ruolo'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 200],
            [['cd_ruolo'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cd_ruolo' => 'Cd Ruolo',
            'descrizione' => 'Descrizione',
        ];
    }
}
