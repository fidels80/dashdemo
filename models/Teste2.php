<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Teste2".
 *
 * @property int $id
 * @property string|null $testo
 * @property string|null $testo2
 */
class Teste2 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Teste2';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['testo', 'testo2'], 'string', 'max' => 80],
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
