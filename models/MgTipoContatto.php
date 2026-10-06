<?php

namespace app\models;

/**
 * This is the model class for table "mg_tipo_contatto".
 *
 * Tipi di contatto disponibili per le anagrafiche (email, PEC, cellulare, ...).
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property string|null $icona
 * @property int $ordine
 * @property bool $attivo
 *
 * @property MgAnagraficaContatto[] $contatti
 */
class MgTipoContatto extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_tipo_contatto';
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

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['ordine' => SORT_ASC, 'descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public static function mapAttivi()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['attivo' => 1])->orderBy(['ordine' => SORT_ASC, 'descrizione' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public function getContatti()
    {
        return $this->hasMany(MgAnagraficaContatto::className(), ['id_tipo_contatto' => 'id']);
    }
}
