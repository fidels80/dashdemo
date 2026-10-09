<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mg_matricola".
 *
 * @property int $id
 * @property int|null $id_articolo
 * @property string|null $codice_articolo
 * @property string $matricola
 * @property string|null $descrizione
 * @property string|null $nota
 * @property bool $attivo
 * @property string|null $created_at
 *
 * @property MgArticolo|null $articolo
 */
class MgMatricola extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'mg_matricola';
    }

    public function rules()
    {
        return [
            [['matricola'], 'required'],
            [['id_articolo'], 'integer'],
            [['id_articolo'], 'exist',
                'targetClass' => MgArticolo::className(),
                'targetAttribute' => ['id_articolo' => 'id'],
                'skipOnEmpty' => true],
            [['attivo'], 'boolean'],
            [['created_at'], 'safe'],
            [['codice_articolo'], 'string', 'max' => 25],
            [['matricola'], 'string', 'max' => 100],
            [['descrizione'], 'string', 'max' => 200],
            [['nota'], 'string', 'max' => 500],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_articolo' => 'Articolo',
            'codice_articolo' => 'Codice articolo',
            'matricola' => 'Matricola / Seriale',
            'descrizione' => 'Descrizione',
            'nota' => 'Nota',
            'attivo' => 'Attivo',
            'created_at' => 'Creato il',
        ];
    }

    public function getArticolo()
    {
        return $this->hasOne(MgArticolo::className(), ['id' => 'id_articolo']);
    }

    public function getArticoloLabel()
    {
        return $this->articolo ? $this->articolo->codice . ' - ' . $this->articolo->descrizione : $this->codice_articolo;
    }

    public function getEtichetta()
    {
        $label = $this->matricola;
        if (!empty($this->descrizione)) {
            $label .= ' - ' . $this->descrizione;
        }
        return $label;
    }

    /**
     * Matricole di un articolo (per id e/o codice articolo).
     *
     * @param int|null $idArticolo
     * @param string|null $codiceArticolo
     * @return MgMatricola[]
     */
    public static function perArticolo($idArticolo, $codiceArticolo = null)
    {
        $query = self::find();
        $conditions = [];
        if (!empty($idArticolo)) {
            $conditions[] = ['id_articolo' => (int) $idArticolo];
        }
        if ($codiceArticolo !== null && $codiceArticolo !== '') {
            $conditions[] = ['codice_articolo' => $codiceArticolo];
        }

        if (empty($conditions)) {
            return [];
        }

        $query->andWhere(array_merge(['or'], $conditions));
        return $query->orderBy(['matricola' => SORT_ASC])->all();
    }

    public static function mapAttivi()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['attivo' => 1])->orderBy(['matricola' => SORT_ASC])->all(),
            'id',
            function ($m) {
                return $m->etichetta;
            }
        );
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->id_articolo === '') {
                $this->id_articolo = null;
            }
            if ($this->id_articolo && empty($this->codice_articolo)) {
                $articolo = $this->articolo;
                if ($articolo) {
                    $this->codice_articolo = $articolo->codice;
                }
            }
            if ($insert && empty($this->created_at)) {
                $this->created_at = new \yii\db\Expression('GETDATE()');
            }
            return true;
        }
        return false;
    }
}
