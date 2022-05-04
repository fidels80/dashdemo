<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "pippo".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $DESK
 */
class Pippo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pippo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['code', 'DESK'], 'string', 'max' => 10],
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
            'code' => 'Code',
            'DESK' => 'Desk',
        ];
    }
}
