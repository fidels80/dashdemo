<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "taglia".
 *
 * @property string $taglia
 * @property string|null $Descrizione
 */
class Taglia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'taglia';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db3');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['taglia'], 'required'],
            [['taglia'], 'string', 'max' => 5],
            [['Descrizione'], 'string', 'max' => 50],
            [['taglia'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'taglia' => Yii::t('app', 'Taglia'),
            'Descrizione' => Yii::t('app', 'Descrizione'),
        ];
    }
}
