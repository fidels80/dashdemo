<?php

namespace app\models;

use yii\helpers\ArrayHelper;

/**
 * Priorità del ticket CRM.
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property string|null $colore
 * @property int $ordine
 * @property bool $attivo
 */
class MgCrmPriorita extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_crm_priorita';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['ordine'], 'integer'],
            [['attivo'], 'boolean'],
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
            'ordine' => 'Ordine',
            'attivo' => 'Attivo',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
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
