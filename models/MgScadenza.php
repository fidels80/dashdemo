<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_scadenza".
 *
 * @property int $id
 * @property int $id_documento
 * @property int|null $id_metodo_pagamento
 * @property int $progressivo
 * @property string $data_scadenza
 * @property float $percentuale
 * @property float $importo
 * @property string $stato
 * @property string|null $created_at
 *
 * @property MgDocumento $documento
 * @property MgMetodoPagamento $metodo
 */
class MgScadenza extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_scadenza';
    }

    public function rules()
    {
        return [
            [['id_documento', 'data_scadenza'], 'required'],
            [['id_documento', 'id_metodo_pagamento', 'progressivo'], 'integer'],
            [['data_scadenza', 'created_at'], 'safe'],
            [['percentuale', 'importo'], 'number'],
            [['stato'], 'string', 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_documento' => 'Documento',
            'id_metodo_pagamento' => 'Metodo pagamento',
            'progressivo' => 'Rata',
            'data_scadenza' => 'Data scadenza',
            'percentuale' => '%',
            'importo' => 'Importo',
            'stato' => 'Stato',
            'created_at' => 'Creata il',
        ];
    }

    public function getDocumento()
    {
        return $this->hasOne(MgDocumento::className(), ['id' => 'id_documento']);
    }

    public function getMetodo()
    {
        return $this->hasOne(MgMetodoPagamento::className(), ['id' => 'id_metodo_pagamento']);
    }
}
