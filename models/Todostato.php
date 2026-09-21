<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "to_do_stato".
 *
 * @property int $id
 * @property string|null $stato
 */
class Todostato extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'to_do_stato';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['stato'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stato' => 'Stato',
        ];
    }
}
