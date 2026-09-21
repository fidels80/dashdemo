<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "to_do_priorita".
 *
 * @property int $id
 * @property string|null $priorita
 */
class Todopriorita extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'to_do_priorita';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['priorita'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'priorita' => 'Priorita',
        ];
    }
}
