<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_sottocommessa".
 *
 * @property int $id
 * @property int $id_commessa
 * @property string $codice
 * @property string $descrizione
 * @property string|null $data_inizio
 * @property string|null $data_fine
 * @property int|null $id_anagrafica
 * @property bool $attivo
 * @property string|null $created_at
 *
 * @property MgCommessa $commessa
 * @property MgAnagrafica $anagrafica
 */
class MgSottocommessa extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_sottocommessa';
    }

    public function rules()
    {
        return [
            [['id_commessa', 'codice', 'descrizione'], 'required'],
            [['id_commessa', 'id_anagrafica'], 'integer'],
            [['attivo'], 'boolean'],
            [['created_at'], 'safe'],
            [['codice'], 'string', 'max' => 20],
            [['descrizione'], 'string', 'max' => 200],
            [['data_inizio', 'data_fine'], 'safe'],
            [['data_fine'], 'compare', 'compareAttribute' => 'data_inizio', 'operator' => '>=', 'when' => function ($model) {
                return $model->data_inizio !== null && $model->data_inizio !== ''
                    && $model->data_fine !== null && $model->data_fine !== '';
            }, 'message' => 'La data di fine non può precedere la data di inizio.'],
            [['codice'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_commessa' => 'Commessa',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'data_inizio' => 'Data inizio',
            'data_fine' => 'Data fine',
            'id_anagrafica' => 'Anagrafica',
            'attivo' => 'Attivo',
            'created_at' => 'Creato il',
        ];
    }

    public function getCommessa()
    {
        return $this->hasOne(MgCommessa::className(), ['id' => 'id_commessa']);
    }

    public function getAnagrafica()
    {
        return $this->hasOne(MgAnagrafica::className(), ['id' => 'id_anagrafica']);
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->with('commessa')->orderBy(['codice' => SORT_ASC])->all(),
            'id',
            function ($m) {
                return $m->commessa ? $m->commessa->codice . ' / ' . $m->descrizione : $m->descrizione;
            }
        );
    }

    public static function mapAttivi()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->with('commessa')->where(['attivo' => 1])->orderBy(['codice' => SORT_ASC])->all(),
            'id',
            function ($m) {
                return $m->commessa ? $m->commessa->codice . ' / ' . $m->descrizione : $m->descrizione;
            }
        );
    }

    /**
     * Codice - descrizione (con il codice della commessa padre), per le tendine
     * che salvano il codice (es. rapportini).
     */
    public static function mapCodici()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['codice' => SORT_ASC])->all(),
            'codice',
            function ($m) {
                return $m->codice . ' - ' . $m->descrizione;
            }
        );
    }

    /**
     * Periodo di validità formattato per la lista.
     */
    public function getPeriodoLabel()
    {
        $inizio = $this->data_inizio ? \yii\helpers\Html::encode($this->data_inizio) : '—';
        $fine = $this->data_fine ? \yii\helpers\Html::encode($this->data_fine) : '—';
        return $inizio . ' / ' . $fine;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->id_anagrafica === '') {
                $this->id_anagrafica = null;
            }
            if ($this->created_at === null) {
                $this->created_at = date('Y-m-d H:i:s');
            }
            return true;
        }
        return false;
    }
}
