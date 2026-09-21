<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uec_testa".
 *
 * @property string $id
 * @property string $data
 * @property int $numero
 * @property int $cliente
 * @property bool|null $esportato
 * @property string|null $tipopag
 */
class Uectesta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uec_testa';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'string'],
            [['data', 'numero', 'cliente'], 'required'],
            [['data'], 'safe'],
            [['numero', 'cliente'], 'integer'],
            [['esportato'], 'boolean'],
            [['tipopag'], 'string', 'max' => 25],
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
            'data' => 'Data',
            'numero' => 'Numero',
            'cliente' => 'Cliente',
            'esportato' => 'Esportato',
            'tipopag' => 'Tipopag',
        ];
    }
    public function getRighe()
    {
        return $this->hasMany(Uecrighe::class, ['id_testa' => 'id']);
    }
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->data = date('Y-m-d');
                $this->numero = Uectesta::find()->max('numero') + 1;
            }
            return true;
        }
        return false;
    }

    public function getUecRighe()
    {
        return $this->hasMany(Uecrighe::className(), ['id_testa' => 'id']);
    }
}
