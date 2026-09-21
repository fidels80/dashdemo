<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gac_risorse".
 *
 * @property string|null $id
 * @property string|null $descrizione
 */
class GacRisorse extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gac_risorse';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'descrizione'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descrizione' => 'Descrizione',
        ];
    }
}
