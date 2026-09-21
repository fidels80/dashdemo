<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tessuto".
 *
 * @property string $tessuto
 * @property string|null $Descrizione
 */
class Tessuto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tessuto';
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
            [['tessuto'], 'required'],
            [['tessuto'], 'string', 'max' => 5],
            [['Descrizione'], 'string', 'max' => 80],
            [['tessuto'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tessuto' => Yii::t('app', 'Tessuto'),
            'Descrizione' => Yii::t('app', 'Descrizione'),
        ];
    }
}
