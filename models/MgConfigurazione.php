<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_configurazione".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property string|null $valore
 */
class MgConfigurazione extends \yii\db\ActiveRecord
{
    /**
     * Cache in memoria delle configurazioni, per evitare query ripetute.
     *
     * @var array|null
     */
    private static $cache;

    public static function tableName()
    {
        return 'mg_configurazione';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['codice'], 'string', 'max' => 100],
            [['descrizione'], 'string', 'max' => 255],
            [['valore'], 'string', 'max' => 500],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'valore' => 'Valore',
        ];
    }

    /**
     * Restituisce il valore di una configurazione dato il codice.
     *
     * @param string $codice
     * @param string|null $default
     * @return string|null
     */
    public static function valore($codice, $default = null)
    {
        if (self::$cache === null) {
            self::$cache = \yii\helpers\ArrayHelper::map(
                self::find()->select(['codice', 'valore'])->all(),
                'codice',
                'valore'
            );
        }

        if (!array_key_exists($codice, self::$cache) || self::$cache[$codice] === null) {
            return $default;
        }

        return self::$cache[$codice];
    }

    /**
     * Scrive (o aggiorna) un valore di configurazione.
     */
    public static function imposta($codice, $valore, $descrizione = null)
    {
        $model = self::findOne(['codice' => $codice]);
        if ($model === null) {
            $model = new self();
            $model->codice = $codice;
            $model->descrizione = $descrizione !== null ? $descrizione : $codice;
        }
        $model->valore = $valore;
        $model->save(false);
        self::$cache = null;

        return $model;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        self::$cache = null;
    }

    public function afterDelete()
    {
        parent::afterDelete();
        self::$cache = null;
    }
}
