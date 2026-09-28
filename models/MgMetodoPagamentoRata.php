<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_metodo_pagamento_rata".
 *
 * @property int $id
 * @property int $id_metodo
 * @property int $progressivo
 * @property int $giorni
 * @property float $percentuale
 *
 * @property MgMetodoPagamento $metodo
 */
class MgMetodoPagamentoRata extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_metodo_pagamento_rata';
    }

    public function rules()
    {
        return [
            [['id_metodo'], 'required'],
            [['id_metodo', 'progressivo', 'giorni'], 'integer'],
            [['percentuale'], 'number'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_metodo' => 'Metodo',
            'progressivo' => 'Rata',
            'giorni' => 'Giorni dalla partenza',
            'percentuale' => '% importo',
        ];
    }

    public function getMetodo()
    {
        return $this->hasOne(MgMetodoPagamento::className(), ['id' => 'id_metodo']);
    }
}
