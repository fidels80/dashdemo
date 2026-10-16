<?php

namespace app\models;

use yii\helpers\ArrayHelper;

/**
 * Stati del workflow del ticket CRM.
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property string|null $colore
 * @property bool $is_chiuso
 * @property int $ordine
 * @property bool $attivo
 */
class MgCrmStato extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_crm_stato';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['ordine'], 'integer'],
            [['is_chiuso', 'attivo'], 'boolean'],
            [['codice'], 'string', 'max' => 30],
            [['descrizione'], 'string', 'max' => 100],
            [['colore'], 'string', 'max' => 20],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'colore' => 'Colore',
            'is_chiuso' => 'Stato di chiusura',
            'ordine' => 'Ordine',
            'attivo' => 'Attivo',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->is_chiuso = $this->is_chiuso ? 1 : 0;
            if ($this->colore === '') {
                $this->colore = null;
            }
            return true;
        }
        return false;
    }

    public static function mapAttivi()
    {
        return ArrayHelper::map(
            self::find()->where(['attivo' => 1])->orderBy(['ordine' => SORT_ASC, 'descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }
}
