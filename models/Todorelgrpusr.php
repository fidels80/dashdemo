<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "to_do_rel_grp_usr".
 *
 * @property int $id_gruppo
 * @property int $id_user
 * @property int $id
 */
class Todorelgrpusr extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'to_do_rel_grp_usr';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_gruppo', 'id_user'], 'required'],
            [['id_gruppo', 'id_user'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_gruppo' => 'Id Gruppo',
            'id_user' => 'Id User',
            'id' => 'ID',
        ];
    }
}
