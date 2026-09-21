<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "testaj".
 *
 * @property int $id
 * @property string $testo
 * @property string|null $testo2
 */
class Testaj extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'testaj';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['testo'], 'required'],
            [['testo'], 'string', 'max' => 80],
            [['testo2'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'testo' => 'Testo',
            'testo2' => 'Testo2',
        ];
    }
}
