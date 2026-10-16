<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_commessa".
 *
 * @property int $id
 * @property string $codice
 * @property string $descrizione
 * @property string|null $data_inizio
 * @property string|null $data_fine
 * @property int|null $id_anagrafica
 * @property bool $attivo
 * @property string|null $created_at
 *
 * @property MgAnagrafica $anagrafica
 * @property MgSottocommessa[] $sottocommesse
 */
class MgCommessa extends \yii\db\ActiveRecord
{
    use \app\components\EliminazioneProtetta;

    public static function tableName()
    {
        return 'mg_commessa';
    }

    protected function riferimentiBloccanti()
    {
        $rif = [];
        // La commessa e' eliminabile solo se nessuna delle sue sottocommesse
        // e' usata in documenti, righe documento o rapportini.
        $ids = \yii\helpers\ArrayHelper::getColumn(
            MgSottocommessa::find()->select('id')->where(['id_commessa' => $this->id])->asArray()->all(),
            'id'
        );
        if (!empty($ids)) {
            if ($this->esisteRiferimento('mg_documento', ['id_sottocommessa' => $ids])
                || $this->esisteRiferimento('mg_documento_riga', ['id_sottocommessa' => $ids])) {
                $rif[] = 'documenti';
            }
            $codici = \yii\helpers\ArrayHelper::getColumn(
                MgSottocommessa::find()->select('codice')->where(['id' => $ids])->asArray()->all(),
                'codice'
            );
            if (!empty($codici) && $this->esisteRiferimento('rapportini', ['commessa' => $codici])) {
                $rif[] = 'rapportini';
            }
        }
        return $rif;
    }

    public function rules()
    {
        return [
            [['codice', 'descrizione'], 'required'],
            [['id_anagrafica'], 'integer'],
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
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'data_inizio' => 'Data inizio',
            'data_fine' => 'Data fine',
            'id_anagrafica' => 'Anagrafica',
            'attivo' => 'Attivo',
            'created_at' => 'Creato il',
        ];
    }

    public function getAnagrafica()
    {
        return $this->hasOne(MgAnagrafica::className(), ['id' => 'id_anagrafica']);
    }

    public function getSottocommesse()
    {
        return $this->hasMany(MgSottocommessa::className(), ['id_commessa' => 'id'])
            ->orderBy(['codice' => SORT_ASC]);
    }

    public static function map()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->orderBy(['codice' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    public static function mapAttivi()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['attivo' => 1])->orderBy(['codice' => SORT_ASC])->all(),
            'id',
            'descrizione'
        );
    }

    /**
     * Codice - descrizione, per le tendine che salvano il codice (es. rapportini).
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
