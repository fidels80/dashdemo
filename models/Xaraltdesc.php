<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "x_araltdesc".
 *
 * @property string $id
 * @property string $cd_ar
 * @property string $descizione
 */
class Xaraltdesc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'x_araltdesc';
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
            [['id'], 'string'],
            [['cd_ar', 'descrizione'], 'required'],
            [['cd_ar'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 200],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cd_ar' => 'Cd Ar',
            'descrizione' => 'Descrizione',
        ];
    }
}
