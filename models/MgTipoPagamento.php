<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_tipo_pagamento".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property bool $attivo
 * @property string|null $fe_condizioni_pagamento
 */
class MgTipoPagamento extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_tipo_pagamento';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['attivo'], 'boolean'],
            [['codice'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 100],
            [['fe_condizioni_pagamento'], 'string', 'max' => 4],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'attivo' => 'Attivo',
            'fe_condizioni_pagamento' => 'Condizioni pagamento (SDI)',
        ];
    }

    /**
     * Etichetta delle condizioni di pagamento SDI.
     */
    public function getFeCondizioniPagamentoLabel()
    {
        $opzioni = \app\components\FatturaElettronica::opzioniCondizioniPagamento();
        return $opzioni[$this->fe_condizioni_pagamento] ?? $this->fe_condizioni_pagamento;
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public static function mapAttivi()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['attivo' => 1])->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public function getMetodi()
    {
        return $this->hasMany(MgMetodoPagamento::className(), ['id_tipo_pagamento' => 'id']);
    }
}
