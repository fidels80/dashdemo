<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "colore".
 *
 * @property string $colore
 * @property string|null $descrizione
 */
class Colore extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'colore';
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
            [['colore'], 'required'],
            [['colore'], 'string', 'max' => 5],
            [['descrizione'], 'string', 'max' => 80],
            [['colore'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'colore' => Yii::t('app', 'Colore'),
            'descrizione' => Yii::t('app', 'Descrizione'),
        ];
    }
}
