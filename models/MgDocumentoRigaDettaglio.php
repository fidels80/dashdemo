<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_documento_riga_dettaglio".
 *
 * @property int $id
 * @property int $id_documento_riga
 * @property string|null $seriale
 * @property int|null $id_lotto
 * @property string|null $data_consegna
 * @property float|null $qta
 * @property int|null $ordine
 *
 * @property MgDocumentoRiga $riga
 * @property MgLotto|null $lotto
 */
class MgDocumentoRigaDettaglio extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_documento_riga_dettaglio';
    }

    public function rules()
    {
        return [
            [['id_documento_riga'], 'required'],
            [['id_documento_riga', 'ordine'], 'integer'],
            [['id_lotto'], 'integer'],
            [['id_lotto'], 'exist',
                'targetClass' => MgLotto::className(),
                'targetAttribute' => ['id_lotto' => 'id'],
                'skipOnEmpty' => true],
            [['data_consegna'], 'safe'],
            [['qta'], 'number'],
            [['seriale'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_documento_riga' => 'Riga documento',
            'seriale' => 'Seriale / Matricola',
            'id_lotto' => 'Lotto',
            'data_consegna' => 'Data consegna',
            'qta' => 'Q.tà',
            'ordine' => 'Ordine',
        ];
    }

    public function getRiga()
    {
        return $this->hasOne(MgDocumentoRiga::className(), ['id' => 'id_documento_riga']);
    }

    public function getLotto()
    {
        return $this->hasOne(MgLotto::className(), ['id' => 'id_lotto']);
    }

    public function getDataConsegnaLabel()
    {
        return $this->data_consegna ? date('d/m/Y', strtotime((string) $this->data_consegna)) : null;
    }
}
