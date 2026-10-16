<?php

namespace app\models;

use yii\helpers\ArrayHelper;

/**
 * Tipi di richiesta CRM (assistenza, amministrativa, preventivo, ...).
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property string|null $icona
 * @property int $ordine
 * @property bool $attivo
 */
class MgCrmTipo extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_crm_tipo';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['ordine'], 'integer'],
            [['attivo'], 'boolean'],
            [['codice'], 'string', 'max' => 30],
            [['descrizione'], 'string', 'max' => 100],
            [['icona'], 'string', 'max' => 50],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'icona' => 'Icona',
            'ordine' => 'Ordine',
            'attivo' => 'Attivo',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->icona === '') {
                $this->icona = null;
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
