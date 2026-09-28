<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_attributo_articolo".
 *
 * Attributi variante dell'articolo (tabella unica discriminata da "tipo"):
 * marche, modelli, taglie e colori.
 *
 * @property int $id
 * @property string $tipo
 * @property string|null $codice
 * @property string $descrizione
 * @property bool $attivo
 */
class MgAttributoArticolo extends \yii\db\ActiveRecord
{
    const TIPO_MARCA = 'marca';
    const TIPO_MODELLO = 'modello';
    const TIPO_TESSUTO = 'tessuto';
    const TIPO_TAGLIA = 'taglia';
    const TIPO_COLORE = 'colore';

    public static function tableName()
    {
        return 'mg_attributo_articolo';
    }

    public function rules()
    {
        return [
            [['tipo', 'descrizione'], 'required'],
            [['attivo'], 'boolean'],
            [['tipo'], 'string', 'max' => 20],
            [['codice'], 'string', 'max' => 30],
            [['descrizione'], 'string', 'max' => 100],
            [['tipo'], 'in', 'range' => array_keys(self::opzioniTipo())],
            [['tipo', 'descrizione'], 'unique', 'targetAttribute' => ['tipo', 'descrizione'],
                'message' => 'Esiste già un attributo di questo tipo con questa descrizione.'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo',
            'codice' => 'Codice',
            'descrizione' => 'Descrizione',
            'attivo' => 'Attivo',
        ];
    }

    public static function opzioniTipo()
    {
        return [
            self::TIPO_MARCA => 'Marca',
            self::TIPO_MODELLO => 'Modello',
            self::TIPO_TESSUTO => 'Tessuto',
            self::TIPO_TAGLIA => 'Taglia',
            self::TIPO_COLORE => 'Colore',
        ];
    }

    public function getTipoLabel()
    {
        $opzioni = self::opzioniTipo();
        return $opzioni[$this->tipo] ?? $this->tipo;
    }

    public function getEtichetta()
    {
        return empty($this->codice) ? $this->descrizione : $this->codice . ' - ' . $this->descrizione;
    }

    /**
     * Mappa id => etichetta per un tipo (marca/modello/taglia/colore).
     */
    public static function mapByTipo($tipo, $soloAttivi = true)
    {
        $query = self::find()->where(['tipo' => $tipo]);
        if ($soloAttivi) {
            $query->andWhere(['attivo' => 1]);
        }
        return \yii\helpers\ArrayHelper::map(
            $query->orderBy(['descrizione' => SORT_ASC])->all(),
            'id',
            function ($m) {
                return $m->etichetta;
            }
        );
    }

    /**
     * Recupera un attributo per tipo+descrizione, creandolo se non esiste.
     * Usata dalla creazione inline.
     */
    public static function findOrCreate($tipo, $descrizione, $codice = null)
    {
        $descrizione = trim((string) $descrizione);
        if ($descrizione === '' || !array_key_exists($tipo, self::opzioniTipo())) {
            return null;
        }
        $model = self::findOne(['tipo' => $tipo, 'descrizione' => $descrizione]);
        if ($model) {
            return $model;
        }
        $model = new self();
        $model->tipo = $tipo;
        $model->descrizione = $descrizione;
        $model->codice = $codice ?: strtoupper(substr($descrizione, 0, 3));
        $model->attivo = true;
        return $model->save() ? $model : null;
    }
}
