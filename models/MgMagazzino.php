<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_magazzino".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property int|null $id_anagrafica
 * @property bool $attivo
 *
 * @property MgAnagrafica|null $anagrafica
 */
class MgMagazzino extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_magazzino';
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['id_anagrafica'], 'integer'],
            [['attivo'], 'boolean'],
            [['codice'], 'string', 'max' => 10],
            [['descrizione'], 'string', 'max' => 100],
            [['id_anagrafica'], 'exist', 'targetClass' => MgAnagrafica::className(), 'targetAttribute' => ['id_anagrafica' => 'id']],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'id_anagrafica' => 'Anagrafica',
            'attivo' => 'Attivo',
        ];
    }

    public function getAnagrafica()
    {
        return $this->hasOne(MgAnagrafica::className(), ['id' => 'id_anagrafica']);
    }

    public function getEtichetta()
    {
        return $this->codice . ' - ' . $this->descrizione;
    }

    public function getAnagraficaLabel()
    {
        return $this->anagrafica ? $this->anagrafica->ragione_sociale : null;
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
}
