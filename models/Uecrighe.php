<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "uec_righe".
 *
 * @property string $id
 * @property string $id_testa
 * @property string $articolo
 * @property string $descrizione
 * @property int|null $qta
 * @property float|null $prezzo
 */
class Uecrighe extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uec_righe';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_testa'], 'string'],
            [['id_testa', 'articolo', ], 'required'],
            [['qta'], 'integer'],
            [['prezzo'], 'number'],
            [['articolo'], 'string', 'max' => 25],
            [['nota'], 'string', 'max' => 2500],
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
            'id_testa' => 'Id Testa',
            'articolo' => 'Articolo',
            'nota' => 'Nota',
            'qta' => 'Qta',
            'prezzo' => 'Prezzo',
        ];
    }
}
