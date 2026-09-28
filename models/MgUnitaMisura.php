<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_unita_misura".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property bool $attivo
 */
class MgUnitaMisura extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_unita_misura';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['attivo'], 'boolean'],
            [['codice'], 'string', 'max' => 10],
            [['descrizione'], 'string', 'max' => 100],
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
        ];
    }

    public function getEtichetta()
    {
        return $this->codice . ' - ' . $this->descrizione;
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['codice' => SORT_ASC])->all(),
            'id',
            function ($m) {
                return $m->etichetta;
            }
        );
    }

    public static function mapAttivi()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['attivo' => 1])->orderBy(['codice' => SORT_ASC])->all(),
            'id',
            function ($m) {
                return $m->etichetta;
            }
        );
    }

    /**
     * Recupera un'unità di misura per codice, creandola se non esiste.
     * Usata dalla creazione inline (form articoli e form documenti).
     */
    public static function findOrCreate($codice, $descrizione = null)
    {
        $codice = strtoupper(trim((string) $codice));
        if ($codice === '') {
            return null;
        }
        $model = self::findOne(['codice' => $codice]);
        if ($model) {
            return $model;
        }
        $model = new self();
        $model->codice = $codice;
        $model->descrizione = $descrizione ?: $codice;
        $model->attivo = true;
        return $model->save() ? $model : null;
    }
}
