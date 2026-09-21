<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "to_do_gruppi".
 *
 * @property int $id
 * @property string|null $gruppo
 */
class Todogruppi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'to_do_gruppi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gruppo'], 'string', 'max' => 80],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gruppo' => 'Gruppo',
        ];
    }
}
